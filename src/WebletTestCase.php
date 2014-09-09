<?php

namespace Renegare\Weblet\Client;

use Renegare\Weblet\Base\WebletTestCase as BaseWebletTestCase;
use Renegare\Weblet\Base\Weblet as BaseWeblet;
use Renegare\GuzzleClientHelper\GuzzlerTestTrait;

class WebletTestCase extends BaseWebletTestCase {
    use GuzzlerTestTrait;

    /**
     * {@inheritdoc}
     */
    public function createApplication() {
        $app = new Weblet(['debug' => true]);
        set_exception_handler(null);
        return $app;
    }
}
