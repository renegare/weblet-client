<?php

namespace Renegare\Weblet\Client\Platform;

use Renegare\Scoauth\ClientInterface;
use Renegare\Scoauth\Token;
use Psr\Log\LoggerInterface;
use Renegare\HTTP\JSONClient;

class Client extends JSONClient implements ClientInterface {

    protected $redirectUrl;
    protected $clientId;
    protected $clientSecret;
    protected $token;
    protected $supportedHTTPMethods = ['get', 'post', 'put', 'delete', 'patch', 'options'];

    /**
     * @param string $endPoint - http://api.endpoint.com (no trailing slash)
     * @param integer $clientId
     * @param string $clientSecret
     * @param string $redirectUrl - http://weblet.com/redirect/cb (full url no uri)
     * @param LoggerInterface $logger [optional]
     */
    public function __construct($endPoint, $clientId, $clientSecret, $redirectUrl, LoggerInterface $logger = null) {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->redirectUrl = $redirectUrl;
        parent::__construct($endPoint, $logger);
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthUrl() {
        throw new \Exception('Not implemented: ' . __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function getRedirectUri() {
        return preg_replace('/^http:\/\/[^\/]+/', '', $this->redirectUrl);
    }

    /**
     * {@inheritdoc}
     */
    public function createToken($authCode) {
        throw new \Exception('Not implemented: ' . __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    public function setToken(Token $token) {
        throw new \Exception('Not implemented: ' . __METHOD__);
    }
    /**
     * {@inheritdoc}
     */
    protected function request($method = 'get', $resource=null, $data = null, array $headers = []) {
        throw new \Exception('Not implemented: ' . __METHOD__);
    }

    /**
     * {@inheritdoc}
     */
    protected function createRequest($method, $url=null, array $options = []) {
        throw new \Exception('Not implemented: ' . __METHOD__);
    }

    protected function getAccessToken() {
        throw new \Exception('Not implemented: ' . __METHOD__);
    }

    protected function willExpireSoon(Token $token) {
        throw new \Exception('Not implemented: ' . __METHOD__);
    }

    protected function refreshToken(AccessToken $token) {
        throw new \Exception('Not implemented: ' . __METHOD__);
    }
}
