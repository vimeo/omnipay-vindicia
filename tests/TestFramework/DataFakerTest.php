<?php

namespace Omnipay\Vindicia\TestFramework;

use Omnipay\Tests\TestCase;
use Omnipay\Common\Currency;
use Omnipay\Common\CreditCard;
use Omnipay\Vindicia\VindiciaItem;
use Omnipay\Vindicia\VindiciaItemBag;
use Omnipay\Vindicia\Attribute;
use Omnipay\Vindicia\AttributeBag;
use Omnipay\Vindicia\Price;
use Omnipay\Vindicia\PriceBag;
use Omnipay\Vindicia\VindiciaRefundItem;
use Omnipay\Vindicia\VindiciaRefundItemBag;
use Omnipay\Vindicia\TaxExemption;
use Omnipay\Vindicia\TaxExemptionBag;

class DataFakerTest extends TestCase
{
    /**
     * @return void
     */
    public function setUp()
    {
        $this->faker = new DataFaker();
    }

    /**
     * @return void
     */
    public function testIntBetween()
    {
        $int = $this->faker->intBetween(-3, 3);
        $this->assertTrue(is_int($int));
        $this->assertTrue(-3 <= $int);
        $this->assertTrue(3 >= $int);

        $int = $this->faker->intBetween(100, 200);
        $this->assertTrue(is_int($int));
        $this->assertTrue(100 <= $int);
        $this->assertTrue(200 >= $int);
    }

    /**
     * @return void
     */
    public function testBool()
    {
        $this->assertTrue(is_bool($this->faker->bool()));
    }

    /**
     * @return void
     */
    public function testMonetaryAmount()
    {
        $amount = $this->faker->monetaryAmount('USD');
        $this->assertTrue(is_string($amount));
        $this->assertEquals($amount, strval(floatval($amount)));
        $this->assertTrue($amount > 0);
        $parts = explode('.', $amount);
        $this->assertSame(2, count($parts));
        $this->assertSame(2, strlen($parts[1]));

        $amount = $this->faker->monetaryAmount('KRW');
        $this->assertTrue(is_string($amount));
        $this->assertTrue(ctype_digit($amount));
        $this->assertTrue($amount > 0);
    }

    /**
     * @return void
     */
    public function testCurrency()
    {
        $this->assertNotNull(Currency::find($this->faker->currency()));
    }

    /**
     * @return void
     */
    public function testCustomerId()
    {
        $id = $this->faker->customerId();
        $this->assertTrue(is_string($id));
        $this->assertTrue(strlen($id) > 0);
        $this->assertNotEquals(0, $id);
    }

    /**
     * @return void
     */
    public function testCustomerReference()
    {
        $reference = $this->faker->customerReference();
        $this->assertTrue(is_string($reference));
        $this->assertTrue(strlen($reference) > 0);
        $this->assertNotEquals(0, $reference);
    }

    /**
     * @return void
     */
    public function testPaymentMethodId()
    {
        $id = $this->faker->paymentMethodId();
        $this->assertTrue(is_string($id));
        $this->assertTrue(strlen($id) > 0);
        $this->assertNotEquals(0, $id);
    }

    /**
     * @return void
     */
    public function testPaymentMethodReference()
    {
        $reference = $this->faker->paymentMethodReference();
        $this->assertTrue(is_string($reference));
        $this->assertTrue(strlen($reference) > 0);
        $this->assertNotEquals(0, $reference);
    }

    /**
     * @return void
     */
    public function testPlanId()
    {
        $id = $this->faker->planId();
        $this->assertTrue(is_string($id));
        $this->assertTrue(strlen($id) > 0);
        $this->assertNotEquals(0, $id);
    }

    /**
     * @return void
     */
    public function testPlanReference()
    {
        $reference = $this->faker->planReference();
        $this->assertTrue(is_string($reference));
        $this->assertTrue(strlen($reference) > 0);
        $this->assertNotEquals(0, $reference);
    }

    /**
     * @return void
     */
    public function testSubscriptionId()
    {
        $id = $this->faker->subscriptionId();
        $this->assertTrue(is_string($id));
        $this->assertTrue(strlen($id) > 0);
        $this->assertNotEquals(0, $id);
    }

