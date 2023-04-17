<?php

declare(strict_types=1);

namespace Larium\CreditCard;

use PHPUnit\Framework\TestCase;

class CreditCardValidatorTest extends TestCase
{
    /**
     * @dataProvider cardOptionsProvider
     */
    public function testValidation($options, $expected, $errorProperty, $context)
    {
        $card = new CreditCard($options);

        $validator = new CreditCardValidator($context);

        $errors = $validator->validate($card);

        $valid = count($errors) == 0;

        $this->assertEquals($expected, $valid);

        if ($errorProperty == null) {
            $this->assertEmpty($errors);
        } else {
            $this->assertArrayHasKey($errorProperty, $errors);
        }
    }

    public function testValidatorContext()
    {
        $validator = new CreditCardValidator();

        $validator->setContext(CreditCardValidator::CONTEXT_TOKEN);

        $card = new CreditCard(['token' => '0123456789']);

        $validator->validate($card);

        $errors = $validator->getErrors();

        $this->assertEmpty($errors);
    }

    public function testInvalidValidatorContext()
    {
        $this->expectException(\RuntimeException::class);
        $validator = new CreditCardValidator();

        $validator->setContext('wrong');
    }

    public static function cardOptionsProvider()
    {
        return [
            # 0
            [
                [
                    'holderName' => 'John Doe',
                    'month'     => 1,
                    'year'      => date('Y') + 1,
                    'brand'     => CreditCard::VISA,
                    'number'    => '4532875311640795',
                    'cvv'       => '123',
                    'foo'       => 1
                ],
                true,
                null,
                CreditCardValidator::CONTEXT_CREDITCARD
            ],
            # 1
            [
                [
                    'holderName' => 'John Doe',
                    'month'     => 1,
                    'year'      => date('Y') + 1,
                    'brand'     => CreditCard::VISA,
                    'number'    => '4735930212834206',
                ],
                false,
                'cvv',
                CreditCardValidator::CONTEXT_CREDITCARD
            ],
            # 2
            [
                [
                    'holderName' => 'John Doe',
                    'month'     => 1,
                    'year'      => date('Y') + 1,
                    'brand'     => CreditCard::VISA,
                    'number'    => '4735930212834206',
                    'cvv'       => '13',
                ],
                false,
                'cvv',
                CreditCardValidator::CONTEXT_CREDITCARD
            ],
            # 3
            [
                [
                    'holderName' => 'John Doe',
                    'month'     => 1,
                    'year'      => date('Y') + 1,
                    'brand'     => CreditCard::VISA,
                    'number'    => '4735930212834206',
                    'cvv'       => '1234',
                ],
                false,
                'cvv',
                CreditCardValidator::CONTEXT_CREDITCARD
            ],
            # 4
            [
                [
                    'holderName' => 'John Doe',
                    'month'     => 1,
                    'year'      => date('Y') + 1,
                    'brand'     => CreditCard::AMEX,
                    'number'    => '341419371821943',
                    'cvv'       => '123',
                ],
                false,
                'cvv',
                CreditCardValidator::CONTEXT_CREDITCARD
            ],
            # 5
            [
                [
                    'holderName' => 'John Doe',
                    'month'     => 1,
                    'year'      => date('Y') + 1,
                    'brand'     => CreditCard::AMEX,
                    'number'    => '341419371821943',
                    'cvv'       => '1234',
                ],
                true,
                null,
                CreditCardValidator::CONTEXT_CREDITCARD
            ],
            # 6
            [
                [
                    'holderName' => 'John Doe',
                    'month'     => 1,
                    'year'      => date('Y') - 1,
                    'brand'     => CreditCard::VISA,
                    'number'    => '4532287586041099',
                    'cvv'       => '123'
                ],
                false,
                'date',
                CreditCardValidator::CONTEXT_CREDITCARD
            ],
            # 7
            [
                [
                    'holderName' => 'John Doe',
                    'month'     => 1,
                    'year'      => date('Y') + 1,
                    'brand'     => CreditCard::MASTER,
                    'number'    => '5284911033259148',
                    'cvv'       => '123'
                ],
                true,
                null,
                CreditCardValidator::CONTEXT_CREDITCARD
            ],
            # 8
            [
                [
                    'holderName' => null,
                    'month'     => 1,
                    'year'      => date('Y') + 1,
                    'brand'     => CreditCard::VISA,
                    'number'    => '4111111111111111',
                    'cvv'       => '123'
                ],
                false,
                'name',
                CreditCardValidator::CONTEXT_CREDITCARD
            ],
            # 9
            [
                [
                    'month'     => 1,
                    'year'      => date('Y') + 1,
                    'brand'     => CreditCard::VISA,
                    'number'    => '4111111111111111',
                    'cvv'       => '123'
                ],
                false,
                'name',
                CreditCardValidator::CONTEXT_CREDITCARD
            ],
            # 10
            [
                [
                    'holderName' => 'John Doe',
                    'month'     => 1,
                    'year'      => date('Y') + 1,
                    'brand'     => null,
                    'number'    => '869989909227336',
                    'cvv'       => '123'
                ],
                false,
                'brand',
                CreditCardValidator::CONTEXT_CREDITCARD
            ],
            # 11
            [
                [
                    'holderName' => 'John Doe',
                    'month'     => 1,
                    'year'      => date('Y') + 1,
                    'brand'     => null,
                    'number'    => '4111111111111111',
                    'requireCvv'=> false
                ],
                true,
                null,
                CreditCardValidator::CONTEXT_CREDITCARD
            ],
            # 12
            [
                [
                    'holderName' => 'John Doe',
                    'month'     => 1,
                    'year'      => date('Y') + 1,
                    'brand'     => CreditCard::MAESTRO,
                    'number'    => '5038525566641172',
                    'cvv'       => '123'
                ],
                true,
                null,
                CreditCardValidator::CONTEXT_CREDITCARD
            ],
            # 13
            [
                [
                    'token' => '0123456789',
                ],
                true,
                null,
                CreditCardValidator::CONTEXT_TOKEN
            ],
            # 14
            [
                [
                    'token' => null,
                ],
                false,
                'token',
                CreditCardValidator::CONTEXT_TOKEN
            ],
            # 15
            [
                [
                    'holderName' => 'John Doe',
                    'month'     => 1,
                    'year'      => date('Y') + 1,
                    'brand'     => CreditCard::VISA,
                    'number'    => '41111111111111',
                    'cvv'       => '123'
                ],
                false,
                'number',
                CreditCardValidator::CONTEXT_CREDITCARD
            ],
            # 16
            [
                [
                    'token' => new Token('0123456789', new \DateTime('15 minutes ago')),
                ],
                false,
                'token',
                CreditCardValidator::CONTEXT_TOKEN
            ],
            # 17
            [
                [
                    'holderName' => 'John Doe',
                    'month'     => 13,
                    'year'      => date('Y') + 1,
                    'brand'     => CreditCard::VISA,
                    'number'    => '41111111111111',
                    'cvv'       => '123'
                ],
                false,
                'month',
                CreditCardValidator::CONTEXT_CREDITCARD
            ],
        ];
    }
}
