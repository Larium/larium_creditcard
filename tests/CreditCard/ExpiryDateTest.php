<?php

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

class ExpiryDateTest extends \PHPUnit_Framework_TestCase
{
    public function testExpiryDateFormatters()
    {
        $month = 2;
        $year = 17;
        $e = new ExpiryDate($month, $year);

        $this->assertEquals('02', $e->getTwoDigitMonth());
        $this->assertEquals(2, $e->getMonth());
        $this->assertEquals(2017, $e->getYear());
        $this->assertEquals(17, $e->getTwoDigitYear());
    }

    public function testExpired()
    {
        $last_month = new DateTime('2 months ago');

        $e = new ExpiryDate(
            $last_month->format('n'),
            $last_month->format('y')
        );

        $this->assertTrue($e->isExpired());
    }

    public function testTodayNotExpired()
    {
        $today = new DateTime();

        $e = new ExpiryDate(
            $today->format('n'),
            $today->format('y')
        );

        $this->assertFalse($e->isExpired());
    }

    public function testNotExpireInFuture()
    {
        $next_month = new DateTime('1 month');

        $e = new ExpiryDate(
            $next_month->format('n'),
            $next_month->format('y')
        );

        $this->assertFalse($e->isExpired());
    }
}
