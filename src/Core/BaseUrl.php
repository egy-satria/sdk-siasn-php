<?php
namespace SiASN\Sdk\Core;

final class BaseUrl
{
    const WS_AUTH_PRODUCTION = "https://apimws.bkn.go.id/oauth2/token";
    const WS_AUTH_TRAINING   = "https://training-apimws.bkn.go.id/oauth2/token";

    const SSO_AUTH_PRODUCTION = "https://sso-siasn.bkn.go.id/auth/realms/public-siasn/protocol/openid-connect/token";
    const SSO_AUTH_TRAINING   = "https://iam-siasn.bkn.go.id/auth/realms/public-siasn/protocol/openid-connect/token";

    const TRAINING_URL   = "https://training-apimws.bkn.go.id:8243/api/1.0/";
    const PRODUCTION_URL = "https://apimws.bkn.go.id:8243/apisiasn/1.0/";
}