    /**
     * @return void
     */
    public function testSubscriptionReference()
    {
        $reference = $this->faker->subscriptionReference();
        $this->assertTrue(is_string($reference));
        $this->assertTrue(strlen($reference) > 0);
        $this->assertNotEquals(0, $reference);
    }

    /**
     * @return void
     */
    public function testProductId()
    {
        $id = $this->faker->productId();
        $this->assertTrue(is_string($id));
        $this->assertTrue(strlen($id) > 0);
        $this->assertNotEquals(0, $id);
    }

    /**
     * @return void
     */
    public function testProductReference()
    {
        $reference = $this->faker->productReference();
        $this->assertTrue(is_string($reference));
        $this->assertTrue(strlen($reference) > 0);
        $this->assertNotEquals(0, $reference);
    }

    /**
     * @return void
     */
    public function testBillingInterval()
    {
        $this->assertTrue(in_array($this->faker->billingInterval(), array('day', 'week', 'month', 'year'), true));
    }

    /**
     * @return void
     */
    public function testBillingIntervalCount()
    {
        $count = $this->faker->billingIntervalCount();
        $this->assertTrue(is_int($count));
        $this->assertTrue($count > 0);
    }

    /**
     * @return void
     */
    public function testStatementDescriptor()
    {
        $descriptor = $this->faker->statementDescriptor();
        $this->assertTrue(is_string($descriptor));
        $this->assertTrue(strlen($descriptor) > 0);
    }

    /**
     * @return void
     */
    public function testWebSessionReference()
    {
        $reference = $this->faker->webSessionReference();
        $this->assertTrue(is_string($reference));
        $this->assertTrue(strlen($reference) > 0);
        $this->assertNotEquals(0, $reference);
    }

    /**
     * @return void
     */
    public function testTransactionId()
    {
        $id = $this->faker->transactionId();
        $this->assertTrue(is_string($id));
        $this->assertTrue(strlen($id) > 0);
        $this->assertNotEquals(0, $id);
    }

    /**
     * @return void
     */
    public function testTransactionReference()
    {
        $reference = $this->faker->transactionReference();
        $this->assertTrue(is_string($reference));
        $this->assertTrue(strlen($reference) > 0);
        $this->assertNotEquals(0, $reference);
    }

    /**
     * @return void
     */
    public function testRefundId()
    {
        $id = $this->faker->refundId();
        $this->assertTrue(is_string($id));
        $this->assertTrue(strlen($id) > 0);
        $this->assertNotEquals(0, $id);
    }

    /**
     * @return void
     */
    public function testRefundReference()
    {
        $reference = $this->faker->refundReference();
        $this->assertTrue(is_string($reference));
        $this->assertTrue(strlen($reference) > 0);
        $this->assertNotEquals(0, $reference);
    }

    /**
     * @return void
     */
    public function testChargebackId()
    {
        $id = $this->faker->chargebackId();
        $this->assertTrue(is_string($id));
        $this->assertTrue(strlen($id) > 0);
        $this->assertNotEquals(0, $id);
    }

    /**
     * @return void
     */
    public function testChargebackReference()
    {
        $reference = $this->faker->chargebackReference();
        $this->assertTrue(is_string($reference));
        $this->assertTrue(strlen($reference) > 0);
        $this->assertNotEquals(0, $reference);
    }

    /**
     * @return void
     */
    public function testTimeout()
    {
        $timeout = $this->faker->timeout();
        $this->assertTrue(is_int($timeout));
        $this->assertTrue($timeout > 0);
    }

    /**
     * @return void
     */
    public function testTimestamp()
    {
        $timestamp = $this->faker->timestamp();
        $this->assertTrue(is_string($timestamp));
        $this->assertTrue(strpos($timestamp, 'T') !== false);
        $this->assertTrue(strtotime($timestamp) !== false);
    }

    /**
     * @return void
     */
    public function testChargebackProbability()
    {
        $chargebackProbability = $this->faker->chargebackProbability();
        $this->assertTrue(is_int($chargebackProbability));
        $this->assertTrue($chargebackProbability >= 0);
        $this->assertTrue($chargebackProbability <= 100);
    }

    /**
     * @return void
     */
    public function testCard()
    {
        $card = new CreditCard($this->faker->card());
        $this->assertNull($card->validate());
    }

    /**
     * @expectedException Omnipay\Common\Exception\InvalidCreditCardException
     * @return            void
     */
    public function testInvalidCard()
    {
        $card = new CreditCard($this->faker->invalidCard());
        $card->validate();
    }

