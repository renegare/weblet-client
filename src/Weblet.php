<?php

namespace Renegare\Weblet\Client;

use Renegare\Weblet\Base\Weblet as BaseWeblet;
use Silex\Provider\SecurityServiceProvider;

class Weblet extends BaseWeblet {

    public function enableSecurity() {
        $this->addFirewall('healthcheck', [
                'pattern' => sprintf('^/%s', trim($this->getHealthCheckUri(), '/')),
                'stateless' => true]);

        $this->doRegister(new SecurityServiceProvider, ['security.firewalls']);
    }

    public function addFirewall($name, $config) {
        $existingConfig = isset($this['security.firewalls'])? $this['security.firewalls'] : [];
        $this['security.firewalls'] = array_merge($existingConfig, [$name => $config]);
    }
}
