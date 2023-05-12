<?php
namespace SiASN\Sdk\Authentication;

use SiASN\Sdk\Credentials\Credentials;
use SiASN\Sdk\Core\Cache;
use GuzzleHttp\Client as HttpClient;

final class Client
{
    const WS_AUTH_PRODUCTION = "https://apimws.bkn.go.id/oauth2/token";
    const WS_AUTH_TRAINING   = "https://training-apimws.bkn.go.id/oauth2/token";

    const SSO_AUTH_PRODUCTION = "https://sso-siasn.bkn.go.id/auth/realms/public-siasn/protocol/openid-connect/token";
    const SSO_AUTH_TRAINING   = "https://iam-siasn.bkn.go.id/auth/realms/public-siasn/protocol/openid-connect/token";

    private $credentials;
    private $cache;

    public function __construct(array $credentials = [])
    {
        $this->credentials = new Credentials($credentials);
        $this->cache       = new Cache();
    }

    public function getAccess()
    {
        $this->signInWs();
        $this->signInSso();
    }

    public function getWsAccessToken()
    {
        if ($this->cache->isNotExpired($this->cacheTokenWsName()) && $this->cache->isCached($this->cacheTokenWsName())) {
            return $this->cache->retrieve($this->cacheTokenWsName());
        }
    }

    public function getSsoAccessToken()
    {
        if ($this->cache->isNotExpired($this->cacheTokenSsoName()) && $this->cache->isCached($this->cacheTokenSsoName())) {
            return $this->cache->retrieve($this->cacheTokenSsoName());
        }
    }

    private function signInWs()
    {
        if ($this->cache->isNotExpired($this->cacheTokenWsName()) && $this->cache->isCached($this->cacheTokenWsName())) {
            return $this->cache->retrieve($this->cacheTokenWsName());
        }

        try {
            $http     = new HttpClient();
            $response = $http->post(($this->credentials->isProduction()) ? Client::WS_AUTH_PRODUCTION : Client::WS_AUTH_TRAINING, 
                [
                    'auth' => [$this->credentials->getConsumerKey(), $this->credentials->getConsumerSecret()],
                    'headers' => [
                        'Content-Type' => 'application/x-www-form-urlencoded',
                    ],
                    'form_params' => [
                        'grant_type' => 'client_credentials'
                    ]
                ],
            );
            return $this->storeWs(json_decode($response->getBody()->getContents(), true));
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    private function signInSso()
    {
        if ($this->cache->isNotExpired($this->cacheTokenSsoName()) && $this->cache->isCached($this->cacheTokenSsoName())) {
            return $this->cache->retrieve($this->cacheTokenSsoName());
        }

        try {
            $http     = new HttpClient();
            $response = $http->post(($this->credentials->isProduction()) ? Client::SSO_AUTH_PRODUCTION : Client::SSO_AUTH_TRAINING, 
                [
                    'headers' => [
                        'Content-Type' => 'application/x-www-form-urlencoded',
                    ],
                    'form_params' => [
                        'grant_type' => 'password',
                        'client_id'  => $this->credentials->getClientId(),
                        'username'   => $this->credentials->getUsername(),
                        'password'   => $this->credentials->getPassword()
                    ]
                ],
            );
            return $this->storeSso(json_decode($response->getBody()->getContents(), true));
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function storeWs($response)
    {
        $this->cache->store($this->cacheTokenWsName(), $response['access_token'], time() + $response['expires_in']);
    }

    public function storeSso($response)
    {
        $this->cache->store($this->cacheTokenSsoName(),[
            "access_token"  => $response['access_token'],
            "refresh_token" => $response['refresh_token']
        ], time() + $response['expires_in']);
    }

    private function cacheTokenWsName()
    {
        return "wso.token.".$this->credentials->getConsumerKey();
    }

    private function cacheTokenSsoName()
    {
        return "sso.token.".$this->credentials->getUsername();
    }
}
