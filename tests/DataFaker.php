<?php

namespace Omnipay\VindiciaTest;

use DateTime;
use InvalidArgumentException;
use Omnipay\Vindicia\VindiciaItem;
use Omnipay\Vindicia\VindiciaItemBag;
use Omnipay\Vindicia\VindiciaRefundItem;
use Omnipay\Vindicia\VindiciaRefundItemBag;
use Omnipay\Vindicia\Attribute;
use Omnipay\Vindicia\AttributeBag;
use Omnipay\Vindicia\TaxExemption;
use Omnipay\Vindicia\TaxExemptionBag;
use Omnipay\Vindicia\Price;
use Omnipay\Vindicia\PriceBag;
use Omnipay\Common\Currency;

/**
 * Generates fake data for use in test cases. Perhaps one day Omnipay
 * could start using something like this or
 * https://github.com/fzaninotto/Faker
 */
class DataFaker
{
    const HEX_CHARACTERS = '0123456789abcdef';
    const ALPHABET_LOWER = 'abcdefghijklmnopqrstuvwxyz';
    const ALPHABET_UPPER = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    const DIGITS = '0123456789';

    public function __construct()
    {
        mt_srand();
        date_default_timezone_set('Europe/London');
    }

    /**
     * Returns an int between $min and $max, inclusive
     *
     * @param int $min
     * @param int $max
     * @return int
     */
    public function intBetween($min, $max)
    {
        return mt_rand($min, $max);
    }

    /**
     * Returns a bool
     *
     * @return bool
     */
    public function bool()
    {
        return mt_rand(0, 1) ? true : false;
    }

    /**
     * Returns a monetary amount that is valid in the provided currency
     *
     * @param string $currency
     * @return string
     */
    public function monetaryAmount($currency)
    {
        $currency = Currency::find($currency);
        if ($currency === null) {
            throw new InvalidArgumentException('This currency is not supported.');
        }

        $currencyPrecision = $currency->getDecimals();

        $integerComponent = strval($this->intBetween($currencyPrecision > 0 ? 0 : 1, 999));

        if ($currencyPrecision === 0) {
            return $integerComponent;
        }

        $decimalComponent = $this->intBetween($integerComponent > 0 ? 0 : 1, intval(str_repeat(9, $currencyPrecision)));
        $decimalComponent = str_pad($decimalComponent, $currencyPrecision, '0', STR_PAD_LEFT);

        return $integerComponent . '.' . $decimalComponent;
    }

    /**
     * Return a three letter currency code
     *
     * @return string
     */
    public function currency()
    {
        $currencies = array_keys(Currency::all());
        return $currencies[$this->intBetween(0, count($currencies) - 1)];
    }

    /**
     * Return a customer id
     *
     * @return string
     */
    public function customerId()
    {
        do {
            $result = $this->randomCharacters(self::HEX_CHARACTERS, $this->intBetween(3, 9));
        } while (!$result);
        return $result;
    }

    /**
     * Return a customer reference
     *
     * @return string
     */
    public function customerReference()
    {
        do {
            $result = $this->randomCharacters(self::HEX_CHARACTERS, $this->intBetween(9, 12));
        } while (!$result);
        return $result;
    }

    /**
     * Return a card id
     *
     * @return string
     */
    public function paymentMethodId()
    {
        do {
            $result = $this->randomCharacters(self::HEX_CHARACTERS, $this->intBetween(6, 9));
        } while (!$result);
        return $result;
    }

    /**
     * Return a card reference
     *
     * @return string
     */
    public function paymentMethodReference()
    {
        do {
            $result = $this->randomCharacters(self::HEX_CHARACTERS, $this->intBetween(9, 12));
        } while (!$result);
        return $result;
    }

    /**
     * Return a plan id
     *
     * @return string
     */
    public function planId()
    {
        return strval($this->intBetween(1, 999));
    }

    /**
     * Return a plan reference
     *
     * @return string
     */
    public function planReference()
    {
        do {
            $result = $this->randomCharacters(self::HEX_CHARACTERS, $this->intBetween(9, 12));
        } while (!$result);
        return $result;
    }

