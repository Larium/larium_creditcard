<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/*
 * This file is part of the Larium CreditCard package.
 *
 * (c) Andreas Kollaros <andreas@larium.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Larium\CreditCard;

use DateTime;

final class Token
{
    private $reference;

    protected $expiryTime;

    public function __construct($reference, DateTime $expiryTime = null)
    {
        $this->reference  = $reference;
        $this->expiryTime = $expiryTime;
    }

    public function getReference()
    {
        return $this->reference;
    }

    public function withExpiryTime(DateTime $expiryTime)
    {
        $token = clone $this;
        $token->expiryTime = $expiryTime;

        return $token;
    }

    public function isExpired()
    {
        return $this->expiryTime
            && $this->expiryTime < new DateTime();
    }

    public function __toString()
    {
        return $this->getReference();
    }
}
