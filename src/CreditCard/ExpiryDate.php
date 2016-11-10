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
use InvalidArgumentException;

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
     * @var integer
     */
    private $year;

    /**
     * Expiration month.
     *
     * @var integer
     */
    private $month;

    /**
     * @param integer $month
     * @param integer $year
     */
    public function __construct($month, $year)
    {
        $pad = "20"; # Since expiration would be in future, assuming start millenium is 2000.
                     # Please correct this accordingly in next millenium :P.
        $this->year  = (int) str_pad($year, 4, $pad, STR_PAD_LEFT);
        $this->month = (int) $month;
    }

    /**
     * Whether date is in past or not.
     *
     * @return boolean
     */
    public function isExpired()
    {
        return (new DateTime()) > $this->getExpiration();
    }

    /**
     * Returns expiration date
     *
     * @return DateTime
     */
    public function getExpiration()
    {
        $dateTime = new DateTime();

        return $dateTime
            ->setDate($this->year, $this->month, $this->getMonthDays())
            ->setTime(23, 59, 59);
    }

    /**
     * Returns four digit year.
     *
     * @return integer
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * Returns two digit year.r
     *
     * @return string
     */
    public function getTwoDigitYear()
    {
        return substr($this->year, 2, 2);
    }

    /**
     * Returns month.
     *
     * @return int
     */
    public function getMonth()
    {
        return $this->month;
    }

    /**
     * Return two digit month.
     *
     * @return string
     */
    public function getTwoDigitMonth()
    {
        return str_pad($this->month, 2, '0', STR_PAD_LEFT);
    }

    private function getMonthDays()
    {
        $dateTime = new DateTime("{$this->year}-{$this->month}-01");
        return $dateTime->format('t');
    }
}
