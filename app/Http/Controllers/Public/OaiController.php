<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Journal;
use App\Models\Submission;
use Carbon\Carbon;

class OaiController extends Controller
{
    public function handle(Request $request)
    {
        try {
            // 1. Resolve Journal (From Route Parameter)
            $journalPath = $request->route('journal');
            $journal = Journal::where('slug', $journalPath)
                ->orWhere('path', $journalPath)
                ->firstOrFail();
            
            // 1.2. Cek OAI Enabled
            if (!$journal->enable_oai) {
                return $this->oaiError('noRecordsMatch', 'OAI-PMH is not enabled for this journal.');
            }
            
            // 1.5 Validasi Verb (dan Landing Page)
            if (!$request->has('verb')) {
                if (empty($request->query())) {
                    return view('journal.oai.landing');
                }
                return $this->oaiError('badVerb', 'Missing OAI verb');
            }

            $verb = $request->input('verb');
            $validVerbs = ['Identify', 'ListRecords', 'ListSets', 'ListMetadataFormats', 'ListIdentifiers', 'GetRecord'];
            if (!in_array($verb, $validVerbs)) {
                return $this->oaiError('badVerb', 'Illegal OAI verb');
            }
            
            // Check for illegal parameters
            $allowedKeys = ['verb', 'identifier', 'metadataPrefix', 'from', 'until', 'set', 'resumptionToken'];
            foreach ($request->query() as $key => $value) {
                if (!in_array($key, $allowedKeys)) {
                    return $this->oaiError('badArgument', 'Illegal parameter: ' . $key);
                }
            }

        // 4. Cek Resumption Token (Eksklusivitas)
        if ($request->has('resumptionToken')) {
            // Count arguments. verb + resumptionToken = 2.
            $paramCount = count($request->query()); 
            if ($paramCount > 2) { 
                return $this->oaiError('badArgument', 'resumptionToken is an exclusive argument');
            }
            return $this->oaiError('badResumptionToken', 'Invalid resumptionToken');
        }

        // 5. Validasi MetadataPrefix
        if (in_array($verb, ['ListRecords', 'ListIdentifiers', 'GetRecord'])) {
            if (!$request->has('metadataPrefix')) {
                return $this->oaiError('badArgument', 'Missing metadataPrefix');
            }
            if ($request->input('metadataPrefix') !== 'oai_dc') {
                return $this->oaiError('cannotDisseminateFormat', 'Only oai_dc is supported');
            }
        }

       // 6. Validasi Tanggal (from & until)
        if ($request->has('from') || $request->has('until')) {
            $from = $request->input('from');
            $until = $request->input('until');

            // PERBAIKAN: Cek Granularity Mismatch
            // Jika panjang string beda jauh (misal 10 karakter vs 20 karakter), return error
            if ($from && $until && abs(strlen($from) - strlen($until)) > 5) {
                return $this->oaiError('badArgument', 'Granularity mismatch between from and until');
            }

            try {
                if ($from) Carbon::parse($from);
                if ($until) Carbon::parse($until);
            } catch (\Exception $e) {
                return $this->oaiError('badArgument', 'Invalid date format');
            }
        }

        // 7. Validasi Identifier
        if (in_array($verb, ['GetRecord', 'ListMetadataFormats'])) {
            if ($verb === 'GetRecord' && !$request->has('identifier')) {
                return $this->oaiError('badArgument', 'Missing identifier');
            }
        }

        // ===============================================
        // DISPATCHER
        // ===============================================
        switch ($verb) {
            case 'Identify':
                $earliestDate = Submission::where('journal_id', $journal->id)
                    ->where('status', Submission::STATUS_PUBLISHED)
                    ->min('updated_at'); 
                
                $earliestDate = $earliestDate ? Carbon::parse($earliestDate) : now();

                $xmlContent = view('journal.public.oai.identify', [
                    'journal' => $journal,
                    'earliestDate' => $earliestDate->setTimezone('UTC')->format('Y-m-d\TH:i:s\Z'),
                    'baseUrl' => route('journal.oai', $journal->slug)
                ])->render();

                return \Illuminate\Support\Facades\Response::make($xmlContent, 200, [
                    'Content-Type' => 'text/xml'
                ]);

            case 'ListMetadataFormats':
                if ($request->has('identifier')) {
                     $id = $this->extractId($request->input('identifier'));
                     if (!$id) {
                         return $this->oaiError('idDoesNotExist', 'Invalid identifier format');
                     }
                     $exists = Submission::where('journal_id', $journal->id)
                        ->where(function($q) use ($id) {
                            if (is_numeric($id)) {
                                $q->where('seq_id', $id);
                            } elseif (\Illuminate\Support\Str::isUuid($id)) {
                                $q->where('id', $id);
                            } else {
                                $q->where('slug', $id);
                            }
                        })->exists();

                     if (!$exists) {
                         return $this->oaiError('idDoesNotExist', 'The specified identifier does not exist');
                     }
                }
                return response()->view('journal.public.oai.metadata_formats', compact('journal'))
                    ->header('Content-Type', 'text/xml');

            case 'ListSets':
                 $sets = [
                    (object)['spec' => strtoupper($journal->abbreviation ?? 'JRN'), 'name' => $journal->name],
                    (object)['spec' => strtoupper($journal->abbreviation ?? 'JRN') . ':ART', 'name' => 'Articles']
                ];
                return response()->view('journal.public.oai.list_sets', compact('journal', 'sets'))
                    ->header('Content-Type', 'text/xml');

            case 'ListIdentifiers':
            case 'ListRecords':
                $query = Submission::where('journal_id', $journal->id)
                    ->where('status', Submission::STATUS_PUBLISHED);
                
                // Filter Tanggal (OAI-PMH 2.0 Inklusif)
                if ($request->has('from')) {
                    $from = Carbon::parse($request->input('from'))->utc();
                    $query->where('updated_at', '>=', $from);
                }
                if ($request->has('until')) {
                    $until = Carbon::parse($request->input('until'))->utc()->endOfSecond();
                    $query->where('updated_at', '<=', $until);
                }

                // Filter Set (Jika ada parameter set, harus sesuai abbreviation jurnal)
                if ($request->has('set')) {
                    $set = $request->input('set');
                    $journalAbbr = strtoupper($journal->abbreviation ?? 'JRN');
                    // Dukung format JCO atau JCO:ART
                    if ($set !== $journalAbbr && $set !== $journalAbbr . ':ART') {
                         // Jika set tidak cocok, paksa query mengembalikan hasil kosong
                         $query->whereRaw('1 = 0');
                    }
                }

                // Metadata Eager Load
                $query->with(['currentPublication', 'authors', 'keywords', 'issue', 'journal', 'galleys']);

                $records = $query->orderBy('updated_at', 'desc')->take(100)->get();

                if ($records->isEmpty()) {
                    return $this->oaiError('noRecordsMatch', 'No records found');
                }

                $view = ($verb === 'ListIdentifiers') ? 'journal.public.oai.list_identifiers' : 'journal.public.oai.list_records';
                return response()->view($view, compact('records', 'journal', 'verb'))
                    ->header('Content-Type', 'text/xml; charset=UTF-8');

            case 'GetRecord':
                $id = $this->extractId($request->input('identifier'));
                if (!$id) {
                     return $this->oaiError('idDoesNotExist', 'Invalid identifier format');
                }

                $recordRaw = Submission::where('journal_id', $journal->id)
                    ->where(function($q) use ($id) {
                        if (is_numeric($id)) {
                            $q->where('seq_id', $id);
                        } elseif (\Illuminate\Support\Str::isUuid($id)) {
                            $q->where('id', $id);
                        } else {
                            $q->where('slug', $id);
                        }
                    })
                    ->where('status', Submission::STATUS_PUBLISHED)
                    ->with(['currentPublication', 'authors', 'keywords', 'issue', 'journal', 'galleys'])
                    ->first();

                if (!$recordRaw) {
                    return $this->oaiError('idDoesNotExist', 'Identifier not found');
                }
                
                $record = $recordRaw;

                return response()->view('journal.public.oai.get_record', compact('record', 'journal'))
                    ->header('Content-Type', 'text/xml; charset=UTF-8');
        }
        
        } catch (\Exception $e) {
            return $this->oaiError('badArgument', 'Invalid OAI-PMH request: ' . $e->getMessage());
        }
    }

