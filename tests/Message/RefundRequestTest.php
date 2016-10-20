<?php

namespace Omnipay\Vindicia\Message;

use Omnipay\Vindicia\TestFramework\Mocker;
use Omnipay\Vindicia\TestFramework\DataFaker;
use Omnipay\Vindicia\TestFramework\SoapTestCase;
use Omnipay\Vindicia\VindiciaRefundItem;
use Omnipay\Common\Currency;
use Omnipay\Vindicia\AttributeBag;

class RefundRequestTest extends SoapTestCase
{
    public function setUp()
    {
        $this->faker = new DataFaker();

        $this->refundId = $this->faker->refundId();
        $this->currency = $this->faker->currency();
        $this->refundAmount = $this->faker->monetaryAmount($this->currency);
        $this->transactionAmount = $this->faker->monetaryAmount($this->currency);
        // make transactionAmount >= refundAmount
        if ($this->transactionAmount < $this->refundAmount) {
            $temp = $this->transactionAmount;
            $this->transactionAmount = $this->refundAmount;
            $this->refundAmount = $temp;
            unset($temp);
        }

        $this->transactionId = $this->faker->transactionId();
        $this->transactionReference = $this->faker->transactionReference();
        $this->note = $this->faker->randomCharacters(
            DataFaker::ALPHABET_LOWER . DataFaker::ALPHABET_UPPER,
            $this->faker->intBetween(10, 50)
        );

        $this->attributes = $this->faker->attributesAsArray();

        $this->request = new RefundRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->initialize(
            array(
                'refundId' => $this->refundId,
                'amount' => $this->refundAmount,
                'currency' => $this->currency,
                'transactionId' => $this->transactionId,
                'transactionReference' => $this->transactionReference,
                'note' => $this->note,
                'attributes' => $this->attributes
            )
        );

        $this->refundReference = $this->faker->refundReference();
        $this->card = $this->faker->card();
        $this->paymentMethodId = $this->faker->paymentMethodId();
        $this->customerId = $this->faker->customerId();
    }

