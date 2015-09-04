# Credit card as [Value Object](https://en.wikipedia.org/wiki/Value_object)

````php
<?php
use Larium\CreditCard\CreditCard;
use Larium\CreditCard\CreditCardValidator;

require_once 'vendor/autoload.php';

$data = [
    'firstName' => 'John',
    'lastName'  => 'Doe',
    'month'     => 1,
    'year'      => date('Y') + 1,
    'brand'     => CreditCard::VISA,
    'number'    => '4532875311640795',
    'cvv'       => '123',
];

$card = new CreditCard($data);

echo $card->getBrand(); # visa

$card = $card->withNumber('5284911033259148');
echo $card->getBrand(); # master

````

## Validating credit card

````php
use Larium\CreditCard\CreditCard;
use Larium\CreditCard\CreditCardValidator;

require_once 'vendor/autoload.php';

$data = [
    'firstName' => 'John',
    'lastName'  => 'Doe',
    'month'     => 1,
    'year'      => date('Y') + 1,
    'brand'     => CreditCard::VISA,
    'number'    => '4532875311640795',
    'cvv'       => '123',
];

$validator = new CreditCardValidator();
$errors = $validator->validate($card);
$valid = count($errors) === 0;

$card = $card->withNumber('1');
$error = $validator->validate($card);

print_r($errors);
/*
Array
(
    [number] => not a valid number
    [brand] => not valid card type
)
*/
````
