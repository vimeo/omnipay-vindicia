<?php

namespace Omnipay\Vindicia\Message;

use Omnipay\Vindicia\TestFramework\DataFaker;
use Omnipay\Vindicia\TestFramework\SoapTestCase;

class ChargebackRequestTest extends SoapTestCase
{
    /**
     * @return void
     */
    public function setUp()
    {
        date_default_timezone_set('Europe/London');
        $this->faker = new DataFaker();
        $this->currency = $this->faker->currency();

        $this->transactionId = $this->faker->transactionId();
        $this->amount = $this->faker->monetaryAmount($this->currency);
        $this->referenceNumber = $this->faker->chargebackReference();
        $this->processorReceivedTime = $this->faker->timestamp();

        $this->request = new ChargebackRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->initialize(
            array(
                'transactionId' => $this->transactionId,
                'amount' => $this->amount,
                'chargebackReference' => $this->referenceNumber,
                'processorReceivedTime' => $this->processorReceivedTime,
            )
        );
    }

    /**
     * @return void
     */
    public function testGetData()
    {
        $data = $this->request->getData();

        $this->assertSame($this->transactionId, $data['chargeback']->merchantTransactionId);
        $this->assertSame($this->amount, $data['chargeback']->amount);
        $this->assertSame($this->referenceNumber, $data['chargeback']->referenceNumber);
        $this->assertSame($this->processorReceivedTime, $data['chargeback']->processorReceivedTimestamp);
    }

}