    public function testRefundId()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\RefundRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setRefundId($this->refundId));
        $this->assertSame($this->refundId, $request->getRefundId());
    }

    public function testNote()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\RefundRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setNote($this->note));
        $this->assertSame($this->note, $request->getNote());
    }

    public function testTransactionId()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\RefundRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setTransactionId($this->transactionId));
        $this->assertSame($this->transactionId, $request->getTransactionId());
    }

    public function testTransactionReference()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\RefundRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setTransactionReference($this->transactionReference));
        $this->assertSame($this->transactionReference, $request->getTransactionReference());
    }

    public function testCurrency()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\RefundRequest')->makePartial();
        $request->initialize();

        $this->assertSame($request, $request->setCurrency($this->currency));
        $this->assertSame($this->currency, $request->getCurrency());
    }

    public function testAmount()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\RefundRequest')->makePartial();
        $request->initialize();

        $request->setCurrency($this->currency);
        $this->assertSame($request, $request->setAmount($this->refundAmount));
        $this->assertSame($this->refundAmount, $request->getAmount());
    }

    public function testItemsAsBag()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\RefundRequest')->makePartial();
        $request->initialize();

        // $items is a VindiciaRefundItemBag
        $items = $this->faker->refundItems($this->currency);
        $this->assertSame($request, $request->setItems($items));
        $this->assertSame($items, $request->getItems());
    }

    public function testItemsAsArray()
    {
        $request = Mocker::mock('\Omnipay\Vindicia\Message\RefundRequest')->makePartial();
        $request->initialize();

        // $items is an array
        $items = $this->faker->refundItemsAsArray($this->currency);
        $this->assertSame($request, $request->setItems($items));

        $returnedItems = $request->getItems();
        $this->assertInstanceOf('Omnipay\Vindicia\VindiciaRefundItemBag', $returnedItems);

        $numItems = count($items);
        $this->assertSame($numItems, $returnedItems->count());

        foreach ($returnedItems as $i => $returnedItem) {
            $this->assertEquals(new VindiciaRefundItem($items[$i]), $returnedItem);
        }
    }

    public function testAttributes()
    {
        $this->assertSame($this->request, $this->request->setAttributes($this->attributes));
        $this->assertEquals(new AttributeBag($this->attributes), $this->request->getAttributes());
    }

    public function testGetData()
    {
        $data = $this->request->getData();

        $this->assertSame($this->refundAmount, $data['refunds'][0]->amount);
        $this->assertSame($this->currency, $data['refunds'][0]->currency);
        $this->assertSame($this->note, $data['refunds'][0]->note);
        $this->assertSame('None', $data['refunds'][0]->refundDistributionStrategy);
        $this->assertSame($this->transactionId, $data['refunds'][0]->transaction->merchantTransactionId);
        $this->assertSame($this->transactionReference, $data['refunds'][0]->transaction->VID);
        $this->assertSame('perform', $data['action']);

        $numAttributes = count($this->attributes);
        $this->assertSame($numAttributes, count($data['refunds'][0]->nameValues));
        for ($i = 0; $i < $numAttributes; $i++) {
            $this->assertSame($this->attributes[$i]['name'], $data['refunds'][0]->nameValues[$i]->name);
            $this->assertSame($this->attributes[$i]['value'], $data['refunds'][0]->nameValues[$i]->value);
        }
    }

    public function testGetDataNoAmount()
    {
        $data = $this->request->setAmount(null)->getData();

        $this->assertNull($data['refunds'][0]->amount);
        $this->assertSame($this->currency, $data['refunds'][0]->currency);
        $this->assertSame($this->note, $data['refunds'][0]->note);
        $this->assertSame('RemainingBalance', $data['refunds'][0]->refundDistributionStrategy);
        $this->assertSame($this->transactionId, $data['refunds'][0]->transaction->merchantTransactionId);
        $this->assertSame($this->transactionReference, $data['refunds'][0]->transaction->VID);
        $this->assertSame('perform', $data['action']);
    }

    public function testGetDataItems()
    {
        $refundItems = $this->faker->refundItemsAsArray($this->currency);
        $sumOfItems = '0';
        foreach ($refundItems as $refundItem) {
            // strval to eliminate floating point errors
            $sumOfItems = strval($sumOfItems + $refundItem['amount']);
        }
        // make sure sumOfItems is formatted properly
        $sumOfItems = number_format(floatval($sumOfItems), Currency::find($this->currency)->getDecimals(), '.', '');

        // behavior should be the same whether the sum of the item amounts is provided
        // as the total amount or the total amount is not provided
        $amounts = array(null, $sumOfItems);
        foreach ($amounts as $amount) {
            $data = $this->request->setAmount($amount)->setItems($refundItems)->getData();

            $this->assertSame($amount, $data['refunds'][0]->amount);
            $numItems = count($refundItems);
            $this->assertSame($numItems, count($data['refunds'][0]->refundItems));
            for ($i = 0; $i < $numItems; $i++) {
                $this->assertSame($refundItems[$i]['amount'], $data['refunds'][0]->refundItems[$i]->amount);
                $this->assertSame($refundItems[$i]['sku'], $data['refunds'][0]->refundItems[$i]->sku);
                $this->assertSame($refundItems[$i]['transactionItemIndexNumber'], $data['refunds'][0]->refundItems[$i]->transactionItemIndexNumber);
            }
            $this->assertSame($this->currency, $data['refunds'][0]->currency);
            $this->assertSame($this->note, $data['refunds'][0]->note);
            $this->assertSame('SpecifiedItems', $data['refunds'][0]->refundDistributionStrategy);
            $this->assertSame($this->transactionId, $data['refunds'][0]->transaction->merchantTransactionId);
            $this->assertSame($this->transactionReference, $data['refunds'][0]->transaction->VID);
            $this->assertSame('perform', $data['action']);
        }
    }

    /**
     * If both an amount and refund items are provided, the sum of the refund items
     * amounts must equal the amount or else an exception will be thrown.
     *
     * @expectedException \Omnipay\Common\Exception\InvalidRequestException
     * @expectedExceptionMessage Sum of refund item amounts not equal to set amount.
     */
    public function testRefundItemsMustEqualTotalAmount()
    {
        $refundItems = $this->faker->refundItemsAsArray($this->currency);
        $sumOfItems = '0.0';
        foreach ($refundItems as $refundItem) {
            // strval to eliminate floating point errors
            $sumOfItems = strval($sumOfItems + $refundItem['amount']);
        }
        $sumOfItems = strval($sumOfItems * $this->faker->intBetween(2, 5));
        $sumOfItems = number_format(floatval($sumOfItems), Currency::find($this->currency)->getDecimals(), '.', '');
        $data = $this->request->setAmount($sumOfItems)->setItems($refundItems)->getData();
    }

    /**
     * Refund items must have either the sku or transactionItemIndexNumber set.
     *
     * @expectedException \Omnipay\Vindicia\Exception\InvalidItemException
     * @expectedExceptionMessage Refund item requires sku or transactionItemIndexNumber.
     */
    public function testTransactionItemIndexNumberOrSkuRequired()
    {
        $refundItems = $this->faker->refundItemsAsArray($this->currency);
        $badItem = &$refundItems[$this->faker->intBetween(0, count($refundItems) - 1)];
        $badItem['sku'] = null;
        $badItem['transactionItemIndexNumber'] = null;

        $data = $this->request->setAmount(null)->setItems($refundItems)->getData();
    }

    /**
     * @expectedException \Omnipay\Common\Exception\InvalidRequestException
     * @expectedExceptionMessage Either the transactionId or transactionReference parameter is required.
     */
    public function testTransactionIdOrReferenceRequired()
    {
        $this->request->setTransactionId(null);
        $this->request->setTransactionReference(null);
        $this->request->getData();
    }

    /**
     * @expectedException \Omnipay\Common\Exception\InvalidRequestException
     * @expectedExceptionMessage The refundId parameter is required
     */
    public function testRefundIdRequired()
    {
        $this->request->setRefundId(null);
        $this->request->getData();
    }

    public function testSendSuccess()
    {
        $this->setMockSoapResponse('RefundSuccess.xml', array(
            'CURRENCY' => $this->currency,
            'TRANSACTION_AMOUNT' => $this->transactionAmount,
            'REFUND_AMOUNT' => $this->refundAmount,
            'CUSTOMER_ID' => $this->customerId,
            'CARD_FIRST_SIX' => substr($this->card['number'], 0, 6),
            'CARD_LAST_FOUR' => substr($this->card['number'], -4),
            'EXPIRY_MONTH' => $this->card['expiryMonth'],
            'EXPIRY_YEAR' => $this->card['expiryYear'],
            'CVV' => $this->card['cvv'],
            'PAYMENT_METHOD_ID' => $this->paymentMethodId,
            'TRANSACTION_ID' => $this->transactionId,
            'TRANSACTION_REFERENCE' => $this->transactionReference,
            'REFUND_ID' => $this->refundId,
            'REFUND_REFERENCE' => $this->refundReference
        ));

        $response = $this->request->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isPending());
        $this->assertSame('OK', $response->getMessage());
        $this->assertSame($this->refundId, $response->getRefundId());
        $this->assertSame($this->refundReference, $response->getRefundReference());

        $this->assertSame('https://soap.prodtest.sj.vindicia.com/18.0/Refund.wsdl', $this->getLastEndpoint());
    }

    public function testSendFailure()
    {
        // switch transactionAmount and refundAmount so refundAmount is higher
        // there's a slight chance that they'll be the same but it doesn't matter for this test
        $temp = $this->transactionAmount;
        $this->transactionAmount = $this->refundAmount;
        $this->refundAmount = $temp;
        unset($temp);

        $this->setMockSoapResponse('RefundFailure.xml', array(
            'CURRENCY' => $this->currency,
            'TRANSACTION_AMOUNT' => $this->transactionAmount,
            'REFUND_AMOUNT' => $this->refundAmount,
            'CUSTOMER_ID' => $this->customerId,
            'CARD_FIRST_SIX' => substr($this->card['number'], 0, 6),
            'CARD_LAST_FOUR' => substr($this->card['number'], -4),
            'EXPIRY_MONTH' => $this->card['expiryMonth'],
            'EXPIRY_YEAR' => $this->card['expiryYear'],
            'CVV' => $this->card['cvv'],
            'PAYMENT_METHOD_ID' => $this->paymentMethodId,
            'TRANSACTION_ID' => $this->transactionId,
            'TRANSACTION_REFERENCE' => $this->transactionReference
        ));

        $response = $this->request->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isPending());
        $this->assertSame('206', $response->getCode());
        $this->assertSame('ERROR:  Refund failed:  Refund amount ' . $this->refundAmount . ' exceeds max allowable ' . $this->transactionAmount, $response->getMessage());
        // no id or reference since Vindicia creates them both
        $this->assertNull($response->getRefundId());
        $this->assertNull($response->getRefundReference());
    }
}