    /**
     * Return a subscription id
     *
     * @return string
     */
    public function subscriptionId()
    {
        return strval($this->intBetween(1, 99999999));
    }

    /**
     * Return a subscription reference
     *
     * @return string
     */
    public function subscriptionReference()
    {
        do {
            $result = $this->randomCharacters(self::HEX_CHARACTERS, $this->intBetween(9, 12));
        } while (!$result);
        return $result;
    }

    /**
     * Return a product id
     *
     * @return string
     */
    public function productId()
    {
        return strval($this->intBetween(1, 99999999));
    }

    /**
     * Return a product reference
     *
     * @return string
     */
    public function productReference()
    {
        do {
            $result = $this->randomCharacters(self::HEX_CHARACTERS, $this->intBetween(9, 12));
        } while (!$result);
        return $result;
    }

    /**
     * Return a billing interval
     *
     * @return string
     */
    public function billingInterval()
    {
        switch ($this->intBetween(0, 3)) {
            case 0:
                return 'day';

            case 1:
                return 'week';

            case 2:
                return 'month';

            default:
                return 'year';
        }
    }

    /**
     * Return a billing interval count
     *
     * @return string
     */
    public function billingIntervalCount()
    {
        return $this->intBetween(1, 24);
    }

    /**
     * Return a statement descritpor
     *
     * @return string
     */
    public function statementDescriptor()
    {
        return $this->randomCharacters(self::ALPHABET_UPPER . self::ALPHABET_LOWER . ' ', $this->intBetween(6, 20));
    }

    /**
     * Return a HOA web session reference
     *
     * @return string
     */
    public function webSessionReference()
    {
        do {
            $result = $this->randomCharacters(self::HEX_CHARACTERS, $this->intBetween(9, 12));
        } while (!$result);
        return $result;
    }

    /**
     * Return a transaction id
     *
     * @return string
     */
    public function transactionId()
    {
        return $this->randomCharacters(self::ALPHABET_UPPER, $this->intBetween(2, 4)) . $this->randomCharacters(self::DIGITS, $this->intBetween(6, 9));
    }

    /**
     * Return a transaction reference
     *
     * @return string
     */
    public function transactionReference()
    {
        do {
            $result = $this->randomCharacters(self::HEX_CHARACTERS, $this->intBetween(9, 12));
        } while (!$result);
        return $result;
    }

    /**
     * Return a refund id
     *
     * @return string
     */
    public function refundId()
    {
        return $this->transactionId();
    }

    /**
     * Return a refund reference
     *
     * @return string
     */
    public function refundReference()
    {
        return $this->transactionReference();
    }

    /**
     * Return a timeout time in seconds
     *
     * @return int
     */
    public function timeout()
    {
        return $this->intBetween(1, 999);
    }

    /**
     * Return a timestamp
     *
     * @return int
     */
    public function timestamp()
    {
        $year = $this->intBetween(2000, 2100);
        $month = $this->intBetween(1, 12);
        if ($month < 10) {
            $month = '0' . $month;
        }
        $day = $this->intBetween(1, 28);
        if ($day < 10) {
            $day = '0' . $day;
        }
        $hour = $this->intBetween(0, 23);
        if ($hour < 10) {
            $hour = '0' . $hour;
        }
        $minute = $this->intBetween(0, 59);
        if ($minute < 10) {
            $minute = '0' . $minute;
        }
        $second = $this->intBetween(0, 59);
        if ($second < 10) {
            $second = '0' . $second;
        }
        $timezone = $this->intBetween(0, 12);
        if ($timezone < 10) {
            $timezone = '0' . $timezone;
        }
        if ($timezone != '00' && $this->bool()) {
            $timezone = '-' . $timezone;
        } else {
            $timezone = '+' . $timezone;
        }
        $timezone .= ':00';

        return $year . '-' . $month . '-' . $day . 'T' . $hour . ':' . $minute . ':' . $second;
    }

