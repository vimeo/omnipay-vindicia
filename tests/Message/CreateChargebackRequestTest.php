<?php

namespace Omnipay\Vindicia\Message;

use Omnipay\Vindicia\TestFramework\DataFaker;
use Omnipay\Vindicia\TestFramework\SoapTestCase;

class CreateChargebackRequestTest extends SoapTestCase
{
    public function setUp(): void
    {
        $this->faker = new DataFaker();
        $this->currency = $this->faker->currency();
        $this->amount = floatval($this->faker->monetaryAmount($this->currency));
        $this->caseNumber = $this->faker->uuid();
        $this->divisionNumber = $this->faker->uuid();
        $this->merchantNumber = $this->faker->customerReference();
        $this->merchantTransactionId = $this->faker->transactionId();
        $this->merchantTransactionTimestamp = $this->faker->today();
        $this->merchantUserId = $this->faker->uuid();
        $this->note = $this->faker->note();
        $this->presentmentAmount = floatval($this->faker->monetaryAmount($this->currency));
        $this->presentmentCurrency =  $this->faker->currency();
        $this->postedTimestamp = $this->faker->today();
        $this->processorReceivedTimestamp = $this->faker->today();
        $this->reasonCode = $this->faker->refundReason();
        $this->referenceNumber = $this->faker->uuid();
        $this->status = $this->faker->status();
        $this->statusChangedTimestamp = $this->faker->today();
        $this->VID = $this->faker->uuid();

        $this->request = new CreateChargebackRequest($this->getHttpClient(), $this->getHttpRequest());
        $this->request->initialize(
           [
               'amount' => $this->amount,
               'caseNumber' => $this->caseNumber,
               'currency' => $this->currency,
               'divisionNumber' => $this->divisionNumber,
               'merchantNumber' => $this->merchantNumber,
               'merchantTransactionId' => $this->merchantTransactionId,
               'merchantTransactionTimestamp' => $this->merchantTransactionTimestamp,
               'merchantUserId' => $this->merchantUserId,
               'note' => $this->note,
               'presentmentAmount' => $this->presentmentAmount,
               'presentmentCurrency' => $this->presentmentCurrency,
               'postedTimestamp ' => $this->postedTimestamp,
               'processorReceivedTimestamp' => $this->processorReceivedTimestamp,
               'reasonCode ' => $this->reasonCode,
               'referenceNumber ' => $this->referenceNumber,
               'status' => $this->status,
               'statusChangedTimestamp' => $this->statusChangedTimestamp,
               'VID ' => $this->VID,
           ]
        );
    }

    public function testAmount(): void
    {
        $this->assertSame($this->request, $this->request->setAmount($this->amount));
        $this->assertSame($this->amount, $this->request->getAmount());
    }

    public function testCaseNumber(): void
    {
        $this->assertSame($this->request, $this->request->setCaseNumber($this->caseNumber));
        $this->assertSame($this->caseNumber, $this->request->getCaseNumber());
    }

    public function testCurrency(): void
    {
        $this->assertSame($this->request, $this->request->setCurrency($this->currency));
        $this->assertSame($this->currency, $this->request->getCurrency());
    }

    public function testDivisionNumber(): void
    {
        $this->assertSame($this->request, $this->request->setDivisionNumber($this->divisionNumber));
        $this->assertSame($this->divisionNumber, $this->request->getDivisionNumber());
    }

    public function testMerchantNumber(): void
    {
        $this->assertSame($this->request, $this->request->setMerchantNumber($this->merchantNumber));
        $this->assertSame($this->merchantNumber, $this->request->getMerchantNumber());
    }

    public function testMerchantTransactionId(): void
    {
        $this->assertSame($this->request, $this->request->setMerchantTransactionId($this->merchantTransactionId));
        $this->assertSame($this->merchantTransactionId, $this->request->getMerchantTransactionId());
    }

    public function testMerchantTransactionTimestamp(): void
    {
        $this->assertSame($this->request, $this->request->setMerchantTransactionTimestamp($this->merchantTransactionTimestamp));
        $this->assertSame($this->merchantTransactionTimestamp, $this->request->getMerchantTransactionTimestamp());
    }

