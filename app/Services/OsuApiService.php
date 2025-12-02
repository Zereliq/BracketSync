<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OsuApiService
{
    private string $clientId;

    private string $clientSecret;

    private ?string $accessToken = null;

    public function __construct()
    {
        $this->clientId = config('services.osu.client_id');
        $this->clientSecret = config('services.osu.client_secret');
    }

    /**
     * Get osu! API access token using client credentials.
     */
    private function getAccessToken(): ?string
    {
        if ($this->accessToken) {
            return $this->accessToken;
        }

        try {
            $http = Http::asForm();

            // Disable SSL verification for local development if configured
            if (config('app.curl_verify_ssl') === false) {
                $http = $http->withOptions(['verify' => false]);
            }

            $response = $http->post('https://osu.ppy.sh/oauth/token', [
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
                'grant_type' => 'client_credentials',
                'scope' => 'public',
            ]);

            if ($response->successful()) {
                $this->accessToken = $response->json('access_token');

                return $this->accessToken;
            }
        } catch (\Exception $e) {
            Log::error('Failed to get osu! API token: '.$e->getMessage());
        }

        return null;
    }

    /**
     * Fetch beatmap data from osu! API by beatmap ID.
     */
    public function getBeatmap(int $beatmapId): ?array
    {
        $token = $this->getAccessToken();

        if (! $token) {
            return null;
        }

        try {
            $http = Http::withToken($token);

            // Disable SSL verification for local development if configured
            if (config('app.curl_verify_ssl') === false) {
                $http = $http->withOptions(['verify' => false]);
            }

            $response = $http->get("https://osu.ppy.sh/api/v2/beatmaps/{$beatmapId}");

            if ($response->successful()) {
                $data = $response->json();

                return [
                    'osu_beatmap_id' => $data['id'],
                    'osu_beatmapset_id' => $data['beatmapset_id'] ?? null,
                    'artist' => $data['beatmapset']['artist'] ?? 'Unknown Artist',
                    'title' => $data['beatmapset']['title'] ?? 'Unknown Title',
                    'version' => $data['version'] ?? 'Unknown',
                    'mode' => $this->convertMode($data['mode'] ?? 'osu'),
                    'star_rating' => $data['difficulty_rating'] ?? null,
                    'length_seconds' => $data['total_length'] ?? null,
                    'mapper' => $data['beatmapset']['creator'] ?? null,
                    'beatmap_url' => "https://osu.ppy.sh/beatmaps/{$beatmapId}",
                    'cover_url' => $data['beatmapset']['covers']['cover'] ?? null,
                    'bpm' => $data['bpm'] ?? null,
                    'cs' => $data['cs'] ?? null,
                    'ar' => $data['ar'] ?? null,
                    'od' => $data['accuracy'] ?? null,
                    'hp' => $data['drain'] ?? null,
                ];
            }
        } catch (\Exception $e) {
            Log::error("Failed to fetch beatmap {$beatmapId}: ".$e->getMessage());
        }

        return null;
    }

    /**
     * Convert osu! API mode string to our format.
     */
    private function convertMode(string $mode): string
    {
        return match ($mode) {
            'osu' => 'standard',
            'taiko' => 'drums',
            'fruits' => 'fruit',
            'mania' => 'piano',
            default => 'standard',
        };
    }
}
