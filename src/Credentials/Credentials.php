<?php
namespace SiASN\Sdk\Credentials;

use SiASN\Sdk\Credentials\CredentialsInterface;

class Credentials implements CredentialsInterface
{
    private $client_id;
    private $username;
    private $password;
    private $consumer_key;
    private $consumer_secret;
    private $mode;

    public function __construct(array $credentials = [])
    {
        $this->client_id       = $credentials['client_id'] ?? null;
        $this->username        = $credentials['username'] ?? null;
        $this->password        = $credentials['password'] ?? null;
        $this->consumer_key    = $credentials['consumer_key'] ?? null;
        $this->consumer_secret = $credentials['consumer_secret'] ?? null;
        $this->mode            = $credentials['mode'] ?? null;
    }

    public function getClientId()
    {
        return $this->client_id;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getConsumerKey()
    {
        return $this->consumer_key;
    }

    public function getConsumerSecret()
    {
        return $this->consumer_secret;
    }

    public function getMode()
    {
        return $this->mode;
    }

    public function isProduction()
    {
        return $this->mode !== null && $this->mode === 'production';
    }
}