    /**
     * @return void
     */
    public function testName()
    {
        $name = $this->faker->name();
        $this->assertTrue(is_string($name));

        $parts = explode(' ', $name);
        $this->assertSame(2, count($parts));
    }

    /**
     * @return void
     */
    public function testIpAddress()
    {
        $this->assertTrue(ip2long($this->faker->ipAddress()) !== false);
    }

    /**
     * @return void
     */
    public function testEmail()
    {
        $email = $this->faker->email();
        $this->assertTrue(is_string($email));

        $parts = explode('@example.', $email);
        $this->assertSame(2, count($parts));
        $this->assertTrue(0 < strlen($parts[0]));
        $this->assertSame(3, strlen($parts[1]));
    }

    /**
     * @return void
     */
    public function testUrl()
    {
        $url = $this->faker->url();
        $this->assertTrue(is_string($url));
        $this->assertSame(0, strpos($url, 'http://www.example.'));
        $this->assertTrue(strlen('http://www.example.') + 3 <= strlen($url));
    }

    /**
     * @return void
     */
    public function testItem()
    {
        $item = $this->faker->item($this->faker->currency());
        $this->assertInstanceOf('Omnipay\Vindicia\VindiciaItem', $item);
        $item->validate();
        $this->assertTrue(is_string($item->getName()));
        $this->assertTrue(is_string($item->getDescription()));
        $this->assertTrue(is_int($item->getQuantity()));
        $this->assertTrue(is_numeric($item->getPrice()));
        $this->assertTrue(is_string($item->getSku()));
    }

    /**
     * @return void
     */
    public function testItemAsArray()
    {
        $itemArray = $this->faker->itemAsArray($this->faker->currency());
        $item = new VindiciaItem($itemArray);
        $item->validate();
        $this->assertTrue(is_string($item->getName()));
        $this->assertTrue(is_string($item->getDescription()));
        $this->assertTrue(is_int($item->getQuantity()));
        $this->assertTrue(is_numeric($item->getPrice()));
        $this->assertTrue(is_string($item->getSku()));
    }

    /**
     * @return void
     */
    public function testItems()
    {
        $items = $this->faker->items($this->faker->currency());
        $this->assertInstanceOf('Omnipay\Vindicia\VindiciaItemBag', $items);
        $this->assertTrue(0 < $items->count());
    }

    /**
     * @return void
     */
    public function testItemsAsArray()
    {
        $itemsArray = $this->faker->itemsAsArray($this->faker->currency());
        $items = new VindiciaItemBag($itemsArray);
        $this->assertTrue(0 < $items->count());
    }

    /**
     * @return void
     */
    public function testAttribute()
    {
        $attribute = $this->faker->attribute();
        $this->assertInstanceOf('Omnipay\Vindicia\Attribute', $attribute);
        $this->assertTrue(is_string($attribute->getName()));
        $this->assertTrue(is_string($attribute->getValue()));
    }

    /**
     * @return void
     */
    public function testAttributeAsArray()
    {
        $attributeArray = $this->faker->attributeAsArray();
        $attribute = new Attribute($attributeArray);
        $this->assertTrue(is_string($attribute->getName()));
        $this->assertTrue(is_string($attribute->getValue()));
    }

    /**
     * @return void
     */
    public function testAttributes()
    {
        $attributes = $this->faker->attributes();
        $this->assertInstanceOf('Omnipay\Vindicia\AttributeBag', $attributes);
        $this->assertTrue(0 < $attributes->count());
    }

    /**
     * @return void
     */
    public function testAttributesAsArray()
    {
        $attributesArray = $this->faker->attributesAsArray();
        $attributes = new AttributeBag($attributesArray);
        $this->assertTrue(0 < $attributes->count());
    }

    /**
     * @return void
     */
    public function testPrice()
    {
        $price = $this->faker->price();
        $this->assertInstanceOf('Omnipay\Vindicia\Price', $price);
        $this->assertTrue(Currency::find($price->getCurrency()) !== false);
        $this->assertTrue(is_numeric($price->getAmount()));
    }

