<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

namespace Larium\CreditCard;

class CreditCardDetector
{
    private static $cardPatterns = array(
        CreditCard::VISA               => '/(^4\d{12}(\d{3})?$)|(^4\d{12}(\d{6})?$)/',
        CreditCard::MASTER             => '/^((5[1-5]\d{4}|677189)\d{10})|(2(?:2(?:2[1-9]|[3-9]\d)|[3-6]\d\d|7(?:[01]\d|20))-?\d{4}-?\d{4}-?\d{4})$/',
        CreditCard::DISCOVER           => '/^6(?:011\d{12}|5\d{14}|4[4-9]\d{13}|22(?:1(?:2[6-9]|[3-9]\d)|[2-8]\d{2}|9(?:[01]\d|2[0-5]))\d{10})$/',
        CreditCard::UNIONPAY           => '/^62[0-9]\d{13,16}$/',
        CreditCard::AMEX               => '/^3[47]\d{13}$/',
        CreditCard::DINERS_CLUB        => '/^(3((?:0[0-5])|(?:09)|(?:6[0-9])|(?:8[0-9])|(?:9[0-9]))\d{11}|2014\d{11}|2149\d{11})$/',
        CreditCard::JCB                => '/^35(28|29|[3-8]\d)\d{12}$/',
        CreditCard::DANKORT            => '/^5019\d{12}$/',
        CreditCard::MAESTRO            => '/(?:5018|5020|5038|5612|5892|5893|6304|6759|6761|6762|6763|0604|6390)[0-9]{8,15}/',
        CreditCard::FORBRUGSFORENINGEN => '/^600722\d{10}$/',
        CreditCard::LASER              => '/^(6706|6771|6709)\d{8}(\d{4}|\d{6,7})?$/',
        CreditCard::SWITCH_BRAND       => '/^6759\d{12}(\d{2,3})?$/',
        CreditCard::SOLO               => '/^6767\d{12}(\d{2,3})?$/',

    );

    /**
     * Detect card brand from card number
     *
     * @param string $number The card number to detect.
     * @return mixed|false Card name on success or false if not.
     */
    public function detect($number)
    {
        foreach (self::$cardPatterns as $name => $pattern) {
            if (preg_match($pattern, $number)) {
                return $name;
            }
        }

        return false;
    }
}