    /**
     * Return a chargeback probability
     *
     * @return int
     */
    public function chargebackProbability()
    {
        return $this->intBetween(0, 100);
    }

    /**
     * Return a fake card
     *
     * @return array of string
     */
    public function card()
    {
        $now = new DateTime();
        $now2 = clone $now;
        return array(
            'number' => $this->intBetween(0, 1) ? '4242424242424242' : '5555555555554444',
            'expiryMonth' => strval($this->intBetween(1, 12)),
            'expiryYear' => strval($this->intBetween(
                $now->modify('+1 year')->format('Y'),
                $now2->modify('+50 year')->format('Y')
            )),
            'cvv' => strval($this->intBetween(100, 999)),
            'country' => $this->region(),
            'postcode' => $this->postcode()
        );
    }

    /**
     * Return a fake card that has an invalid number or expiration date
     *
     * @return array of string
     */
    public function invalidCard()
    {
        $now = new DateTime();
        $now2 = clone $now;
        $card = $this->card();
        switch ($this->intBetween(0, 2)) {
            case 0:
                $card['number'] = '5555555555554443';
                break;
            case 1:
                $card['number'] = '4242424242424243';
                break;
            default:
                $card['expiryYear'] = strval($this->intBetween(
                    $now->modify('-15 year')->format('Y'),
                    $now2->modify('-1 year')->format('Y')
                ));
                break;
        }

        return $card;
    }

    /**
     * Return a name (first and last)
     *
     * @return string
     */
    public function name()
    {
        return ucfirst($this->randomCharacters(self::ALPHABET_LOWER, $this->intBetween(3, 10)))
            . ' '
            . ucfirst($this->randomCharacters(self::ALPHABET_LOWER, $this->intBetween(3, 10)));
    }

    /**
     * Return an ip address
     *
     * @return string
     */
    public function ipAddress()
    {
        return implode('.', array(
            $this->intBetween(0, 255),
            $this->intBetween(0, 255),
            $this->intBetween(0, 255),
            $this->intBetween(0, 255)
        ));
    }

    /**
     * Return an email address
     *
     * @return string
     */
    public function email()
    {
        return $this->randomCharacters(self::DIGITS . self::ALPHABET_LOWER, $this->intBetween(3, 10))
                                                    . '@example.'
                                                    . $this->topLevelDomain();
    }

    /**
     * Return a url
     *
     * @return string
     */
    public function url()
    {
        return 'http://www.example.' . $this->topLevelDomain() . '/' . $this->randomCharacters(self::DIGITS . self::ALPHABET_LOWER . '%', $this->intBetween(0, 10));
    }

    protected function topLevelDomain()
    {
        switch ($this->intBetween(0, 3)) {
            case 0:
                return 'com';

            case 1:
                return 'org';

            case 2:
                return 'net';

            default:
                return 'edu';
        }
    }

    /**
     * Return an item, either as a VindiciaItem (default) or as an
     * array, if $asArray is set to true.
     *
     * @param string $currency
     * @param bool $asArray default false
     * @return VindiciaItem|array
     */
    public function item($currency, $asArray = false)
    {
        $params = array(
            'name' => $this->randomCharacters(
                DataFaker::ALPHABET_LOWER,
                $this->intBetween(3, 15)
            ),
            'description' => $this->randomCharacters(
                DataFaker::ALPHABET_LOWER,
                $this->intBetween(3, 30)
            ),
            'quantity' => $this->intBetween(1, 15),
            'price' => $this->monetaryAmount($currency),
            'sku' => $this->sku()
        );

        if ($asArray) {
            return $params;
        }
        return new VindiciaItem($params);
    }

    /**
     * Return some items, either as a VindiciaItemBag (default) or as an
     * array, if $asArray is set to true.
     *
     * @param string $currency
     * @param bool $asArray default false
     * @return VindiciaItemBag|array
     */
    public function items($currency, $asArray = false)
    {
        $items = $asArray ? array() : new VindiciaItemBag();
        for ($i = 0; $i < $this->intBetween(1, 5); $i++) {
            if ($asArray) {
                $items[] = $this->item($currency, true);
            } else {
                $items->add($this->item($currency));
            }
        }
        return $items;
    }

