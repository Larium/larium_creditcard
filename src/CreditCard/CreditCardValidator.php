<?php

declare(strict_types=1);

namespace Larium\CreditCard;

use RuntimeException;

use function in_array;
use function range;
use function sprintf;
use function strlen;

class CreditCardValidator
{
    public const CONTEXT_CREDITCARD = 'credit-card';

    public const CONTEXT_TOKEN = 'token';

    private CreditCard $creditCard;

    private array $errors = [];

    private string $context = self::CONTEXT_CREDITCARD;

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
    public function setContext($context): void
    {
        $contexts = [self::CONTEXT_CREDITCARD, self::CONTEXT_TOKEN];
        if (!in_array($context, $contexts)) {
            throw new RuntimeException(
                sprintf("Invalid validation context '%s'", $context)
            );
        }

        $this->context = $context;
    }

    /**
     * Validates a CreditCard object.
     *
     * @param CreditCard $creditCard
     * @return array
     */
    public function validate(CreditCard $creditCard): array
    {
        $this->errors       = [];
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
     * example:
     * [
     *     'number' => 'not a valid number',
     *     'month' => 'not a valid month',
     *     'brand' => 'not a valid card type',
     * ]
     *
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    private function validateNumber(): void
    {
        if (false === ($this->assertLength($this->creditCard->getNumber(), 12, 19)
            && $this->assertChecksum($this->creditCard->getNumber()))
        ) {
            $this->errors['number'] = 'not a valid number';
        }
    }

    private function validateExpiration(): void
    {
        $month = $this->creditCard->getExpiryDate()->getMonth();

        if (!in_array($month, range(1, 12))) {
            $this->errors['month'] = 'not a valid month';

            return;
        }

        if ($this->creditCard->getExpiryDate()->isExpired()) {
            $this->errors['date'] = 'not a valid date';
        }
    }

    private function validateVerificationValue(): void
    {
        if (false === $this->creditCard->isRequireCvv()) {
            return;
        }

        $length = $this->creditCard->getBrand() == CreditCard::AMEX
            ? 4 : 3;

        if (strlen($this->creditCard->getCvv()) !== $length) {
            $this->errors['cvv'] = 'not a valid cvv';
        }
    }

    private function validateBrand(): void
    {
        if ($this->assertNotEmpty($this->creditCard->getBrand()) == false) {
            $this->errors['brand'] = 'not valid card type';
        }
    }

    private function validateCardHolder(): void
    {
        if (false === $this->assertNotEmpty($this->creditCard->getHolderName())) {
            $this->errors['name'] = 'not a valid holder name';
        }
    }

    private function validateToken(): void
    {
        if (!$this->assertNotEmpty($this->creditCard->getToken())) {
            $this->errors['token'] = 'token value is empty';

            return;
        }

        if ($this->creditCard->getToken()->isExpired()) {
            $this->errors['token'] = 'token has been expired';
        }
    }

    private function assertLength(string $value, $min = 0, $max = 1): bool
    {
        $length = strlen($value);

        return $length >= $min && $length <= $max;
    }

    private function assertNotEmpty(mixed $value): bool
    {
        return !empty($value);
    }

    /**
     * Checks the validity of a card number by use of the the Luhn Algorithm.
     * Please see http://en.wikipedia.org/wiki/Luhn_algorithm for details.
     *
     * @param string $number the number to check
     *
     * @return bool if given number has valid checksum
     */
    private function assertChecksum(string $number): bool
    {
        $map = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 0, 2, 4, 6, 8, 1, 3, 5, 7, 9];
        $sum = 0;
        $last = strlen($number) - 1;
        for ($i = 0; $i <= $last; $i++) {
            $sum += $map[$number[$last - $i] + ($i & 1) * 10];
        }

        return ($sum % 10 == 0);
    }
}
