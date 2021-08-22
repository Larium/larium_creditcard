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

class CreditCardValidatorTest extends \PHPUnit_Framework_TestCase
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

        if (null == $errorProperty) {
            $this->assertEmpty($errors);
        } else {
            $this->assertArrayHasKey($errorProperty, $errors);
        }
    }

    public function testValidatorContext()
    {
        $validator = new CreditCardValidator();

        $validator->setContext(CreditCardValidator::CONTEXT_TOKEN);

        $card = new CreditCard(array('token' => '0123456789'));

        $validator->validate($card);

        $errors = $validator->getErrors();

        $this->assertEmpty($errors);
    }

    /**
     * @expectedException RuntimeException
     */
    public function testInvalidValidatorContext()
    {
        $validator = new CreditCardValidator();

        $validator->setContext('wrong');
    }

    public function cardOptionsProvider()
    {
        return array(
            # 0
            array(
                array(
                    'holderName' => 'John Doe',
                    'month'     => 1,
                    'year'      => date('Y') + 1,
                    'brand'     => CreditCard::VISA,
                    'number'    => '4532875311640795',
                    'cvv'       => '123',
                    'foo'       => 1
                ),
                true,
                null,
                CreditCardValidator::CONTEXT_CREDITCARD
            ),
            # 1
            array(
                array(
                    'holderName' => 'John Doe',
                    'month'     => 1,
                    'year'      => date('Y') + 1,
                    'brand'     => CreditCard::VISA,
                    'number'    => '4735930212834206',
                ),
                false,
                'cvv',
                CreditCardValidator::CONTEXT_CREDITCARD
            ),
            # 2
            array(
                array(
                    'holderName' => 'John Doe',
                    'month'     => 1,
                    'year'      => date('Y') + 1,
                    'brand'     => CreditCard::VISA,
                    'number'    => '4735930212834206',
                    'cvv'       => '13',
                ),
                false,
                'cvv',
                CreditCardValidator::CONTEXT_CREDITCARD
            ),
            # 3
            array(
                array(
                    'holderName' => 'John Doe',
                    'month'     => 1,
                    'year'      => date('Y') + 1,
                    'brand'     => CreditCard::VISA,
                    'number'    => '4735930212834206',
                    'cvv'       => '1234',
                ),
                false,
                'cvv',
                CreditCardValidator::CONTEXT_CREDITCARD
            ),
            # 4
            array(
                array(
                    'holderName' => 'John Doe',
                    'month'     => 1,
                    'year'      => date('Y') + 1,
                    'brand'     => CreditCard::AMEX,
                    'number'    => '341419371821943',
                    'cvv'       => '123',
                ),
                false,
                'cvv',
                CreditCardValidator::CONTEXT_CREDITCARD
            ),
            # 5
            array(
                array(
                    'holderName' => 'John Doe',
                    'month'     => 1,
                    'year'      => date('Y') + 1,
                    'brand'     => CreditCard::AMEX,
                    'number'    => '341419371821943',
                    'cvv'       => '1234',
                ),
                true,
                null,
                CreditCardValidator::CONTEXT_CREDITCARD
            ),
            # 6
            array(
                array(
                    'holderName' => 'John Doe',
                    'month'     => 1,
                    'year'      => date('Y') - 1,
                    'brand'     => CreditCard::VISA,
                    'number'    => '4532287586041099',
                    'cvv'       => '123'
                ),
                false,
                'date',
                CreditCardValidator::CONTEXT_CREDITCARD
            ),
            # 7
            array(
                array(
                    'holderName' => 'John Doe',
                    'month'     => 1,
                    'year'      => date('Y') + 1,
                    'brand'     => CreditCard::MASTER,
                    'number'    => '5284911033259148',
                    'cvv'       => '123'
                ),
                true,
                null,
                CreditCardValidator::CONTEXT_CREDITCARD
            ),
            # 8
            array(
                array(
                    'holderName' => null,
                    'month'     => 1,
                    'year'      => date('Y') + 1,
                    'brand'     => CreditCard::VISA,
                    'number'    => '4111111111111111',
                    'cvv'       => '123'
                ),
                false,
                'name',
                CreditCardValidator::CONTEXT_CREDITCARD
            ),
            # 9
            array(
                array(
                    'month'     => 1,
                    'year'      => date('Y') + 1,
                    'brand'     => CreditCard::VISA,
                    'number'    => '4111111111111111',
                    'cvv'       => '123'
                ),
                false,
                'name',
                CreditCardValidator::CONTEXT_CREDITCARD
            ),
            # 10
            array(
                array(
                    'holderName' => 'John Doe',
                    'month'     => 1,
                    'year'      => date('Y') + 1,
                    'brand'     => null,
                    'number'    => '869989909227336',
                    'cvv'       => '123'
                ),
                false,
                'brand',
                CreditCardValidator::CONTEXT_CREDITCARD
            ),
            # 11
            array(
                array(
                    'holderName' => 'John Doe',
                    'month'     => 1,
                    'year'      => date('Y') + 1,
                    'brand'     => null,
                    'number'    => '4111111111111111',
                    'requireCvv'=> false
                ),
                true,
                null,
                CreditCardValidator::CONTEXT_CREDITCARD
            ),
            # 12
            array(
                array(
                    'holderName' => 'John Doe',
                    'month'     => 1,
                    'year'      => date('Y') + 1,
                    'brand'     => CreditCard::MAESTRO,
                    'number'    => '5038525566641172',
                    'cvv'       => '123'
                ),
                true,
                null,
                CreditCardValidator::CONTEXT_CREDITCARD
            ),
            # 13
            array(
                array(
                    'token' => '0123456789',
                ),
                true,
                null,
                CreditCardValidator::CONTEXT_TOKEN
            ),
            # 14
            array(
                array(
                    'token' => null,
                ),
                false,
                'token',
                CreditCardValidator::CONTEXT_TOKEN
            ),
            # 15
            array(
                array(
                    'holderName' => 'John Doe',
                    'month'     => 1,
                    'year'      => date('Y') + 1,
                    'brand'     => CreditCard::VISA,
                    'number'    => '41111111111111',
                    'cvv'       => '123'
                ),
                false,
                'number',
                CreditCardValidator::CONTEXT_CREDITCARD
            ),
            # 16
            array(
                array(
                    'token' => new Token('0123456789', new \DateTime('15 minutes ago')),
                ),
                false,
                'token',
                CreditCardValidator::CONTEXT_TOKEN
            ),
            # 17
            array(
                array(
                    'holderName' => 'John Doe',
                    'month'     => 13,
                    'year'      => date('Y') + 1,
                    'brand'     => CreditCard::VISA,
                    'number'    => '41111111111111',
                    'cvv'       => '123'
                ),
                false,
                'month',
                CreditCardValidator::CONTEXT_CREDITCARD
            ),
        );
    }
}
