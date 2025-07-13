<?php

namespace App\Http\Controllers;

use App\Http\Requests\Url\StoreRequest;
use App\Models\Url;
use App\Traits\UrlTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;

class UrlController extends Controller
{
    use UrlTrait;
    public function create()
    {
        return Inertia::render('Urls/Create');
    }

    public function store(StoreRequest $request)
    {
        $this->shortenUrl($request);

        return back()->with([
            'message' => 'URL created successfully!',
        ]);
    }

    public function redirect($shortCode)
    {
        $shortUrl = Url::where('short_code', $shortCode)->firstOrFail();

        if ($shortUrl->isExpired()) {
            abort(410, 'This link has expired');
        }

        $shortUrl->increment('count');
        $shortUrl->clicks()->create([
            'country' => request()->header('X-Country'),
            'city' => request()->header('X-City'),
            'browser' => request()->header('X-Browser'),
            'os' => request()->header('X-OS'),
            'device' => request()->header('X-Device'),
            'visitor_ip' => request()->ip(),
            'referer' => request()->header('referer')
        ]);

        return redirect()->away($shortUrl->original_url, 301);
    }

    public function userUrls(Request $request)
    {
        $page = $request->query('page', 1); // Get page number from query parameter, default to 1

        $urls = $request->user()->urls()
            ->latest()
            ->paginate(1, ['*'], 'page', $page);

        return Inertia::render('Dashboard', [
            'urls' => $urls
        ]);
    }


    public function show(Request $request, $id)
    {
        $url = Url::with('user')->findOrFail($id);

        // Basic stats
        $totalClicks = $url->count;

        // Clicks by day for the last 30 days with proper date handling
        $startDate = Carbon::now()->subDays(30)->startOfDay();
        $endDate = Carbon::now()->endOfDay();

        $clicksByDay = DB::table('clicks')
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as clicks')
            )
            ->where('url_id', $url->id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        // Generate complete date range with filled data
        $dates = collect();
        $currentDate = $startDate->copy();

        while ($currentDate <= $endDate) {
            $formattedDate = $currentDate->format('M j');
            $dateKey = $currentDate->format('Y-m-d');

            $dates->push([
                'date' => $formattedDate,
                'clicks' => $clicksByDay->has($dateKey) ? (int)$clicksByDay[$dateKey]->clicks : 0
            ]);

            $currentDate->addDay();
        }

        // Top referrers (using proper column name from schema)
        $topReferrers = DB::table('clicks')
            ->select(
                DB::raw('COALESCE(referer, "Direct") as referrer'),
                DB::raw('COUNT(*) as count')
            )
            ->where('url_id', $url->id)
            ->groupBy('referer')
            ->orderByDesc('count')
            ->limit(5)
            ->get();

        // Devices breakdown
        $devices = DB::table('clicks')
            ->select(
                DB::raw('COALESCE(device, "Unknown") as device'),
                DB::raw('COUNT(*) as count')
            )
            ->where('url_id', $url->id)
            ->groupBy('device')
            ->get();

        // Browsers breakdown
        $browsers = DB::table('clicks')
            ->select(
                DB::raw('COALESCE(browser, "Unknown") as browser'),
                DB::raw('COUNT(*) as count')
            )
            ->where('url_id', $url->id)
            ->groupBy('browser')
            ->get();

        // Geographical data
        $topCountries = DB::table('clicks')
            ->select(
                DB::raw('COALESCE(country, "Unknown") as country'),
                DB::raw('COUNT(*) as count')
            )
            ->where('url_id', $url->id)
            ->groupBy('country')
            ->orderByDesc('count')
            ->limit(5)
            ->get();

//        return $url;

        return response()->json([
            'url' => [
                'short_code' => $url->short_code,
                'original_url' => $url->original_url,
                'created_at' => $url->created_at->format('M j, Y'),
                'expires_at' => Carbon::make( $url->expires_at),
                'user' => $url->user ? [
                    'id' => $url->user->id,
                    'name' => $url->user->name
                ] : null,
                'total_clicks' => $totalClicks,
            ],
            'clicks_by_day' => $dates,
            'top_referrers' => $topReferrers,
            'devices' => $devices,
            'browsers' => $browsers,
            'top_countries' => $topCountries,
            'stats_period' => [
                'start' => $startDate->format('Y-m-d'),
                'end' => $endDate->format('Y-m-d')
            ]
        ]);
    }

    private function generateUniqueShortCode($length = 6)
    {
        do {
            $code = Str::random($length);
        } while (Url::where('short_code', $code)->exists());

        return $code;
    }
}