    // --- HELPER METHODS ---

    private function extractId($oaiString) {
        if (!$oaiString) return null;
        $parts = explode('/', $oaiString);
        return end($parts);
    }

    public static function getRequestAttributes()
    {
        $allowedKeys = ['verb', 'identifier', 'metadataPrefix', 'from', 'until', 'set', 'resumptionToken'];
        $requestAttributes = '';
        foreach (request()->only($allowedKeys) as $key => $value) {
            if ($value !== null && $value !== '') {
                $requestAttributes .= ' ' . $key . '="' . htmlspecialchars($value, ENT_QUOTES, 'UTF-8') . '"';
            }
        }
        return $requestAttributes;
    }

   private function oaiError($code, $message)
    {
        $baseUrl = url()->current(); 
        $requestAttributes = self::getRequestAttributes();

        // XML String Manual
        $xml = '<?xml version="1.0" encoding="UTF-8"?>
        <OAI-PMH xmlns="http://www.openarchives.org/OAI/2.0/" 
                 xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                 xsi:schemaLocation="http://www.openarchives.org/OAI/2.0/ http://www.openarchives.org/OAI/2.0/OAI-PMH.xsd">
            <responseDate>' . now()->setTimezone('UTC')->format('Y-m-d\TH:i:s\Z') . '</responseDate>
            <request' . $requestAttributes . '>' . htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8') . '</request>
            <error code="' . $code . '">' . htmlspecialchars($message, ENT_QUOTES, 'UTF-8') . '</error>
        </OAI-PMH>';

        return response($xml, 200)->header('Content-Type', 'text/xml; charset=UTF-8');
    }
}
