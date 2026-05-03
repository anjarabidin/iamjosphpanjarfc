<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\Models\Event;
use App\Models\Member;
use App\Models\Article;
use App\Models\Contact;
use App\Models\HomeAds;
use App\Models\ContactUs;
use App\Models\MediaCorner;
use App\Models\MeetingRoom;
use App\Models\HotelBooking;
use App\Models\MeetingVenue;
use App\Models\HonoraryCouncil;
use Illuminate\Support\Facades\DB;
use App\Models\RegionalCoordinator;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function index()
    {
        $isPasswordSafe = $this->isPasswordSafe(Auth::user()->password);

        return view('admins.dashboard.index', compact(
            'isPasswordSafe'
        ));
    }

    /**
     * Hitung jumlah row di rentang waktu, optional filter kolom boolean aktif, dan optional scope (closure).
     *
     * @param class-string<\Illuminate\Database\Eloquent\Model> $modelClass
     * @param Carbon $start
     * @param Carbon $end
     * @param string|null $activeColumn
     * @param callable(\Illuminate\Database\Eloquent\Builder):void|null $scope
     */
    private function countInRange(
        string $modelClass,
        Carbon $start,
        Carbon $end,
        ?string $activeColumn = null,
        ?callable $scope = null
    ): int {
        /** @var \Illuminate\Database\Eloquent\Builder $q */
        $q = $modelClass::query()->whereBetween('created_at', [$start, $end]);

        if ($activeColumn) {
            $q->where($activeColumn, true);
        }
        if ($scope) {
            $scope($q); // terapkan whereHas / filter tambahan di sini
        }

        return (int) $q->count();
    }

    private function pctDelta(int $thisPeriod, int $lastPeriod): array
    {
        if ($lastPeriod > 0) {
            $delta = (($thisPeriod - $lastPeriod) / $lastPeriod) * 100;
        } else {
            $delta = $thisPeriod > 0 ? 100 : 0;
        }
        return [
            'value'    => $delta,
            'positive' => $delta >= 0,
            'label'    => ($delta >= 0 ? '+' : '') . number_format($delta, 1) . '%',
        ];
    }

    public function isPasswordSafe($password)
    {
        $datasets = ['12345678', '12345', '123', 'bismillah', 'admin123', 'admin', 'qwerty', 'password', 'welcome', '123abc', '123qwe', 'iloveyou', 'abc123', '123456789', '1234567', '1234', '123456', 'master', '696969', 'mustang', 'batman', 'anjing', 'sayang', 'cinta', 'kucing', 'indonesia', 'ganteng', 'cantik', '1234567890', 'qazwsx', '987654321', '1q2w3e4r', '123123', '555555'];

        foreach ($datasets as $dataset) {
            if (Hash::check($dataset, $password)) {
                return false;
            }
        }
        return true;
    }
}
