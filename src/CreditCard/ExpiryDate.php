<?php

declare(strict_types=1);

namespace Larium\CreditCard;

use DateTime;

use function intval;
use function str_pad;
use function substr;

/**
 * ExpiryDate class provides help for handling credit card expiration date.
 *
 * @author  Andreas Kollaros <andreas@larium.net>
 */
final class ExpiryDate
{
    /**
     * Expiration year.
     * Two or four digit of expiration year.
     * Will be converted to four digit if two digit input provided.
     *
     * @var string
     */
    private $year;

    /**
     * Expiration month.
     *
     * @var string
     */
    private $month;

    /**
     * @param string $month
     * @param string $year
     */
    public function __construct(string $month, string $year)
    {
        $pad = "20"; # Since expiration would be in future, assuming start millenium is 2000.
        # Please correct this accordingly in next millenium :P.
        $this->year  = str_pad($year, 4, $pad, STR_PAD_LEFT);
        $this->month = $month;
    }

    /**
     * Whether date is in past or not.
     *
     * @return bool
     */
    public function isExpired(): bool
    {
        return (new DateTime()) > $this->getExpiration();
    }

    /**
     * Returns expiration date
     *
     * @return DateTime
     */
    public function getExpiration(): DateTime
    {
        $dateTime = new DateTime();

        return $dateTime
            ->setDate(intval($this->year), intval($this->month), intval($this->getMonthDays()))
            ->setTime(23, 59, 59);
    }

    /**
     * Returns four digit year.
     *
     * @return string
     */
    public function getYear(): string
    {
        return $this->year;
    }

    /**
     * Returns two digit year.
     *
     * @return string
     */
    public function getTwoDigitYear(): string
    {
        return substr($this->year, 2, 2);
    }

    /**
     * Returns month.
     *
     * @return string
     */
    public function getMonth(): string
    {
        return $this->month;
    }

    /**
     * Return two digit month.
     *
     * @return string
     */
    public function getTwoDigitMonth(): string
    {
        return str_pad($this->month, 2, '0', STR_PAD_LEFT);
    }

    private function getMonthDays(): string
    {
        $dateTime = new DateTime("{$this->year}-{$this->month}-01");
        return $dateTime->format('t');
    }
}
