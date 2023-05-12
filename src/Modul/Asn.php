<?php
namespace SiASN\Sdk\Modul;

use SiASN\Sdk\Core\BaseUrl;
use SiASN\Sdk\Authentication\Client as SiASNClient;
use GuzzleHttp\Client as HttpClient;

final class Asn
{
    private $client;

    public function __construct(array $credentials)
    {
        $this->client = new SiASNClient($credentials);

        $this->client->getAccess();
    }

    public function dataUtama($param)
    {
        try {
            $http     = new HttpClient();
            $response = $http->get($this->baseUrl("pns/data-utama/{$param}"), 
                [
                    'headers' => [
                        'accept'        => 'application/json',
                        'Auth'          => "bearer " . $this->client->getSsoAccessToken(),
                        'Authorization' => "Bearer " . $this->client->getWsAccessToken()
                    ],
                    'form_params' => [
                        'grant_type' => 'client_credentials'
                    ]
                ],
            );
            return json_decode($response->getBody()->getContents(), true);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    private function baseUrl($end_point)
    {
        $base =  ($this->client->isProduction()) ? BaseUrl::PRODUCTION_URL : BaseUrl::TRAINING_URL;
        return $base .  $end_point;
    }
}