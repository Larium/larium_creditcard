<?php

declare(strict_types=1);

namespace Larium\CreditCard;

use function array_intersect_key;
use function array_replace;
use function strlen;
use function strtoupper;
use function substr;

/**
 * CreditCard class acts as value object.
 */
final class CreditCard
{
    public const VISA               = 'visa';
    public const MASTER             = 'master';
    public const DISCOVER           = 'discover';
    public const AMEX               = 'american_express';
    public const DINERS_CLUB        = 'diners_club';
    public const JCB                = 'jcb';
    public const SWITCH_BRAND       = 'switch';
    public const SOLO               = 'solo';
    public const DANKORT            = 'dankort';
    public const MAESTRO            = 'maestro';
    public const FORBRUGSFORENINGEN = 'forbrugsforeningen';
    public const LASER              = 'laser';
    public const UNIONPAY           = 'unionpay';
    public const MIR                = 'mir';

    /**
     * Card holder name.
     * Should be in upper case.
     *
     * @var string
     */
    private ?string $holderName = '';

    /**
     * Expire date of card as value object
     *
     * @var ExpiryDate
     */
    private ExpiryDate $expiryDate;

    /**
     * The brand of card.
     *
     * @var string
     */
    private string $brand = '';

    /**
     * The number of card.
     *
     * @var string
     */
    private string $number = '';

    /**
     * The verification value of card (cvv).
     * 3 or 4 digits.
     *
     * @var string
     */
    private string $cvv;

    /**
     * Whether card is require verification value to be present.
     *
     * @var bool
     */
    private bool $requireCvv = true;

    /**
     * Token stored from a real credit card and can be used for purchases.
     *
     * @var Token
     */
    private ?Token $token;

    /**
     * @var string
     */
    private string $bin = '';

    /**
     * @var string
     */
    private string $issuingBank = '';

    /**
     * The iso alpha 3 country code.
     * @var string
     */
    private string $country = '';

    public function __construct(array $options = [])
    {
        $default = [
            'holderName' => '',
            'month'      => '1',
            'year'       => '1970',
            'brand'      => '',
            'number'     => '',
            'cvv'        => '',
            'requireCvv' => true,
            'token'      => null
        ];

        $options = array_intersect_key($options, $default);
        $options = array_replace($default, $options);

        $month = strval($options['month'] ?? 1);
        $year  = strval($options['year'] ?? 1970);
        $token = $options['token'];

        unset($options['month'], $options['year'], $options['brand'], $options['token']);

        $this->setProperties($month, $year, $token, $options);
    }

    private function setProperties(
        string $month,
        string $year,
        string | Token | null $token = null,
        array $options = []
    ) {
        foreach ($options as $prop => $value) {
            $this->$prop = $value;
        }

        if (!empty($this->holderName)) {
            $this->holderName = strtoupper($this->holderName);
        }
        $this->expiryDate = new ExpiryDate($month, $year);

        $this->detectBrand();

        $this->token($token);

        $this->bin = substr($this->number, 0, 6);
    }

    private function token(string|Token|null $token): void
    {
        if ($token instanceof Token || $token === null) {
            $this->token = $token;

            return;
        }

        $this->token = new Token($token);
    }

    /**
     * @return void
     */
    private function detectBrand(): void
    {
        $detector = new CreditCardDetector();

        $this->brand = $detector->detect($this->number);
    }

    /**
     * @param string $prop
     * @param mixed $value
     * @return CreditCard
     */
    private function with(string $prop, mixed $value): self
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
    public function getNumber(): string
    {
        return $this->number;
    }

    /**
     * Sets card number.
     *
     * @param  string $number
     * @return CreditCard
     */
    public function withNumber(string $number): self
    {
        $card = $this->with('number', $number);
        $card->detectBrand();
        $card->token = null;
        $card->bin = substr($number, 0, 6);

        return $card;
    }

    /**
     * Gets card holder name.
     *
     * @return string|null
     */
    public function getHolderName(): ?string
    {
        return $this->holderName;
    }

    /**
     * Sets card holder name.
     *
     * @param  string $holderName
     * @return CreditCard
     */
    public function withHolderName(string $holderName): self
    {
        $holderName = strtoupper($holderName);

        return $this->with('holderName', $holderName);
    }

    /**
     * Gets expiry date card.
     *
     * @return ExpiryDate
     */
    public function getExpiryDate(): ExpiryDate
    {
        return $this->expiryDate;
    }

    /**
     * Sets expiry month of card.
     *
     * @param  ExpiryDate $expiryDate
     * @return CreditCard
     */
    public function withExpiryDate(ExpiryDate $expiryDate): self
    {
        return $this->with('expiryDate', $expiryDate);
    }

    /**
     * Gets the brand of card.
     *
     * @return string
     */
    public function getBrand(): string
    {
        return $this->brand;
    }

    /**
     * Sets the brand of card.
     *
     * @param  string $brand
     * @return CreditCard
     */
    public function withBrand(string $brand): self
    {
        return $this->with('brand', $brand);
    }

    /**
     * Gets card verification value (cvv).
     *
     * @return string
     */
    public function getCvv(): string
    {
        return $this->cvv;
    }

    /**
     * Sets card verification value.
     *
     * @param  string $cvv
     * @return CreditCard
     */
    public function withCvv(string $cvv): self
    {
        return $this->with('cvv', $cvv);
    }

    /**
     * Check if cvv is required for credit card validation.
     *
     * @return bool
     */
    public function isRequireCvv(): bool
    {
        return $this->requireCvv;
    }

    /**
     * Gets referenece token of a credit card.
     *
     * @return Token|null
     */
    public function getToken(): ?Token
    {
        return $this->token;
    }

    /**
     * Sets token value.
     *
     * @param  Token $token
     * @return CreditCard
     */
    public function withToken(Token $token): self
    {
        $card = $this->with('token', $token);

        if (null !== $card->number) {
            $lastDigits = strlen($card->number) <= 4
                ? $card->number :
                substr($card->number, -4);
            $card->number = "XXXX-XXXX-XXXX-" . $lastDigits;
        }

        $card->cvv = '';

        return $card;
    }

    /**
     * Checks whether credit card has stored a Token reference or not.
     *
     * @return bool
     */
    public function hasToken(): bool
    {
        return null !== $this->token;
    }

    public function __clone(): void
    {
        if ($this->expiryDate) {
            $this->expiryDate = clone $this->expiryDate;
        }
    }

    public function getBin(): string
    {
        return $this->bin;
    }

    public function withIssuingBank(string $issuingBank): self
    {
        return $this->with('issuingBank', $issuingBank);
    }

    public function getIssuingBank(): string
    {
        return $this->issuingBank;
    }

    public function withCountry(string $country): self
    {
        return $this->with('country', $country);
    }

    public function getCountry(): string
    {
        return $this->country;
    }
}
