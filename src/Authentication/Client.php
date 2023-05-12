<?php
namespace SiASN\Sdk\Authentication;

use SiASN\Sdk\Credentials\Credentials;
use SiASN\Sdk\Core\Cache;
use SiASN\Sdk\Core\BaseUrl;
use GuzzleHttp\Client as HttpClient;

final class Client
{
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
            $response = $http->post(($this->credentials->isProduction()) ? BaseUrl::WS_AUTH_PRODUCTION : BaseUrl::WS_AUTH_TRAINING, 
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
            $response = $http->post(($this->credentials->isProduction()) ? BaseUrl::SSO_AUTH_PRODUCTION : BaseUrl::SSO_AUTH_TRAINING, 
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
        $this->cache->store($this->cacheTokenSsoName(), $response['access_token'], time() + $response['expires_in']);
    }

    public function isProduction()
    {
        return $this->credentials->isProduction();
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
