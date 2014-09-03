<?php

namespace Renegare\Weblet\Client\Platform;

use Silex\Application;
use Silex\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface {

    public function register(Application $app) {
        $app['platform'] = $app->share(function() use ($app){
            return new Client(
                isset($app['platform.endpoint'])? $app['platform.endpoint'] : '',
                isset($app['platform.client_id'])? $app['platform.client_id'] : '',
                isset($app['platform.client_secret'])? $app['platform.client_secret'] : '',
                isset($app['platform.redirect_uri'])? $app['platform.redirect_uri'] : '',
                $app['logger']
            );
        });
    }

    public function boot(Application $app) {}
}