    /**
     * @return void
     */
    public function testPriceAsArray()
    {
        $priceArray = $this->faker->priceAsArray();
        $price = new Price($priceArray);
        $currency = $price->getCurrency();
        $this->assertNotNull($currency);
        if ($currency !== null) {
            $this->assertTrue(Currency::find($currency) !== false);
        }
        $this->assertTrue(is_numeric($price->getAmount()));
    }

    /**
     * @return void
     */
    public function testPrices()
    {
        $prices = $this->faker->prices();
        $this->assertInstanceOf('Omnipay\Vindicia\PriceBag', $prices);
        $this->assertTrue(0 < $prices->count());
    }

    /**
     * @return void
     */
    public function testPricesAsArray()
    {
        $pricesArray = $this->faker->pricesAsArray();
        $prices = new PriceBag($pricesArray);
        $this->assertTrue(0 < $prices->count());
    }

    /**
     * @return void
     */
    public function testRefundItem()
    {
        $item = $this->faker->refundItem($this->faker->currency());
        $this->assertInstanceOf('Omnipay\Vindicia\VindiciaRefundItem', $item);
        $item->validate();
        $this->assertTrue(is_int($item->getTransactionItemIndexNumber()));
        $this->assertTrue(is_numeric($item->getAmount()));
        $this->assertTrue(is_string($item->getSku()));
    }

    /**
     * @return void
     */
    public function testRefundItemAsArray()
    {
        $itemArray = $this->faker->refundItemAsArray($this->faker->currency());
        $item = new VindiciaRefundItem($itemArray);
        $item->validate();
        $this->assertTrue(is_int($item->getTransactionItemIndexNumber()));
        $this->assertTrue(is_numeric($item->getAmount()));
        $this->assertTrue(is_string($item->getSku()));
    }

    /**
     * @return void
     */
    public function testRefundItems()
    {
        $items = $this->faker->refundItems($this->faker->currency());
        $this->assertInstanceOf('Omnipay\Vindicia\VindiciaRefundItemBag', $items);
        $this->assertTrue(0 < $items->count());
    }

    /**
     * @return void
     */
    public function testRefundItemsAsarray()
    {
        $itemsArray = $this->faker->refundItemsAsArray($this->faker->currency());
        $items = new VindiciaItemBag($itemsArray);
        $this->assertTrue(0 < $items->count());
    }

    /**
     * @return void
     */
    public function testSku()
    {
        $sku = $this->faker->sku();
        $this->assertTrue(is_string($sku));
        $this->assertTrue(strlen($sku) > 0);
    }

    /**
     * @return void
     */
    public function testTaxClassification()
    {
        $taxClassification = $this->faker->taxClassification();
        $this->assertTrue(is_string($taxClassification));
        $this->assertTrue(strlen($taxClassification) > 0);
    }

    /**
     * @return void
     */
    public function testRegion()
    {
        $region = $this->faker->region();
        $this->assertTrue(is_string($region));
        $this->assertSame(2, strlen($region));
        $this->assertTrue(ctype_upper($region));
    }

    /**
     * @return void
     */
    public function testPostcode()
    {
        $postcode = $this->faker->postcode();
        $this->assertTrue(is_string($postcode));
        $this->assertSame(5, strlen($postcode));
        $this->assertTrue(ctype_digit($postcode));
    }

    /**
     * @return void
     */
    public function testTaxExemptionId()
    {
        $taxExemptionId = $this->faker->taxExemptionId();
        $this->assertTrue(is_string($taxExemptionId));
        $this->assertTrue(strlen($taxExemptionId) > 0);
        $this->assertNotEquals(0, $taxExemptionId);
    }

    /**
     * @return void
     */
    public function testTaxExemption()
    {
        $taxExemption = $this->faker->taxExemption();
        $this->assertInstanceOf('Omnipay\Vindicia\TaxExemption', $taxExemption);
        $this->assertTrue(is_string($taxExemption->getExemptionId()));
        $this->assertTrue(ctype_upper($taxExemption->getRegion()));
        $this->assertSame(2, strlen($taxExemption->getRegion()));
        $this->assertTrue(is_bool($taxExemption->getActive()));
    }

    /**
     * @return void
     */
    public function testTaxExemptionAsArray()
    {
        $taxExemptionArray = $this->faker->taxExemptionAsArray();
        $taxExemption = new TaxExemption($taxExemptionArray);
        $this->assertTrue(is_string($taxExemption->getExemptionId()));
        $this->assertTrue(ctype_upper($taxExemption->getRegion()));
        $this->assertSame(2, strlen($taxExemption->getRegion()));
        $this->assertTrue(is_bool($taxExemption->getActive()));
    }

