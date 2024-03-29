<?php

declare(strict_types=1);

namespace Larium\CreditCard;

use PHPUnit\Framework\TestCase;

class CreditCardTest extends TestCase
{
    public function testCreditCardInstance()
    {
        $data = [
            'holderName' => 'John Doe',
            'month'     => '1',
            'year'      => strval(date('Y') + 1),
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
        $this->assertEquals(strtoupper($data['holderName']), $card->getHolderName());
    }

    public function testCreditCardMethods()
    {
        $card = new CreditCard();

        $card = $card->withNumber('5038525566641172')
            ->withHolderName('Mark Doe')
            ->withExpiryDate(new ExpiryDate('1', strval(date('Y') + 1)))
            ->withBrand(CreditCard::MAESTRO)
            ->withCvv('123')
            ->withIssuingBank('NATIONAL BANK OF GREECE')
            ->withCountry('GRC');

        $this->assertNotNull($card->getNumber());
        $this->assertNotNull($card->getHolderName());
        $this->assertNotNull($card->getExpiryDate());
        $this->assertNotNull($card->getBrand());
        $this->assertNotNull($card->getCvv());
        $this->assertNotNull($card->getIssuingBank());
        $this->assertNotNull($card->getCountry());
        $this->assertNotNull($card->getBin());
    }

    public function testCreditCardToken()
    {
        $card = new CreditCard();

        $card = $card->withToken(new Token('0123456789'));

        $this->assertNotNull($card->getToken());
        $this->assertTrue($card->hasToken());
    }

    public function testCreditCardTokenInConsrtuctor()
    {
        $card = new CreditCard(['token' => '0123456789']);

        $this->assertNotNull($card->getToken());
        $this->assertInstanceOf('Larium\CreditCard\Token', $card->getToken());
        $this->assertTrue($card->hasToken());
    }

    public function testSettingToken()
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

        $card = $card->withToken(new Token('0123456789'));

        $this->assertMatchesRegularExpression('/XXXX-XXXX-XXXX-\d{4}/', $card->getNumber());
    }

    public function testExpiryDateImmutability()
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

        $expiryDate = $card->getExpiryDate();

        $newCard = clone $card;

        $this->assertFalse($expiryDate === $newCard->getExpiryDate());
    }

    public function testChangeCreditCardNumber()
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

        $this->assertEquals('453287', $card->getBin());

        $card = $card->withNumber('5413190777725077');
        $this->assertEquals('541319', $card->getBin());
        $this->assertEquals(CreditCard::MASTER, $card->getBrand());
    }
}
