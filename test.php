<?php
require_once "./vendor/autoload.php";

$client = new \SiASN\Sdk\Authentication\Client([
    "consumer_key"    => "9NB75yEfBlFhkSuPojsjO9yhmUYa",
    "consumer_secret" => "3d45kbbsstx9dxztLNWwW9aRP1ca",
    "mode"            => "production",
    "client_id"       => "kabmglclient",
    "grant_type"      => "password",
    "username"        => "199202062020121004",
    "password"        => "Trinifornia13"
]);

echo "<pre>";
$client->getAccess();
var_dump($client->getWsAccessToken());
var_dump($client->getSsoAccessToken());
