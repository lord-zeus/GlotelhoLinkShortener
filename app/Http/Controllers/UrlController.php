<?php

namespace App\Http\Controllers;

use App\Http\Requests\Url\StoreRequest;
use App\Models\Url;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class UrlController extends Controller
{
    public function index()
    {
        return 'OK';
    }

    public function store(StoreRequest $request)
    {
        $shortCode = $request->custom_code ?? $this->generateUniqueShortCode();

        $shortUrl = Url::create([
            'original_url' => $request->url,
            'short_code' => $shortCode,
            'expires_at' => $request->expires_at,
            'user_id' => $request->user()?->id
        ]);

        return response()->json([
            'short_url' => url("/$shortCode"),
            'original_url' => $shortUrl->original_url,
            'short_code' => $shortCode,
            'expires_at' => $shortUrl->expires_at,
            // Bonus: QR code generation
        ], 201);
    }

    public function redirect($shortCode)
    {
        $shortUrl = Url::where('short_code', $shortCode)->firstOrFail();

        if ($shortUrl->isExpired()) {
            abort(410, 'This link has expired');
        }

        $shortUrl->increment('clicks');
        $shortUrl->clicks()->create([
            'country' => request()->header('X-Country'),
            'city' => request()->header('X-City'),
            'latitude' => request()->header('X-Latitude'),
            'longitude' => request()->header('X-Longitude'),
            'browser' => request()->header('X-Browser'),
            'os' => request()->header('X-OS'),
            'device' => request()->header('X-Device'),
            'visitor_ip' => request()->ip(),
//            'user_agent' => request()->userAgent(),
            'referer' => request()->header('referer')
        ]);

        return redirect()->away($shortUrl->original_url, 301);
    }

    public function userUrls(Request $request)
    {
        $urls = $request->user()->urls()
            ->latest()
            ->paginate(10);

        return response()->json($urls);
    }

    private function generateUniqueShortCode($length = 6): string
    {
        do {
            $code = Str::random($length);
        } while (Url::where('short_code', $code)->exists());

        return $code;
    }
}
