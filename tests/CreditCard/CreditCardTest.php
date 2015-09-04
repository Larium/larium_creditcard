<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Larium\CreditCard;

class CreditCardTest extends \PHPUnit_Framework_TestCase
{
    public function testCreditCardInstance()
    {
        $data = [
            'firstName' => 'John',
            'lastName'  => 'Doe',
            'month'     => 1,
            'year'      => date('Y') + 1,
            'brand'     => CreditCard::VISA,
            'number'    => '4532875311640795',
            'cvv'       => '123',
            'foo'       => 1
        ];
        $card = new CreditCard($data);

        $otherCard = $card->withNumber('4735930212834206');

        $this->assertNotEquals($otherCard, $card);
        $this->assertEquals('4532875311640795', $card->getNumber());
        $this->assertEquals('4735930212834206', $otherCard->getNumber());
    }

    public function testCreditCardMethods()
    {
        $card = new CreditCard();

        $card = $card->withNumber('5038525566641172')
            ->withFirstName('Mark')
            ->withLastName('Doe')
            ->withExpiryDate(new ExpiryDate(1, date('Y')+1))
            ->withBrand(CreditCard::MAESTRO)
            ->withCvv('123');

        $this->assertNotNull($card->getNumber());
        $this->assertNotNull($card->getFirstName());
        $this->assertNotNull($card->getLastName());
        $this->assertNotNull($card->getExpiryDate());
        $this->assertNotNull($card->getBrand());
        $this->assertNotNull($card->getCvv());
    }

    public function testCreditCardToken()
    {
        $card = new CreditCard();

        $card = $card->withToken('0123456789');

        $this->assertNotNull($card->getToken());
    }
}
