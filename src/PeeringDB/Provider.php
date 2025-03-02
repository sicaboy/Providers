<?php

namespace SocialiteProviders\PeeringDB;

use GuzzleHttp\RequestOptions;
use SocialiteProviders\Manager\OAuth2\AbstractProvider;
use SocialiteProviders\Manager\OAuth2\User;

class Provider extends AbstractProvider
{
    public const IDENTIFIER = 'PEERINGDB';

    /**
     * Scopes defintions.
     *
     * @see https://developer.okta.com/docs/reference/api/oidc/#scopes
     */
    public const SCOPE_PROFILE = 'profile';

    public const SCOPE_EMAIL = 'email';

    public const SCOPE_NETWORKS = 'networks';

    protected $scopes = ['profile email networks'];

    protected function getAuthUrl($state): string
    {
        return $this->buildAuthUrlFromBase('https://auth.peeringdb.com/oauth2/authorize', $state);
    }

    protected function getTokenUrl(): string
    {
        return 'https://auth.peeringdb.com/oauth2/token/';
    }

    /**
     * {@inheritdoc}
     */
    protected function getUserByToken($token)
    {
        $response = $this->getHttpClient()->get('https://auth.peeringdb.com/profile/v1', [
            RequestOptions::HEADERS => [
                'Authorization' => 'Bearer '.$token,
            ],
        ]);

        return json_decode((string) $response->getBody(), true);
    }

    /**
     * {@inheritdoc}
     */
    protected function mapUserToObject(array $user)
    {
        return (new User)->setRaw($user)->map([
            'id'    => $user['id'],
            'name'  => $user['name'],
            'email' => $user['email'],
        ]);
    }
}
