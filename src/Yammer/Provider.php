<?php

namespace SocialiteProviders\Yammer;

use GuzzleHttp\RequestOptions;
use Illuminate\Support\Arr;
use SocialiteProviders\Manager\OAuth2\AbstractProvider;
use SocialiteProviders\Manager\OAuth2\User;

class Provider extends AbstractProvider
{
    public const IDENTIFIER = 'YAMMER';

    protected function getAuthUrl($state): string
    {
        return $this->buildAuthUrlFromBase('https://www.yammer.com/dialog/oauth', $state);
    }

    protected function getTokenUrl(): string
    {
        return 'https://www.yammer.com/oauth2/access_token.json';
    }

    /**
     * {@inheritdoc}
     */
    protected function getUserByToken($token)
    {
        $response = $this->getHttpClient()->get(
            'https://www.yammer.com/api/v1/users/current.json',
            [
                RequestOptions::HEADERS => [
                    'Authorization' => 'Bearer '.$token,
                ],
            ]
        );

        return json_decode((string) $response->getBody(), true);
    }

    /**
     * {@inheritdoc}
     */
    protected function mapUserToObject(array $user)
    {
        return (new User)->setRaw($user)->map([
            'id'     => $user['id'], 'nickname' => $user['name'],
            'name'   => $user['full_name'], 'email' => $user['email'],
            'avatar' => $user['mugshot_url'],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function parseAccessToken($body)
    {
        return Arr::get($body, 'access_token.token');
    }
}
