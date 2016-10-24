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

/**
 * CreditCard class acts as value object.
 *
 * @author  Andreas Kollaros <andreas@larium.net>
 */
final class CreditCard
{
    const VISA               = 'visa';
    const MASTER             = 'master';
    const DISCOVER           = 'discover';
    const AMEX               = 'american_express';
    const DINERS_CLUB        = 'diners_club';
    const JCB                = 'jcb';
    const SWITCH_BRAND       = 'switch';
    const SOLO               = 'solo';
    const DANKORT            = 'dankort';
    const MAESTRO            = 'maestro';
    const FORBRUGSFORENINGEN = 'forbrugsforeningen';
    const LASER              = 'laser';

    /**
     * Card holder name.
     * Should be in upper case.
     *
     * @var string
     */
    private $holderName;

    /**
     * Expire date of card as value object
     *
     * @var ExpiryDate
     */
    private $expiryDate;

    /**
     * The brand of card.
     *
     * @var string
     */
    private $brand;

    /**
     * The number of card.
     *
     * @var string
     */
    private $number;

    /**
     * The verification value of card (cvv).
     * 3 or 4 digits.
     *
     * @var integer
     */
    private $cvv;

    /**
     * Whether card is require verification value to be present.
     *
     * @var boolean
     */
    private $requireCvv = true;

    /**
     * Token stored from a real credit card and can be used for purchases.
     *
     * @var Token
     */
    private $token;

    private static $cardCompanies = array(
        self::VISA               => '/^4\d{12}(\d{3})?$/',
        self::MASTER             => '/^(5[1-5]\d{4}|677189)\d{10}$/',
        self::DISCOVER           => '/^(6011|65\d{2})\d{12}$/',
        self::AMEX               => '/^3[47]\d{13}$/',
        self::DINERS_CLUB        => '/^3(0[0-5]|[68]\d)\d{11}$/',
        self::JCB                => '/^35(28|29|[3-8]\d)\d{12}$/',
        self::SWITCH_BRAND       => '/^6759\d{12}(\d{2,3})?$/',
        self::SOLO               => '/^6767\d{12}(\d{2,3})?$/',
        self::DANKORT            => '/^5019\d{12}$/',
        self::MAESTRO            => '/^(5[06-8]|6\d)\d{10,17}$/',
        self::FORBRUGSFORENINGEN => '/^600722\d{10}$/',
        self::LASER              => '/^(6304|6706|6771|6709)\d{8}(\d{4}|\d{6,7})?$/'
    );

    public function __construct(array $options = array())
    {
        $default = array(
            'holderName' => null,
            'month'      => 1,
            'year'       => 1970,
            'brand'      => null,
            'number'     => null,
            'cvv'        => null,
            'requireCvv' => true,
            'token'      => null
        );

        $options = array_intersect_key($options, $default);

        $options = array_replace($default, $options);

        $month = $options['month'];
        $year  = $options['year'];
        $brand = $options['brand'];
        $token = $options['token'];

        unset($options['month'], $options['year'], $options['brand'], $options['token']);

        $expiryDate = new ExpiryDate($month, $year);
        $this->expiryDate = $expiryDate;

        foreach ($options as $prop => $value) {
            $this->$prop = $value;
        }

        if (false === $this->brand = $this->detectBrand()) {
            $this->brand = $brand;
        }

        if ($token) {
            $token instanceof Token
                ? $this->token = $token
                : $this->token = new Token($token);
        }
    }

    private function detectBrand()
    {
        foreach (self::$cardCompanies as $name => $pattern) {
            if ($name == 'maestro') {
                continue;
            }

            if (preg_match($pattern, $this->number)) {
                return $name;
            }
        }

        if (preg_match(self::$cardCompanies['maestro'], $this->number)) {
            return 'maestro';
        }

        return false;
    }

    private function with($prop, $value)
    {
        $card = clone $this;

        $card->$prop = $value;

        return $card;
    }

    /**
     * Gets the number of card.
     *
     * @return string
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Sets card number.
     *
     * @param  string $number
     * @return CreditCard
     */
    public function withNumber($number)
    {
        $card = $this->with('number', $number);
        $card->brand = $card->detectBrand();
        $card->token = null;

        return $card;
    }

    /**
     * Gets card holder first name.
     *
     * @return string
     */
    public function getHolderName()
    {
        return $this->holderName;
    }

    /**
     * Sets card holder first name.
     *
     * @param  string $firstName
     * @return CreditCard
     */
    public function withHolderName($holderName)
    {
        $holderName = strtoupper($holderName);
        return $this->with('holderName', $holderName);
    }

    /**
     * Gets expiry date card.
     *
     * @return ExpireDate
     */
    public function getExpiryDate()
    {
        return $this->expiryDate;
    }

    /**
     * Sets expiry month of card.
     *
     * @param  ExpiryDate $expiryDate
     * @return CreditCard
     */
    public function withExpiryDate(ExpiryDate $expiryDate)
    {
        return $this->with('expiryDate', $expiryDate);
    }

    /**
     * Gets the brand of card.
     *
     * @return string
     */
    public function getBrand()
    {
        return $this->brand;
    }

    /**
     * Sets the brand of card.
     *
     * @param  string $brand
     * @return CreditCard
     */
    public function withBrand($brand)
    {
        return $this->with('brand', $brand);
    }

    /**
     * Gets card verification value (cvv).
     *
     * @return integer
     */
    public function getCvv()
    {
        return $this->cvv;
    }

    /**
     * Sets card verification value.
     *
     * @param  integer $cvv
     * @return CreditCard
     */
    public function withCvv($cvv)
    {
        return $this->with('cvv', $cvv);
    }

    /**
     * Check if cvv is required for credit card validation.
     *
     * @return boolean
     */
    public function isRequireCvv()
    {
        return $this->requireCvv;
    }

    /**
     * Gets referenece token of a credit card.
     *
     * @return Token
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Sets token value.
     *
     * @param  Token $token
     * @return CreditCard
     */
    public function withToken(Token $token)
    {
        $card = $this->with('token', $token);

        if (null !== $card->number) {
            $lastDigits = strlen($card->number) <= 4
                ? $card->number :
                substr($card->number, -4);
            $card->number = "XXXX-XXXX-XXXX-" . $lastDigits;
        }

        $card->cvv = null;

        return $card;
    }

    /**
     * Checks whether credit card has stored a Token reference or not.
     *
     * @return boolean
     */
    public function hasToken()
    {
        return null !== $this->token;
    }

    public function __clone()
    {
        if ($this->expiryDate) {
            $this->expiryDate = clone $this->expiryDate;
        }
    }
}
