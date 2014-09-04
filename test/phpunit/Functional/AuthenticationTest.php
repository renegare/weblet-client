<?php

namespace Renegare\Weblet\Client\Test\Functional;

use Renegare\Weblet\Base\Weblet as BaseWeblet;
use Renegare\Weblet\Client\Weblet;
use Renegare\Weblet\Client\Test\WebletTestCase;
use Symfony\Component\HttpFoundation\Response;
use GuzzleHttp\Message\MessageFactory;

class AuthenticationTest extends WebletTestCase {

    public function configureApplication(BaseWeblet $app) {
        $app['platform.endpoint'] = 'https://api.example.com';
        $app['platform.client_id'] = 123;
        $app['platform.client_secret'] = '50f4k3!';
        $app['platform.redirect_uri'] = 'http://localhost/redirect/cb';
        $app['session.test'] = true;

        parent::configureApplication($app);

        $app->enableSecurity();
        $app->enableCookieSession();

        $app->get('/test-resource', function() use ($app) {
            return 'Test Resource ' . $app['platform']->get('test/endpoint')
                ->json()['response'];
        });

    }

    public function testAuthentication() {
        $start = time();
        $client = $this->createClient();
        $client->request('GET', '/test-resource');

        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_FOUND, $response->getStatusCode());

        $query = http_build_query([
            'client_id' => 123,
            'redirect_uri' => 'http://localhost/redirect/cb',
        ]);
        $this->assertEquals('https://api.example.com/auth/?' . $query, $response->getTargetUrl());

        // post user authentication
        $platform = $this->app['platform'];
        $this->mockHttpResponse($platform, 'POST', 'https://api.example.com/auth/access/', function($request) use ($platform){
            $data = json_decode((string)$request->getBody(), true);
            $this->assertEquals([
                'code' => 'test-auth-code'
            ], $data);

            $this->assertEquals('application/json', implode(',', $request->getHeaders()['Content-Type']));
            $this->assertEquals('50f4k3!', implode(',', $request->getHeaders()['X-CLIENT-SECRET']));

            return [
                'access_code' => 'test-access-token',
                'refresh_code' => 'test-refresh-token',
                'lifetime' => '3600'
            ];
        });

        $mockAuthCode = 'test-auth-code';
        $client->request('GET', sprintf('%s?code=%s', $platform->getRedirectUri(), $mockAuthCode));
        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_FOUND, $response->getStatusCode());
        $this->assertEquals('/test-resource', $response->getTargetUrl());

        $this->mockHttpResponse($platform, 'GET', 'https://api.example.com/test/endpoint', function($request) use ($platform){
            $this->assertEquals('test-access-token', implode(',', $request->getHeaders()['X-ACCESS-CODE']));

            return ['response' => 'All Good!'];
        });

        $client->followRedirect();
        $response = $client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertEquals('Test Resource All Good!', $response->getContent());

        $tokenAttributes = $this->getAccessToken($client)->getCredentials();

        $created = $tokenAttributes['created'];
        $this->assertGreaterThanOrEqual($start, $created);
        unset($tokenAttributes['created']);

        $this->assertEquals([
            'auth_code' => 'test-auth-code',
            'access_code' => 'test-access-token',
            'refresh_code' => 'test-refresh-token',
            'lifetime' => '3600',
        ], $tokenAttributes);
    }
}