    /**
     * Return an attribute, either as an Attribute (default) or as an
     * array, if $asArray is set to true.
     *
     * @param bool $asArray default false
     * @return Attribute|array
     */
    public function attribute($asArray = false)
    {
        $params = array(
            'name' => $this->randomCharacters(
                DataFaker::ALPHABET_LOWER,
                $this->intBetween(3, 15)
            ),
            'value' => $this->randomCharacters(
                DataFaker::ALPHABET_LOWER . DataFaker::DIGITS,
                $this->intBetween(1, 3)
            )
        );

        if ($asArray) {
            return $params;
        }
        return new Attribute($params);
    }

    /**
     * Return some attributes, either as an AttributeBag (default) or as an
     * array, if $asArray is set to true.
     *
     * @param bool $asArray default false
     * @return AttributeBag|array
     */
    public function attributes($asArray = false)
    {
        $attributes = $asArray ? array() : new AttributeBag();
        for ($i = 0; $i < $this->intBetween(1, 5); $i++) {
            if ($asArray) {
                $attributes[] = $this->attribute(true);
            } else {
                $attributes->add($this->attribute());
            }
        }
        return $attributes;
    }

    /**
     * Return a price, either as a Price (default) or as an
     * array, if $asArray is set to true.
     *
     * @param bool $asArray default false
     * @return Price|array
     */
    public function price($asArray = false)
    {
        $currency = $this->currency();
        $params = array(
            'currency' => $currency,
            'amount' => $this->monetaryAmount($currency)
        );

        if ($asArray) {
            return $params;
        }
        return new Price($params);
    }

    /**
     * Return some prices, either as a PriceBag (default) or as an
     * array, if $asArray is set to true.
     *
     * @param bool $asArray default false
     * @return PriceBag|array
     */
    public function prices($asArray = false)
    {
        $currencies = array_keys(Currency::all());
        $prices = $asArray ? array() : new PriceBag();
        for ($i = 0; $i < $this->intBetween(1, count($currencies) - 1); $i++) {
            $currency = $currencies[$i];
            if ($asArray) {
                $prices[] = array(
                    'currency' => $currency,
                    'amount' => $this->monetaryAmount($currency)
                );
            } else {
                $prices->add(new Price(array(
                    'currency' => $currency,
                    'amount' => $this->monetaryAmount($currency)
                )));
            }
        }
        return $prices;
    }

    /**
     * Return a refund item, either as a VindiciaRefundItem (default) or as an
     * array, if $asArray is set to true.
     *
     * @param string $currency
     * @param bool $asArray default false
     * @return VindiciaRefundItem|array
     */
    public function refundItem($currency, $asArray = false)
    {
        $params = array(
            'amount' => $this->monetaryAmount($currency),
            'sku' => $this->sku(),
            'transactionItemIndexNumber' => $this->intBetween(1, 15)
        );

        if ($asArray) {
            return $params;
        }
        return new VindiciaRefundItem($params);
    }

    /**
     * Return some refund items, either as a VindiciaRefundItemBag (default) or as an
     * array, if $asArray is set to true.
     *
     * @param string $currency
     * @param bool $asArray default false
     * @return VindiciaRefundItemBag|array
     */
    public function refundItems($currency, $asArray = false)
    {
        $items = $asArray ? array() : new VindiciaRefundItemBag();
        for ($i = 0; $i < $this->intBetween(1, 5); $i++) {
            if ($asArray) {
                $items[] = $this->refundItem($currency, true);
            } else {
                $items->add($this->refundItem($currency));
            }
        }
        return $items;
    }

    /**
     * Return a sku
     *
     * @return string
     */
    public function sku()
    {
        return strval($this->intBetween(1, 99999));
    }

