<?php
namespace SiASN\Sdk\Credentials;

interface CredentialsInterface
{
    /**
     * Returns Si ASN SSO client id.
     *
     * @return string
     */
    public function getClientId();

    /**
     * Returns Si ASN SSO username.
     *
     * @return string
     */
    public function getUsername();

    /**
     * Returns Si ASN SSO password.
     *
     * @return string
     */
    public function getPassword();

    /**
     * Returns Si ASN WSO Consumer Key.
     *
     * @return string
     */
    public function getConsumerKey();

    /**
     * Returns Si ASN WSO Consumer Secret.
     *
     * @return string
     */
    public function getConsumerSecret();

    /**
     * Returns Si ASN WSO Mode.
     *
     * @return string
     */
    public function getMode();

    /**
     * Check apakah dalam mode production
     *
     * @return bool
     */
    public function isProduction();
}