    /**
     * @return void
     */
    public function testTaxExemptions()
    {
        $taxExemptions = $this->faker->taxExemptions();
        $this->assertInstanceOf('Omnipay\Vindicia\TaxExemptionBag', $taxExemptions);
        $this->assertTrue(0 < $taxExemptions->count());
    }

    /**
     * @return void
     */
    public function testTaxExemptionsAsArray()
    {
        $taxExemptionsArray = $this->faker->taxExemptionsAsArray();
        $taxExemptions = new TaxExemptionBag($taxExemptionsArray);
        $this->assertTrue(0 < $taxExemptions->count());
    }

    /**
     * @return void
     */
    public function testDuplicateBehavior()
    {
        $this->assertTrue(in_array($this->faker->duplicateBehavior(), array('Fail', 'Duplicate', 'SucceedIgnore'), true));
    }

    /**
     * @return void
     */
    public function testRandomCharacters()
    {
        $length = $this->faker->intBetween(1, 100);
        $chars = $this->faker->randomCharacters('a', $length);
        $this->assertTrue(is_string($chars));
        $this->assertSame($length, strlen($chars));
        $this->assertSame(str_repeat('a', $length), $chars);

        $chars = $this->faker->randomCharacters('abc', $length);
        $this->assertSame($length, strlen($chars));
        $chars_array = str_split($chars);
        foreach ($chars_array as $char) {
            $this->assertTrue(in_array($char, array('a', 'b', 'c')));
        }
    }

    /**
     * @return void
     */
    public function testUsername()
    {
        $username = $this->faker->username();
        $this->assertTrue(is_string($username));
        $this->assertTrue(strlen($username) > 0);
    }

    /**
     * @return void
     */
    public function testPassword()
    {
        $password = $this->faker->password();
        $this->assertTrue(is_string($password));
        $this->assertTrue(strlen($password) > 0);
    }

    /**
     * @return void
     */
    public function testPayPalTransactionReference()
    {
        $reference = $this->faker->payPalTransactionReference();
        $this->assertTrue(is_string($reference));
        $this->assertNotEquals(0, $reference);
        $this->assertSame(40, strlen($reference));
    }

    /**
     * @return void
     */
    public function testPayPalToken()
    {
        $token = $this->faker->payPalToken();
        $this->assertTrue(is_string($token));
        $this->assertSame(0, strpos($token, 'EC-'));
        $this->assertSame(20, strlen($token));
    }

    /**
     * @return void
     */
    public function testPayPalCustomerReference()
    {
        $token = $this->faker->payPalCustomerReference();
        $this->assertTrue(is_string($token));
        $this->assertSame(0, strpos($token, 'B-'));
        $this->assertSame(19, strlen($token));
    }

    /**
     * @return void
     */
    public function testSoapId()
    {
        $soapId = $this->faker->soapId();
        $this->assertTrue(is_string($soapId));
        $this->assertSame(40, strlen($soapId));
        $this->assertNotEquals(0, $soapId);
    }

    /**
     * @return void
     */
    public function testNote()
    {
        $note = $this->faker->note();
        $this->assertTrue(is_string($note));
        $this->assertTrue(strlen($note) > 0);
    }

    /**
     * @return void
     */
    public function testStatus()
    {
        $status = $this->faker->status();
        $this->assertTrue(is_string($status));
        $this->assertTrue(strlen($status) > 0);
    }

    /**
     * @return void
     */
    public function testStatusCode()
    {
        $statusCode = $this->faker->statusCode();
        $this->assertTrue(is_string($statusCode));
        $this->assertTrue(strlen($statusCode) > 0);
    }

    /**
     * @return void
     */
    public function testHOAParamName()
    {
        $HOAParamName = $this->faker->HOAParamName();
        $this->assertTrue(is_string($HOAParamName));
        $this->assertTrue(strlen($HOAParamName) > 0);
    }

    /**
     * @return void
     */
    public function testRiskScore()
    {
        $score = $this->faker->riskScore();
        $this->assertTrue(is_int($score));
        $this->assertGreaterThanOrEqual(-2, $score);
        $this->assertLessThanOrEqual(100, $score);
    }
}
