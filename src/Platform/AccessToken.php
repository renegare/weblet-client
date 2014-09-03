<?php

namespace Renegare\Weblet\Client\Platform;

use Renegare\Scoauth\Token;

class AccessToken extends Token {

    public function getCredentials() {
        return $this->getAttributes();
    }
}
