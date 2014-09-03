<?php

namespace Renegare\Weblet\Client\Test;

use Renegare\Weblet\Client\WebletTestCase as ClientWebletTestCase;
use Symfony\Component\HttpKernel\Client;
use Symfony\Component\BrowserKit\Cookie;

class WebletTestCase extends ClientWebletTestCase {

    public function getAccessToken(Client $client) {
        $app = $this->app;
        $this->assertArrayHasKey('session.storage.handler', $app, 'Renegare\SilexCSH\CookieSessionServiceProvider has not been registered.');
        $sessionStorageHandler = $app['session.storage.handler'];
        $this->assertInstanceOf('Renegare\SilexCSH\CookieSessionHandler', $sessionStorageHandler, 'Can only support Renegare\SilexCSH\CookieSessionHandler sessions');
        $cookie = $client->getCookieJar()->get($sessionStorageHandler->getCookieName());
        $sessionData = unserialize(unserialize($cookie->getValue())[1]);
        $token = unserialize($sessionData['_security_app']);
        return $token;
    }
}
