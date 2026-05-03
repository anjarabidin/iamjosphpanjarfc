<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\Submission;
use App\Models\Keyword;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Step 1: Migrate existing keywords data
        $submissions = Submission::whereNotNull('keywords')
            ->where('keywords', '!=', '')
            ->get();
        
        foreach ($submissions as $submission) {
            // Split keywords by comma
            $keywordStrings = array_map('trim', explode(',', $submission->keywords));
            $keywordStrings = array_filter($keywordStrings); // Remove empty values
            
            $keywordIds = [];
            
            foreach ($keywordStrings as $content) {
                if (empty($content)) {
                    continue;
                }
                
                // Find or create keyword
                $keyword = Keyword::firstOrCreate(
                    ['content' => $content],
                    ['id' => (string) \Illuminate\Support\Str::uuid()]
                );
                
                $keywordIds[] = $keyword->id;
            }
            
            // Attach keywords to submission
            if (!empty($keywordIds)) {
                $submission->keywords()->sync($keywordIds);
            }
        }
        
        // Step 2: Drop the old keywords column from submissions table
        Schema::table('submissions', function (Blueprint $table) {
            $table->dropColumn('keywords');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Step 1: Re-add the keywords column
        Schema::table('submissions', function (Blueprint $table) {
            $table->text('keywords')->nullable()->after('abstract');
        });
        
        // Step 2: Migrate data back to string format
        $submissions = Submission::with('keywords')->get();
        
        foreach ($submissions as $submission) {
            if ($submission->keywords->isNotEmpty()) {
                $keywordString = $submission->keywords->pluck('content')->implode(', ');
                DB::table('submissions')
                    ->where('id', $submission->id)
                    ->update(['keywords' => $keywordString]);
            }
        }
    }
};
