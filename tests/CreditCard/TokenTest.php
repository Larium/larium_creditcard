<?php

namespace Larium\CreditCard;

use DateTime;

class TokenTest extends \PHPUnit_Framework_TestCase
{
    public function testTokenInstance()
    {
        $token = new Token('0123456789');

        $this->assertFalse($token->isExpired());

        $token = $token->withExpiryTime(new DateTime('15 minutes ago'));

        $this->assertTrue($token->isExpired());

        $this->assertEquals('0123456789', $token->getReference());

        $this->assertEquals('0123456789', $token->__toString());
    }
}
