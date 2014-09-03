<?php

namespace Renegare\Weblet\Client\Test\Functional;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Renegare\Weblet\Client\Test\WebletTestCase;
use Renegare\Weblet\Base\Weblet;

class HealthCheckTest extends WebletTestCase {

    public function configureApplication(Weblet $app) {
        parent::configureApplication($app);
        $app->enableSecurity();
    }

    public function testFlow() {
        $client = $this->createClient();
        $client->followRedirects(true);
        $crawler = $client->request('GET', '/_healthcheck');
        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }
}