    /**
     * Return a tax classification
     *
     * @return string
     */
    public function taxClassification()
    {
        return $this->randomCharacters(self::ALPHABET_UPPER . self::ALPHABET_LOWER, $this->intBetween(8, 16));
    }

    /**
     * Return a two-letter region (eg country or state) code
     *
     * @return string
     */
    public function region()
    {
        return $this->randomCharacters(self::ALPHABET_UPPER, 2);
    }

    /**
     * Return a 5 digit postal code
     *
     * @return string
     */
    public function postcode()
    {
        do {
            $result = $this->randomCharacters(self::DIGITS, 5);
        } while (!$result);
        return $result;
    }

    /**
     * Return a tax exemption id
     *
     * @return string
     */
    public function taxExemptionId()
    {
        return $this->randomCharacters(self::ALPHABET_UPPER . self::DIGITS, $this->intBetween(6, 12));
    }

    /**
     * Return a tax exemption, either as a TaxExemption (default) or as an
     * array, if $asArray is set to true.
     *
     * @param bool $asArray default false
     * @return TaxExemption|array
     */
    public function taxExemption($asArray = false)
    {
        $params = array(
            'exemptionId' => $this->taxExemptionId(),
            'region' => $this->region(),
            'active' => $this->bool()
        );

        if ($asArray) {
            return $params;
        }
        return new TaxExemption($params);
    }

    /**
     * Return some tax exemptions, either as a TaxExemptionBag (default) or as an
     * array, if $asArray is set to true.
     *
     * @param bool $asArray default false
     * @return TaxExemptionBag|array
     */
    public function taxExemptions($asArray = false)
    {
        $exemptions = $asArray ? array() : new TaxExemptionBag();
        for ($i = 0; $i < $this->intBetween(1, 5); $i++) {
            if ($asArray) {
                $exemptions[] = $this->taxExemption(true);
            } else {
                $exemptions->add($this->taxExemption());
            }
        }
        return $exemptions;
    }

    /**
     * Return a string indicating the behavior when creating a duplicate object
     *
     * @return string
     */
    public function duplicateBehavior()
    {
        switch ($this->intBetween(0, 2)) {
            case 0:
                return 'Fail';

            case 1:
                return 'Duplicate';

            default:
                return 'SucceedIgnore';
        }
    }

    /**
     * Return a string of random characters from $characterSet that is
     * $numCharacters long
     *
     * @param string $characterSet
     * @param int $numCharacters
     * @return string
     */
    public function randomCharacters($characterSet, $numCharacters)
    {
        if ($numCharacters < 0) {
            throw new InvalidArgumentException('Parameter numCharacters must be positive or zero, saw ' . $numCharacters);
        }
        if (empty($characterSet)) {
            throw new InvalidArgumentException('characterSet must not be empty');
        }

        $result = '';
        $setLength = strlen($characterSet);
        for ($i = 0; $i < $numCharacters; $i++) {
            $result .= $characterSet[$this->intBetween(0, $setLength - 1)];
        }

        return $result;
    }

    public function username()
    {
        return $this->randomCharacters(self::ALPHABET_LOWER, $this->intBetween(3, 10));
    }

    public function password()
    {
        return $this->randomCharacters(self::ALPHABET_LOWER . self::DIGITS, $this->intBetween(8, 16));
    }

    /**
     * Return a PayPal transaction reference
     *
     * @return string
     */
    public function payPalTransactionReference()
    {
        do {
            $result = $this->randomCharacters(self::HEX_CHARACTERS, 40);
        } while (!$result);
        return $result;
    }

    /**
     * Return a PayPal token
     */
    public function payPalToken()
    {
        return 'EC-' . $this->randomCharacters(self::DIGITS . self::ALPHABET_UPPER, 17);
    }

    /**
     * Return a PayPal customer reference
     *
     * @return string
     */
    public function payPalCustomerReference()
    {
        return 'B-' . $this->randomCharacters(self::DIGITS . self::ALPHABET_UPPER, 17);
    }
}
