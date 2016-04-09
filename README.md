[![Coverage Status](https://coveralls.io/repos/Larium/larium_creditcard/badge.svg?branch=master&service=github)](https://coveralls.io/github/Larium/larium_creditcard?branch=master) [![Build Status](https://travis-ci.org/Larium/larium_creditcard.svg?branch=master)](https://travis-ci.org/Larium/larium_creditcard) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Larium/larium_creditcard/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Larium/larium_creditcard/?branch=master)


# Credit card as [Value Object](https://en.wikipedia.org/wiki/Value_object)

## Installation
You can install this library using [Composer](http://getcomposer.org)

Information about how to install composer you can find [here](https://getcomposer.org/doc/00-intro.md)

### Command line
In root directory of your project run through a console:
```bash
$ composer require "larium/credit-card":"@stable"
```
### Composer.json
Include require line in your ```composer.json``` file
```json
{
	"require": {
    	"larium/credit-card": "@stable"
    }
}
```
and run from console in the root directory of your project:
```bash
$ composer update
```

After this you must require autoload file from composer.
```php
<?php
require_once 'vendor/autoload.php';
```

## Usage

### Creating a credit card object

````php
<?php
use Larium\CreditCard\CreditCard;
use Larium\CreditCard\CreditCardValidator;

require_once 'vendor/autoload.php';

$data = array(
    'holderName'=> 'John Doe',
    'month'     => 1,
    'year'      => date('Y') + 1,
    'brand'     => CreditCard::VISA,
    'number'    => '4532875311640795',
    'cvv'       => '123',
);

$card = new CreditCard($data);

echo $card->getBrand(); # visa
//Auto uppercase holder name.
echo $card->getHolderName(); # JOHN DOE

$card = $card->withNumber('5284911033259148');
echo $card->getBrand(); # master

````

### Adding a token in credit card object

````php
<?php
use Larium\CreditCard\CreditCard;
use Larium\CreditCard\CreditCardValidator;
use Larium\CreditCard\Token;

require_once 'vendor/autoload.php';

$data = array(
    'holderName' => 'John Doe',
    'month'     => 1,
    'year'      => date('Y') + 1,
    'brand'     => CreditCard::VISA,
    'number'    => '4532875311640795',
    'cvv'       => '123',
);

$card = new CreditCard($data);

# ... use credit card to obtain a token reference from a remote payment gateway.

$token = new Token('0123456789');

$card = $card->withToken($token);

# Now, credit card will use token for future payments
# and will mask any sensitive data as number and cvv.
echo $card->getCvv(); # null
echo $card->getNumber(); # XXXX-XXXX-XXXX-0795
echo $card->getToken(); # 0123456789

````

In some cases we need to use a temporary token to fulfil a payment.
Many payment gateways support a limited time token, which represents a card or
a payment method.

````php
<?php
use Larium\CreditCard\Token;

require_once 'vendor/autoload.php';

$token = new Token('0123456789', new DateTime('15 minutes'));
$token->isExpired() # false

$token = new Token('0123456789', new DateTime('15 minutes ago'));
$token->isExpired() # true

````


### Validating a credit card

````php
use Larium\CreditCard\CreditCard;
use Larium\CreditCard\CreditCardValidator;

require_once 'vendor/autoload.php';

$data = array(
    'holderName'=> 'John Doe',
    'month'     => 1,
    'year'      => date('Y') + 1,
    'brand'     => CreditCard::VISA,
    'number'    => '4532875311640795',
    'cvv'       => '123',
);

$card = new CreditCard($data);

$validator = new CreditCardValidator();
$errors = $validator->validate($card);
$valid = count($errors) === 0; # true

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

### Validating a token of a credit card

````php
<?php

use Larium\CreditCard\CreditCard;
use Larium\CreditCard\CreditCardValidator;
use Larium\CreditCard\Token;

require_once 'vendor/autoload.php';

$card = new CreditCard();
$token = new Token('0123456789');
$card = $card->withToken($token);
$validator = new CreditCardValidator(CreditCardValidator::CONTEXT_TOKEN);
$errors = $validator->validate($card);
$valid = count($errors) === 0; # true

$token = new Token('0123456789', new DateTime('15 minutes ago'));
$card = $card->withToken($token);
$validator->setContext(CreditCardValidator::CONTEXT_TOKEN);
$validator->validate($card);
$validator->getErrors();
/*
Array
(
    [token] => token has been expired
)
*/
````
