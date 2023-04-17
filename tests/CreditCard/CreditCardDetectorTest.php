<?php

declare(strict_types=1);

namespace Larium\CreditCard;

use PHPUnit\Framework\TestCase;

class CreditCardDetectorTest extends TestCase
{
    /**
     * @dataProvider creditCardsProvider
     */
    public function testDetector($number, $brand)
    {
        $detector = new CreditCardDetector();

        $this->assertEquals(
            $brand,
            $detector->detect($number)
        );
    }

    public static function creditCardsProvider()
    {
        $source = __DIR__ . '/../fixtures/';

        $files = [
            CreditCard::JCB => $source . 'jcb.csv',
            CreditCard::MIR => $source . 'mir.csv',
            CreditCard::VISA => $source . 'visa.csv',
            CreditCard::AMEX => $source . 'amex.csv',
            CreditCard::MASTER => $source . 'master.csv',
            CreditCard::MAESTRO => $source . 'maestro.csv',
            CreditCard::DANKORT => $source . 'dankort.csv',
            CreditCard::UNIONPAY => $source . 'unionpay.csv',
            CreditCard::DISCOVER => $source . 'discover.csv',
            CreditCard::DINERS_CLUB => $source . 'diners.csv',
            CreditCard::FORBRUGSFORENINGEN => $source . 'forb.csv',
        ];

        $data = [];
        foreach ($files as $k => $f) {
            $file = new \SplFileObject($f);
            $file->setFlags(\SplFileObject::SKIP_EMPTY);
            while (!$file->eof()) {
                $line = $file->fgetcsv();
                if (empty($line)) {
                    continue;
                }
                $data[] = [
                    $line[0],
                    $k
                ];
            }
        }

        return $data;
    }
}
