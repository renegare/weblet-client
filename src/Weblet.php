<?php

namespace Renegare\Weblet\Client;

use Renegare\Weblet\Base\Weblet as BaseWeblet;
use Silex\Provider\SecurityServiceProvider;
use Renegare\Scoauth\OAuthClientServiceProvider;

class Weblet extends BaseWeblet {

    public function enableSecurity() {
        $this->addFirewall('healthcheck', [
                'pattern' => sprintf('^/%s', trim($this->getHealthCheckUri(), '/')),
                'stateless' => true]);
        $this->addFirewall('app', [
            'pattern' => '^/',
            'scoauth' => true
        ]);

        $this->doRegister(new SecurityServiceProvider, ['security.firewalls']);
        $this->doRegister(new OAuthClientServiceProvider);
        $this->doRegister(new Platform\ServiceProvider, [
            'platform.endpoint',
            'platform.client_id',
            'platform.client_secret',
            'platform.redirect_uri'
        ]);

        $this['scoauth.api.client'] = function() {
            return $this['platform'];
        };
    }

    public function addFirewall($name, $config) {
        $existingConfig = isset($this['security.firewalls'])? $this['security.firewalls'] : [];
        $this['security.firewalls'] = array_merge($existingConfig, [$name => $config]);
    }
}
