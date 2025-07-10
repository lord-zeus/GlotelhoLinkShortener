<?php
namespace App\Traits;

use App\Http\Requests\Url\StoreRequest;
use App\Models\Url;
use Illuminate\Support\Facades\Auth;

trait UrlTrait
{
    public function shortenUrl(StoreRequest $request)
    {
        $shortCode = $request->custom_code ?? $this->generateUniqueShortCode();

        $shortUrl = Url::create([
            'original_url' => $request->original_url,
            'short_code' => $shortCode,
            'expires_at' => $request->expires_at,
            'user_id' => Auth::id()
        ]);

        return response()->json([
            'short_url' => url("/{$shortCode}"),
            'original_url' => $shortUrl->original_url,
            'short_code' => $shortCode
        ], 201);

    }
}
