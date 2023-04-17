<?php

declare(strict_types=1);

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
