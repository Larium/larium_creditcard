<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Larium\CreditCard;

class CreditCardValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider cardOptionsProvider
     */
    public function testValidation($options, $expected, $errorProperty)
    {
        $card = new CreditCard($options);

        $validator = new CreditCardValidator();

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

        $card = new CreditCard(['token' => '0123456789']);

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
                    'firstName' => 'John',
                    'lastName'  => 'Doe',
                    'month'     => 1,
                    'year'      => date('Y') + 1,
                    'brand'     => CreditCard::VISA,
                    'number'    => '4532875311640795',
                    'cvv'       => '123',
                    'foo'       => 1
                ),
                true,
                null
            ),
            # 1
            array(
                array(
                    'firstName' => 'John',
                    'lastName'  => 'Doe',
                    'month'     => 1,
                    'year'      => date('Y') + 1,
                    'brand'     => CreditCard::VISA,
                    'number'    => '4735930212834206',
                ),
                false,
                'cvv'
            ),
            # 2
            array(
                array(
                    'firstName' => 'John',
                    'lastName'  => 'Doe',
                    'month'     => 1,
                    'year'      => date('Y') + 1,
                    'brand'     => CreditCard::VISA,
                    'number'    => '4735930212834206',
                    'cvv'       => '13',
                ),
                false,
                'cvv'
            ),
            # 3
            array(
                array(
                    'firstName' => 'John',
                    'lastName'  => 'Doe',
                    'month'     => 1,
                    'year'      => date('Y') + 1,
                    'brand'     => CreditCard::VISA,
                    'number'    => '4735930212834206',
                    'cvv'       => '1234',
                ),
                false,
                'cvv'
            ),
            # 4
            array(
                array(
                    'firstName' => 'John',
                    'lastName'  => 'Doe',
                    'month'     => 1,
                    'year'      => date('Y') + 1,
                    'brand'     => CreditCard::AMERICAN_EXPRESS,
                    'number'    => '341419371821943',
                    'cvv'       => '123',
                ),
                false,
                'cvv'
            ),
            # 5
            array(
                array(
                    'firstName' => 'John',
                    'lastName'  => 'Doe',
                    'month'     => 1,
                    'year'      => date('Y') + 1,
                    'brand'     => CreditCard::AMERICAN_EXPRESS,
                    'number'    => '341419371821943',
                    'cvv'       => '1234',
                ),
                true,
                null
            ),
            # 6
            array(
                array(
                    'firstName' => 'John',
                    'lastName'  => 'Doe',
                    'month'     => 1,
                    'year'      => date('Y') - 1,
                    'brand'     => CreditCard::VISA,
                    'number'    => '4532287586041099',
                    'cvv'       => '123'
                ),
                false,
                'date'
            ),
            # 7
            array(
                array(
                    'firstName' => 'John',
                    'lastName'  => 'Doe',
                    'month'     => 1,
                    'year'      => date('Y') + 1,
                    'brand'     => CreditCard::MASTER,
                    'number'    => '5284911033259148',
                    'cvv'       => '123'
                ),
                true,
                null
            ),
            # 8
            array(
                array(
                    'firstName' => 'John',
                    'lastName'  => 'Doe',
                    'month'     => 1,
                    'year'      => date('Y') + 1,
                    'brand'     => CreditCard::VISA,
                    'number'    => '411111111111111',
                    'cvv'       => '123'
                ),
                false,
                'number'
            ),
            # 9
            array(
                array(
                    'firstName' => '',
                    'lastName'  => null,
                    'month'     => 1,
                    'year'      => date('Y') + 1,
                    'brand'     => CreditCard::VISA,
                    'number'    => '4111111111111111',
                    'cvv'       => '123'
                ),
                false,
                'name'
            ),
            # 10
            array(
                array(
                    'firstName' => 'John',
                    'lastName'  => null,
                    'month'     => 1,
                    'year'      => date('Y') + 1,
                    'brand'     => CreditCard::VISA,
                    'number'    => '4111111111111111',
                    'cvv'       => '123'
                ),
                false,
                'name'
            ),
            # 11
            array(
                array(
                    'firstName' => '',
                    'lastName'  => 'Doe',
                    'month'     => 1,
                    'year'      => date('Y') + 1,
                    'brand'     => CreditCard::VISA,
                    'number'    => '4111111111111111',
                    'cvv'       => '123'
                ),
                false,
                'name'
            ),
            # 12
            array(
                array(
                    'firstName' => 'John',
                    'lastName'  => 'Doe',
                    'month'     => 1,
                    'year'      => date('Y') + 1,
                    'brand'     => null,
                    'number'    => '869989909227336',
                    'cvv'       => '123'
                ),
                false,
                'brand'
            ),
            # 13
            array(
                array(
                    'firstName' => 'John',
                    'lastName'  => 'Doe',
                    'month'     => 1,
                    'year'      => date('Y') + 1,
                    'brand'     => null,
                    'number'    => '4111111111111111',
                    'requireCvv'=> false
                ),
                true,
                null
            ),
            # 14
            array(
                array(
                    'firstName' => 'John',
                    'lastName'  => 'Doe',
                    'month'     => 1,
                    'year'      => date('Y') + 1,
                    'brand'     => CreditCard::MAESTRO,
                    'number'    => '5038525566641172',
                    'cvv'       => '123'
                ),
                true,
                null
            ),
        );
    }
}
