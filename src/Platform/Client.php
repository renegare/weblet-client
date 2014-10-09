<?php

namespace Renegare\Weblet\Client\Platform;

use Renegare\Scoauth\ClientInterface;
use Renegare\Scoauth\Token;
use Psr\Log\LoggerInterface;
use Renegare\GuzzleClientHelper\JSONClient;

class Client extends JSONClient implements ClientInterface {

    protected $redirectUrl;
    protected $clientId;
    protected $clientSecret;
    protected $token;
    protected $supportedHTTPMethods = ['get', 'post', 'put', 'delete', 'patch', 'options'];

    /**
     * @param string $baseUrl - http://api.endpoint.com (no trailing slash)
     * @param integer $clientId
     * @param string $clientSecret
     * @param string $redirectUrl - http://weblet.com/redirect/cb (full url no uri)
     * @param LoggerInterface $logger [optional]
     */
    public function __construct($baseUrl, $clientId, $clientSecret, $redirectUrl, LoggerInterface $logger = null) {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->redirectUrl = $redirectUrl;
        parent::__construct($baseUrl, $logger);
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthUrl() {
        return sprintf('%s/oauth/auth?%s', $this->baseUrl, http_build_query([
            'response_type' => 'code',
            'client_id' => $this->clientId,
            'redirect_uri' => $this->redirectUrl
        ]));
    }

    /**
     * {@inheritdoc}
     */
    public function getRedirectUri() {
        return preg_replace('/^https?:\/\/[^\/]+/', '', $this->redirectUrl);
    }

    /**
     * {@inheritdoc}
     */
    public function createToken($authCode) {
        $this->debug('Exchanging auth code for access code ...');
        $response = $this->post('oauth/token', http_build_query([
            'grant_type' => 'authorization_code',
            'code' => $authCode,
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret
        ]), ['Content-Type' => 'application/x-www-form-urlencoded']);
        $responseData = $response->json();

        $token = new AccessToken([]);
        $token->setAttributes(array_merge($responseData, [
            'auth_code' => $authCode,
            'created' => time()
        ]));
        $this->setToken($token);

        return $token;
    }

    /**
     * {@inheritdoc}
     */
    public function setToken(Token $token) {
        $this->token = $token;
    }
    /**
     * {@inheritdoc}
     */
    protected function request($method = 'get', $resource=null, $data = null, array $headers = []) {
        return parent::request($method, $resource, $data, $headers);
    }

    /**
     * {@inheritdoc}
     */
    protected function createRequest($method, $url=null, array $options = []) {
        $request = parent::createRequest($method, $url, $options);

        if($accessToken = $this->getAccessToken()) {
            $request->setHeader('Authorization', 'Bearer ' . $accessToken->getAttribute('access_token'));
        }

        return $request;
    }

    protected function getAccessToken() {
        return $this->token;
    }
}
