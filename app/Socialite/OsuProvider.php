<?php

namespace App\Socialite;

use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\ProviderInterface;
use Laravel\Socialite\Two\User;

class OsuProvider extends AbstractProvider implements ProviderInterface
{
    protected $scopes = ['identify'];

    protected $scopeSeparator = ' ';

    protected function getHttpClient(): \GuzzleHttp\Client
    {
        if (is_null($this->httpClient)) {
            $options = [
                'timeout' => 30,
                'connect_timeout' => 30,
            ];

            if (config('app.env') === 'local' || config('app.env') === 'development') {
                $options['verify'] = false;
            }

            $this->httpClient = new \GuzzleHttp\Client($options);
        }

        return $this->httpClient;
    }

    protected function getAuthUrl($state): string
    {
        return $this->buildAuthUrlFromBase('https://osu.ppy.sh/oauth/authorize', $state);
    }

    protected function getTokenUrl(): string
    {
        return 'https://osu.ppy.sh/oauth/token';
    }

    protected function getUserByToken($token): array
    {
        $response = $this->getHttpClient()->get('https://osu.ppy.sh/api/v2/me', [
            'headers' => [
                'Authorization' => 'Bearer '.$token,
            ],
        ]);

        return json_decode($response->getBody(), true);
    }

    protected function mapUserToObject(array $user): User
    {
        return (new User)->setRaw($user)->map([
            'id' => $user['id'],
            'nickname' => $user['username'] ?? null,
            'name' => $user['username'] ?? null,
            'email' => $user['email'] ?? null,
            'avatar' => $user['avatar_url'] ?? null,
            'country_code' => $user['country_code'] ?? null,
            'mode' => $user['playmode'] ?? null,
        ]);
    }

    public function getAccessTokenResponse($code): array
    {
        $response = $this->getHttpClient()->post($this->getTokenUrl(), [
            'headers' => [
                'Accept' => 'application/json',
            ],
            'form_params' => $this->getTokenFields($code),
        ]);

        return json_decode($response->getBody(), true);
    }

    protected function getTokenFields($code): array
    {
        return [
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'code' => $code,
            'grant_type' => 'authorization_code',
            'redirect_uri' => $this->redirectUrl,
        ];
    }
}
