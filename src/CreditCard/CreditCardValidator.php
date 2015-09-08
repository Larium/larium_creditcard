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

use RuntimeException;

/**
 * CreditCardValidator
 *
 * @author Andreas Kollaros <andreas@larium.net>
 */
class CreditCardValidator
{
    const CONTEXT_CREDITCARD = 'credit-card';

    const CONTEXT_TOKEN      = 'token';

    protected $creditCard;

    protected $errors = array();

    protected $context;

    public function __construct($context = self::CONTEXT_CREDITCARD)
    {
        $this->setContext($context);
    }

    /**
     * Sets the context that the validator will validate a CreditCard.
     *
     * @param string $context
     * @return void
     */
    public function setContext($context)
    {
        if (!in_array($context, [self::CONTEXT_CREDITCARD, self::CONTEXT_TOKEN])) {
            throw new RuntimeException(sprintf("Invalid validation context '%s'", $context));
        }

        $this->context = $context;
    }

    /**
     * Validates a CreditCard object.
     *
     * @param CreditCard $creditCard
     * @return array
     */
    public function validate(CreditCard $creditCard)
    {
        $this->errors       = array();
        $this->creditCard   = $creditCard;

        if ($this->context == self::CONTEXT_CREDITCARD) {
            $this->validateNumber();
            $this->validateExpiration();
            $this->validateVerificationValue();
            $this->validateBrand();
            $this->validateCardHolder();
        } elseif ($this->context == self::CONTEXT_TOKEN) {
            $this->validateToken();
        }

        return $this->errors;
    }

    /**
     * Gets possible errors when validated a CreditCard.
     * Returns empty array if no errors found.
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    protected function validateNumber()
    {
        ($this->assertLength($this->creditCard->getNumber(), 12, 19)
            && $this->assertChecksum($this->creditCard->getNumber()))
            or $this->errors['number'] = 'not a valid number';
    }

    protected function validateExpiration()
    {
        $this->creditCard->getExpiryDate()->isExpired()
            and $this->errors['date'] = 'not a valid date';
    }

    protected function validateVerificationValue()
    {
        if (false === $this->creditCard->isRequireCvv()) {
            return;
        }

        $length = $this->creditCard->getBrand() == CreditCard::AMEX
            ? 4 : 3;

        strlen($this->creditCard->getCvv()) == $length
            or $this->errors['cvv'] = 'not a valid cvv';
    }

    protected function validateBrand()
    {
        $this->assertNotEmpty($this->creditCard->getBrand())
            or $this->errors['brand'] = 'not valid card type';
    }

    protected function validateCardHolder()
    {
        ($this->assertNotEmpty($this->creditCard->getFirstName())
            && $this->assertNotEmpty($this->creditCard->getLastName()))
            or $this->errors['name'] = 'not valid first name or last name';
    }

    protected function validateToken()
    {
        if (!$this->assertNotEmpty($this->creditCard->getToken())) {
            $this->errors['token'] = 'token value is empty';

            return;
        }

        !$this->creditCard->getToken()->isExpired()
            or $this->errors['token'] = 'token has been expired';
    }

    protected function assertLength($value, $min = 0, $max = 1)
    {
        $length = strlen($value);

        return $length >= $min && $length <= $max;
    }

    protected function assertNotEmpty($value)
    {
        return !empty($value);
    }

    /**
     * Checks the validity of a card number by use of the the Luhn Algorithm.
     * Please see http://en.wikipedia.org/wiki/Luhn_algorithm for details.
     *
     * @param integer $number the number to check
     *
     * @return boolean if given number has valid checksum
     */
    protected function assertChecksum($number)
    {
        $map = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 0, 2, 4, 6, 8, 1, 3, 5, 7, 9);
        $sum = 0;
        $last = strlen($number) - 1;
        for ($i = 0; $i <= $last; $i++) {
            $sum += $map[$number[$last - $i] + ($i & 1) * 10];
        }

        return ($sum % 10 == 0);
    }
}