    public function testMerchantUserId(): void
    {
        $this->assertSame($this->request, $this->request->setMerchantUserId($this->merchantUserId));
        $this->assertSame($this->merchantUserId, $this->request->getMerchantUserId());
    }

    public function testNote(): void
    {
        $this->assertSame($this->request, $this->request->setNote($this->note));
        $this->assertSame($this->note, $this->request->getNote());
    }

    public function testPresentmentAmount(): void
    {
        $this->assertSame($this->request, $this->request->setPresentmentAmount($this->presentmentAmount));
        $this->assertSame($this->presentmentAmount, $this->request->getPresentmentAmount());
    }

    public function testPresentmentCurrency(): void
    {
        $this->assertSame($this->request, $this->request->setPresentmentCurrency($this->presentmentCurrency));
        $this->assertSame($this->presentmentCurrency, $this->request->getPresentmentCurrency());
    }

    public function testPostedTimestamp (): void
    {
        $this->assertSame($this->request, $this->request->setPostedTimestamp($this->postedTimestamp));
        $this->assertSame($this->postedTimestamp, $this->request->getPostedTimestamp());
    }

    public function testProcessorReceivedTimestamp(): void
    {
        $this->assertSame($this->request, $this->request->setProcessorReceivedTimestamp($this->processorReceivedTimestamp));
        $this->assertSame($this->processorReceivedTimestamp, $this->request->getProcessorReceivedTimestamp());
    }

    public function testReasonCode(): void
    {
        $this->assertSame($this->request, $this->request->setReasonCode($this->reasonCode));
        $this->assertSame($this->reasonCode, $this->request->getReasonCode());
    }

    public function testReferenceNumber(): void
    {
        $this->assertSame($this->request, $this->request->setReferenceNumber($this->referenceNumber));
        $this->assertSame($this->referenceNumber, $this->request->getReferenceNumber());
    }

    public function testStatus(): void
    {
        $this->assertSame($this->request, $this->request->setStatus($this->status));
        $this->assertSame($this->status, $this->request->getStatus());
    }

    public function testStatusChangedTimestamp(): void
    {
        $this->assertSame($this->request, $this->request->setStatusChangedTimestamp($this->statusChangedTimestamp));
        $this->assertSame($this->statusChangedTimestamp, $this->request->getStatusChangedTimestamp());
    }

    public function testVID(): void
    {
        $this->assertSame($this->request, $this->request->setVID($this->VID));
        $this->assertSame($this->VID, $this->request->getVID());
    }

    /**
     * @runInSeparateProcess
     */
    public function testSendSuccess(): void
    {
        $this->setMockSoapResponse('CreateChargebackSuccess.xml', [
            'VID' => $this->VID,
            'AMOUNT' => $this->amount,
            'CURRENCY' => $this->currency,
            'REASON_CODE' => $this->reasonCode,
            'REFERENCE_NUMBER' => $this->referenceNumber,
            'MERCHANT_TRANSACTION_ID' => $this->merchantTransactionId,
            'STATUS' => $this->status,
            'MERCHANT_USER_ID' => $this->merchantUserId,
        ]);

        $response = $this->request->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isPending());
        $chargeback = $response->getChargeback();

        $this->assertSame('OK', $response->getMessage());
        $this->assertSame($this->VID, $chargeback->getVID());
        $this->assertSame((string)$this->amount, $chargeback->getAmount());
        $this->assertSame($this->currency, $chargeback->getCurrency());
        $this->assertSame($this->reasonCode, $chargeback->getReasonCode());
        $this->assertSame($this->referenceNumber, $chargeback->getReferenceNumber());
        $this->assertSame($this->merchantTransactionId, $chargeback->getMerchantTransactionId());
        $this->assertSame($this->status, $chargeback->getStatus());
        $this->assertSame($this->merchantUserId, $chargeback->getMerchantUserId());

        $this->assertSame(AbstractRequest::TEST_ENDPOINT . '/18.0/Chargeback.wsdl', $this->getLastEndpoint());
    }

    /**
     * @runInSeparateProcess
     */
    public function testSendFailure(): void
    {
        $this->setMockSoapResponse('CreateChargebackFailure.xml');

        $response = $this->request->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertFalse($response->isPending());
        $this->assertSame('400', $response->getCode());
        $this->assertSame('Chargeback must have an amount', $response->getMessage());
    }
}
