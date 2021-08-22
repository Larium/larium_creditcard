<?php

namespace Larium\CreditCard;

use DateTime;
use PHPUnit\Framework\TestCase;

class TokenTest extends TestCase
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
