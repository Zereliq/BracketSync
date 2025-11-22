<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\SiteRole;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;

class OsuLoginController extends Controller
{
    public function redirectToProvider(): RedirectResponse
    {
        try {
            return Socialite::driver('osu')->redirect();
        } catch (\Exception $e) {
            Log::error('osu! OAuth redirect failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect('/')->with('error', 'Unable to connect to osu! authentication service. Please try again later.');
        }
    }

    public function handleProviderCallback(): RedirectResponse
    {
        try {
            $osuUser = Socialite::driver('osu')->user();
        } catch (\Laravel\Socialite\Two\InvalidStateException $e) {
            Log::warning('osu! OAuth invalid state', [
                'error' => $e->getMessage(),
            ]);

            return redirect('/')->with('error', 'Authentication failed. Please try again.');
        } catch (\Exception $e) {
            Log::error('osu! OAuth callback failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect('/')->with('error', 'Authentication failed. Please try again later.');
        }

        if (! $osuUser || ! isset($osuUser->id)) {
            Log::error('osu! OAuth returned invalid user data', [
                'user_data' => $osuUser,
            ]);

            return redirect('/')->with('error', 'Failed to retrieve user information from osu!');
        }

        try {
            $user = $this->findOrCreateUser($osuUser);

            Auth::login($user, true);

            return redirect('/')->with('success', 'Welcome back, '.$user->name.'!');
        } catch (\Exception $e) {
            Log::error('Failed to create or login user', [
                'osu_id' => $osuUser->id ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect('/')->with('error', 'Failed to complete login. Please try again.');
        }
    }

    protected function findOrCreateUser($osuUser): User
    {
        // Make sure a default "player" role exists (created once, reused forever)
        $defaultRole = SiteRole::firstOrCreate(['name' => 'player']);

        $user = User::where('osu_id', $osuUser->id)->first();

        $tokenExpiresAt = null;
        if (isset($osuUser->expiresIn)) {
            $tokenExpiresAt = Carbon::now()->addSeconds($osuUser->expiresIn);
        }

        // OsuProvider::mapUserToObject maps:
        // id, nickname, name, email, avatar, country_code, mode
        $userData = [
            'osu_id' => $osuUser->id,
            'name' => $osuUser->name ?? $osuUser->nickname ?? 'Player',
            'osu_username' => $osuUser->nickname ?? $osuUser->name ?? null,
            'avatar_url' => $osuUser->avatar ?? null,
            'country_code' => $osuUser->country_code ?? null,
            'mode' => $osuUser->mode ?? null,
            'email' => $osuUser->email ?? null,
            'osu_access_token' => $osuUser->token ?? null,
            'osu_refresh_token' => $osuUser->refreshToken ?? null,
            'osu_token_expires_at' => $tokenExpiresAt,
        ];

        // Only update gamemode stats if they haven't been updated in the last 2 weeks
        $shouldUpdateStats = ! $user || ! $user->gamemode_stats_updated_at || $user->gamemode_stats_updated_at->lt(Carbon::now()->subWeeks(2));

        if ($shouldUpdateStats) {
            // Extract gamemode statistics from statistics_rulesets
            $statisticsRulesets = $osuUser->user['statistics_rulesets'] ?? [];

            foreach ($statisticsRulesets as $mode => $stats) {

                if ($mode === 'osu') {
                    $userData['osu_rank'] = $stats['global_rank'] ?? null;
                    $userData['osu_pp'] = $stats['pp'] ?? null;
                    $userData['osu_hit_accuracy'] = $stats['hit_accuracy'] ?? null;
                } elseif ($mode === 'taiko') {
                    $userData['taiko_rank'] = $stats['global_rank'] ?? null;
                    $userData['taiko_pp'] = $stats['pp'] ?? null;
                    $userData['taiko_hit_accuracy'] = $stats['hit_accuracy'] ?? null;
                } elseif ($mode === 'fruits') {
                    $userData['fruits_rank'] = $stats['global_rank'] ?? null;
                    $userData['fruits_pp'] = $stats['pp'] ?? null;
                    $userData['fruits_hit_accuracy'] = $stats['hit_accuracy'] ?? null;
                } elseif ($mode === 'mania') {
                    $userData['mania_rank'] = $stats['global_rank'] ?? null;
                    $userData['mania_pp'] = $stats['pp'] ?? null;
                    $userData['mania_hit_accuracy'] = $stats['hit_accuracy'] ?? null;
                }
            }

            // Update the timestamp
            $userData['gamemode_stats_updated_at'] = Carbon::now();
        }

        if ($user) {
            // Update existing user data
            $user->update($userData);

            // Ensure existing user also has a siterole
            if (! $user->siterole_id) {
                $user->siterole_id = $defaultRole->id;
                $user->save();
            }
        } else {
            // New user: assign default role & base elo
            $userData['siterole_id'] = $defaultRole->id;
            $userData['elo'] = 0;

            $user = User::create($userData);
        }

        return $user;
    }
}
