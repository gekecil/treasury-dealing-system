<?php

return [
    'oauthconf' => [ // See http://oauth2-client.thephpleague.com/usage/#authorization-code-flow
        'clientId' => env('OAUTH2_CLIENT_ID', ''), // The client ID assigned to you by the provider
        'clientSecret' => '', // The client password assigned to you by the provider
        'redirectUri' => env('APP_URL', '').'/oauth2/callback',
        'urlAuthorize' => env('OAUTH2_URL_AUTHORIZE', ''),
        'urlAccessToken' => env('OAUTH2_URL_ACCESS_TOKEN', ''),
        'urlResourceOwnerDetails' => env('OAUTH2_URL_RESOURCE_OWNER_DETAILS', ''),
    ],
    'provider' => \Kronthto\LaravelOAuth2Login\OAuthProvider::class,

    'oauth_redirect_path' => '/oauth2/callback',

    'session_key' => 'oauth2_session',
    'session_key_state' => 'oauth2_auth_state',
    'session_owner' => 'user',

    'resource_owner_attribute' => 'oauth2_user',
    'auth_driver_key' => 'oauth2',
    'authWrapperFactory' => App\OAuth2\OAuthUserWrapper::class, // Can be used to specify a factory with an __invoke (ResourceOwnerInterface passed as arg1) method to build a custom User object
    'authWrapper' => \Kronthto\LaravelOAuth2Login\OAuthUserWrapper::class, // Ignored if authWrapperFactory is not null

    'cacheUserDetailsFor' => 30, // minutes
    'cacheUserPrefix' => 'oauth_user_',
];
