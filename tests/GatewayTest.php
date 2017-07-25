<?php

namespace Omnipay\Vindicia;

use Omnipay\Tests\GatewayTestCase;
use Omnipay\Omnipay;
use Omnipay\Vindicia\TestFramework\DataFaker;
use Omnipay\Vindicia\Message\AuthorizeRequest;

class GatewayTest extends GatewayTestCase
{
    /**
     * @return void
     */
    public function setUp()
    {
        date_default_timezone_set('Europe/London');
        $this->gateway = new Gateway($this->getHttpClient(), $this->getHttpRequest());
        $this->gateway->setTestMode(true);
        $this->faker = new DataFaker();
    }

    /**
     * @return void
     */
    public function testGetName()
    {
        $this->assertSame('Vindicia', $this->gateway->getName());
    }

    /**
     * @return void
     */
    public function testCreation()
    {
        $gateway = Omnipay::create('Vindicia');
        $this->assertInstanceOf('Omnipay\Vindicia\Gateway', $gateway);
    }

    /**
     * @return void
     */
    public function testUsername()
    {
        $username = $this->faker->username();

        $this->assertSame($this->gateway, $this->gateway->setUsername($username));
        $this->assertSame($username, $this->gateway->getUsername());
    }

    /**
     * @return void
     */
    public function testPassword()
    {
        $password = $this->faker->password();

        $this->assertSame($this->gateway, $this->gateway->setPassword($password));
        $this->assertSame($password, $this->gateway->getPassword());
    }

    /**
     * @return void
     */
    public function testTestMode()
    {
        $testMode = $this->faker->bool();

        $this->assertSame($this->gateway, $this->gateway->setTestMode($testMode));
        $this->assertSame($testMode, $this->gateway->getTestMode());
    }

    /**
     * @return void
     */
    public function testMinChargebackProbability()
    {
        $minChargebackProbability = $this->faker->chargebackProbability();

        $this->assertSame($this->gateway, $this->gateway->setMinChargebackProbability($minChargebackProbability));
        $this->assertSame($minChargebackProbability, $this->gateway->getMinChargebackProbability());
    }

    /**
     * @return void
     */
    public function testAuthorize()
    {
        $currency = $this->faker->currency();
        $amount = $this->faker->monetaryAmount($currency);
        $customerId = $this->faker->customerId();
        $card = $this->faker->card();

        $request = $this->gateway->authorize(
            array(
                'amount' => $amount,
                'currency' => $currency,
                'card' => $card,
                'customerId' => $customerId
            )
        );

        $this->assertInstanceOf('Omnipay\Vindicia\Message\AuthorizeRequest', $request);
        $this->assertSame($amount, $request->getAmount());
        $this->assertSame($currency, $request->getCurrency());
        $this->assertSameCard($card, $request->getCard());
        $this->assertSame($customerId, $request->getCustomerId());
    }

    /**
     * @return void
     */
    public function testCapture()
    {
        $transactionId = $this->faker->transactionId();

        $request = $this->gateway->capture(
            array(
                'transactionId' => $transactionId
            )
        );

        $this->assertInstanceOf('Omnipay\Vindicia\Message\CaptureRequest', $request);
        $this->assertSame($transactionId, $request->getTransactionId());
    }

    /**
     * @return void
     */
    public function testPurchase()
    {
        $currency = $this->faker->currency();
        $amount = $this->faker->monetaryAmount($currency);
        $customerId = $this->faker->customerId();
        $card = $this->faker->card();

        $request = $this->gateway->purchase(
            array(
                'amount' => $amount,
                'currency' => $currency,
                'card' => $card,
                'customerId' => $customerId
            )
        );

        $this->assertInstanceOf('Omnipay\Vindicia\Message\PurchaseRequest', $request);
        $this->assertSame($amount, $request->getAmount());
        $this->assertSame($currency, $request->getCurrency());
        $this->assertSameCard($card, $request->getCard());
        $this->assertSame($customerId, $request->getCustomerId());
    }

    /**
     * @return void
     */
    public function testVoid()
    {
        $transactionId = $this->faker->transactionId();

        $request = $this->gateway->void(
            array(
                'transactionId' => $transactionId
            )
        );

        $this->assertInstanceOf('Omnipay\Vindicia\Message\VoidRequest', $request);
        $this->assertSame($transactionId, $request->getTransactionId());
    }

    /**
     * @return void
     */
    public function testRefund()
    {
        $currency = $this->faker->currency();
        $refundAmount = $this->faker->monetaryAmount($currency);
        $transactionAmount = $this->faker->monetaryAmount($currency);
        // make transactionAmount >= refundAmount
        if ($transactionAmount < $refundAmount) {
            $temp = $transactionAmount;
            $transactionAmount = $refundAmount;
            $refundAmount = $temp;
            unset($temp);
        }

        $transactionId = $this->faker->transactionId();
        $transactionReference = $this->faker->transactionReference();
        $reason = $this->faker->refundReason();

        $request = $this->gateway->refund(
            array(
                'amount' => $refundAmount,
                'currency' => $currency,
                'transactionId' => $transactionId,
                'transactionReference' => $transactionReference,
                'reason' => $reason
            )
        );

        $this->assertInstanceOf('Omnipay\Vindicia\Message\RefundRequest', $request);
        $this->assertSame($refundAmount, $request->getAmount());
        $this->assertSame($currency, $request->getCurrency());
        $this->assertSame($transactionId, $request->getTransactionId());
        $this->assertSame($transactionReference, $request->getTransactionReference());
        $this->assertSame($reason, $request->getReason());
    }

    /**
     * @return void
     */
    public function testCreatePaymentMethod()
    {
        $card = $this->faker->card();
        $customerId = $this->faker->customerId();

        $request = $this->gateway->createPaymentMethod(
            array(
                'card' => $card,
                'customerId' => $customerId
            )
        );

        $this->assertInstanceOf('Omnipay\Vindicia\Message\CreatePaymentMethodRequest', $request);
        $this->assertSameCard($card, $request->getCard());
        $this->assertSame($customerId, $request->getCustomerId());
    }

    /**
     * @return void
     */
    public function testUpdatePaymentMethod()
    {
        $card = $this->faker->card();
        $customerId = $this->faker->customerId();

        $request = $this->gateway->updatePaymentMethod(
            array(
                'card' => $card,
                'customerId' => $customerId
            )
        );

        // update uses create's request since they're the same
        $this->assertInstanceOf('Omnipay\Vindicia\Message\CreatePaymentMethodRequest', $request);
        $this->assertSameCard($card, $request->getCard());
        $this->assertSame($customerId, $request->getCustomerId());
    }

    /**
     * @return void
     */
    public function testCreateCustomer()
    {
        $currency = $this->faker->currency();
        $name = $this->faker->name();
        $email = $this->faker->email();
        $customerId = $this->faker->customerId();

        $request = $this->gateway->createCustomer(
            array(
                'name' => $name,
                'email' => $email,
                'customerId' => $customerId
            )
        );

        $this->assertInstanceOf('Omnipay\Vindicia\Message\CreateCustomerRequest', $request);
        $this->assertSame($name, $request->getName());
        $this->assertSame($email, $request->getEmail());
        $this->assertSame($customerId, $request->getCustomerId());
    }

    /**
     * @return void
     */
    public function testUpdateCustomer()
    {
        $currency = $this->faker->currency();
        $name = $this->faker->name();
        $email = $this->faker->email();
        $customerId = $this->faker->customerId();

        $request = $this->gateway->updateCustomer(
            array(
                'name' => $name,
                'email' => $email,
                'customerId' => $customerId
            )
        );

        // update uses create's request since they're the same
        $this->assertInstanceOf('Omnipay\Vindicia\Message\CreateCustomerRequest', $request);
        $this->assertSame($name, $request->getName());
        $this->assertSame($email, $request->getEmail());
        $this->assertSame($customerId, $request->getCustomerId());
    }

    /**
     * @return void
     */
    public function testCreatePlan()
    {
        $planId = $this->faker->planId();
        $interval = $this->faker->billingInterval();
        $intervalCount = $this->faker->billingIntervalCount();
        $prices = $this->faker->pricesAsArray();
        $statementDescriptor = $this->faker->statementDescriptor();
        $taxClassification = $this->faker->taxClassification();

        $request = $this->gateway->createPlan(
            array(
                'planId' => $planId,
                'interval' => $interval,
                'intervalCount' => $intervalCount,
                'prices' => $prices,
                'statementDescriptor' => $statementDescriptor,
                'taxClassification' => $taxClassification
            )
        );

        $this->assertInstanceOf('Omnipay\Vindicia\Message\CreatePlanRequest', $request);
        $this->assertSame($planId, $request->getPlanId());
        $this->assertSame($interval, $request->getInterval());
        $this->assertSame($intervalCount, $request->getIntervalCount());
        $this->assertEquals((new PriceBag($prices)), $request->getPrices());
        $this->assertSame($statementDescriptor, $request->getStatementDescriptor());
        $this->assertSame($taxClassification, $request->getTaxClassification());
    }

    /**
     * @return void
     */
    public function testUpdatePlan()
    {
        $planId = $this->faker->planId();
        $interval = $this->faker->billingInterval();
        $intervalCount = $this->faker->billingIntervalCount();
        $prices = $this->faker->pricesAsArray();
        $statementDescriptor = $this->faker->statementDescriptor();
        $taxClassification = $this->faker->taxClassification();

        $request = $this->gateway->updatePlan(
            array(
                'planId' => $planId,
                'interval' => $interval,
                'intervalCount' => $intervalCount,
                'prices' => $prices,
                'statementDescriptor' => $statementDescriptor,
                'taxClassification' => $taxClassification
            )
        );

        // update uses create's request since they're the same
        $this->assertInstanceOf('Omnipay\Vindicia\Message\CreatePlanRequest', $request);
        $this->assertSame($planId, $request->getPlanId());
        $this->assertSame($interval, $request->getInterval());
        $this->assertSame($intervalCount, $request->getIntervalCount());
        $this->assertEquals((new PriceBag($prices)), $request->getPrices());
        $this->assertSame($statementDescriptor, $request->getStatementDescriptor());
        $this->assertSame($taxClassification, $request->getTaxClassification());
    }

    /**
     * @return void
     */
    public function testCreateProduct()
    {
        $productId = $this->faker->productId();
        $planId = $this->faker->planId();
        $prices = $this->faker->pricesAsArray();
        $statementDescriptor = $this->faker->statementDescriptor();
        $taxClassification = $this->faker->taxClassification();
        $duplicateBehavior = $this->faker->duplicateBehavior();

        $request = $this->gateway->createProduct(
            array(
                'productId' => $productId,
                'planId' => $planId,
                'duplicateBehavior' => $duplicateBehavior,
                'statementDescriptor' => $statementDescriptor,
                'taxClassification' => $taxClassification,
                'prices' => $prices,
            )
        );

        $this->assertInstanceOf('Omnipay\Vindicia\Message\CreateProductRequest', $request);
        $this->assertSame($planId, $request->getPlanId());
        $this->assertSame($productId, $request->getProductId());
        $this->assertSame($duplicateBehavior, $request->getDuplicateBehavior());
        $this->assertEquals((new PriceBag($prices)), $request->getPrices());
        $this->assertSame($statementDescriptor, $request->getStatementDescriptor());
        $this->assertSame($taxClassification, $request->getTaxClassification());
    }

    /**
     * @return void
     */
    public function testUpdateProduct()
    {
        $productId = $this->faker->productId();
        $planId = $this->faker->planId();
        $prices = $this->faker->pricesAsArray();
        $statementDescriptor = $this->faker->statementDescriptor();
        $taxClassification = $this->faker->taxClassification();
        $duplicateBehavior = $this->faker->duplicateBehavior();

        $request = $this->gateway->updateProduct(
            array(
                'productId' => $productId,
                'planId' => $planId,
                'duplicateBehavior' => $duplicateBehavior,
                'statementDescriptor' => $statementDescriptor,
                'taxClassification' => $taxClassification,
                'prices' => $prices,
            )
        );

        // update uses create's request since they're the same
        $this->assertInstanceOf('Omnipay\Vindicia\Message\CreateProductRequest', $request);
        $this->assertSame($planId, $request->getPlanId());
        $this->assertSame($productId, $request->getProductId());
        $this->assertSame($duplicateBehavior, $request->getDuplicateBehavior());
        $this->assertEquals((new PriceBag($prices)), $request->getPrices());
        $this->assertSame($statementDescriptor, $request->getStatementDescriptor());
        $this->assertSame($taxClassification, $request->getTaxClassification());
    }

    /**
     * @return void
     */
    public function testCreateSubscription()
    {
        $subscriptionId = $this->faker->subscriptionId();
        $planId = $this->faker->planId();
        $customerId = $this->faker->customerId();
        $productId = $this->faker->productId();
        $currency = $this->faker->currency();
        $statementDescriptor = $this->faker->statementDescriptor();
        $ip = $this->faker->ipAddress();
        $startTime = $this->faker->timestamp();
        $card = $this->faker->card();
        $paymentMethodId = $this->faker->paymentMethodId();

        $request = $this->gateway->createSubscription(
            array(
                'planId' => $planId,
                'subscriptionId' => $subscriptionId,
                'productId' => $productId,
                'customerId' => $customerId,
                'statementDescriptor' => $statementDescriptor,
                'currency' => $currency,
                'ip' => $ip,
                'startTime' => $startTime,
                'card' => $card,
                'paymentMethodId' => $paymentMethodId
            )
        );

        $this->assertInstanceOf('Omnipay\Vindicia\Message\CreateSubscriptionRequest', $request);
        $this->assertSame($planId, $request->getPlanId());
        $this->assertSame($productId, $request->getProductId());
        $this->assertSame($statementDescriptor, $request->getStatementDescriptor());
        $this->assertSame($subscriptionId, $request->getSubscriptionId());
        $this->assertSame($customerId, $request->getCustomerId());
        $this->assertSame($currency, $request->getCurrency());
        $this->assertSame($ip, $request->getIp());
        $this->assertSame($startTime, $request->getStartTime());
        $this->assertSame($paymentMethodId, $request->getPaymentMethodId());
        $this->assertSameCard($card, $request->getCard());
    }

    /**
     * @return void
     */
    public function testUpdateSubscription()
    {
        $subscriptionId = $this->faker->subscriptionId();
        $planId = $this->faker->planId();
        $customerId = $this->faker->customerId();
        $productId = $this->faker->productId();
        $currency = $this->faker->currency();
        $statementDescriptor = $this->faker->statementDescriptor();
        $ip = $this->faker->ipAddress();
        $startTime = $this->faker->timestamp();
        $card = $this->faker->card();
        $paymentMethodId = $this->faker->paymentMethodId();

        $request = $this->gateway->updateSubscription(
            array(
                'planId' => $planId,
                'subscriptionId' => $subscriptionId,
                'productId' => $productId,
                'customerId' => $customerId,
                'statementDescriptor' => $statementDescriptor,
                'currency' => $currency,
                'ip' => $ip,
                'startTime' => $startTime,
                'card' => $card,
                'paymentMethodId' => $paymentMethodId
            )
        );

        // update uses update's request since they're the same
        $this->assertInstanceOf('Omnipay\Vindicia\Message\CreateSubscriptionRequest', $request);
        $this->assertSame($planId, $request->getPlanId());
        $this->assertSame($productId, $request->getProductId());
        $this->assertSame($statementDescriptor, $request->getStatementDescriptor());
        $this->assertSame($subscriptionId, $request->getSubscriptionId());
        $this->assertSame($customerId, $request->getCustomerId());
        $this->assertSame($currency, $request->getCurrency());
        $this->assertSame($ip, $request->getIp());
        $this->assertSame($startTime, $request->getStartTime());
        $this->assertSame($paymentMethodId, $request->getPaymentMethodId());
        $this->assertSameCard($card, $request->getCard());
    }

    /**
     * @return void
     */
    public function testCancelSubscription()
    {
        $subscriptionId = $this->faker->subscriptionId();
        $subscriptionReference = $this->faker->subscriptionReference();

        $request = $this->gateway->cancelSubscription(
            array(
                'subscriptionId' => $subscriptionId,
                'subscriptionReference' => $subscriptionReference,
            )
        );

        $this->assertInstanceOf('Omnipay\Vindicia\Message\CancelSubscriptionRequest', $request);
        $this->assertSame($subscriptionId, $request->getSubscriptionId());
        $this->assertSame($subscriptionReference, $request->getSubscriptionReference());
    }

    /**
     * @return void
     */
    public function testCancelSubscriptions()
    {
        $numSubscriptions = $this->faker->intBetween(0, 3);
        $subscriptionIds = array();
        for ($i = 0; $i < $numSubscriptions; $i++) {
            $subscriptionIds[] = $this->faker->subscriptionId();
        }
        if (empty($subscriptionIds)) {
            $subscriptionIds = null;
        }

        $customerId = $this->faker->customerId();

        $request = $this->gateway->cancelSubscriptions(
            array(
                'customerId' => $customerId,
                'subscriptionIds' => $subscriptionIds
            )
        );

        $this->assertInstanceOf('Omnipay\Vindicia\Message\CancelSubscriptionsRequest', $request);
        $this->assertSame($subscriptionIds, $request->getSubscriptionIds());
        $this->assertSame($customerId, $request->getCustomerId());
    }

    /**
     * @return void
     */
    public function testFetchTransaction()
    {
        $transactionId = $this->faker->transactionId();

        $request = $this->gateway->fetchTransaction(
            array(
                'transactionId' => $transactionId
            )
        );

        $this->assertInstanceOf('Omnipay\Vindicia\Message\FetchTransactionRequest', $request);
        $this->assertSame($transactionId, $request->getTransactionId());
    }

    /**
     * @return void
     */
    public function testFetchTransactions()
    {
        $customerId = $this->faker->customerId();

        $request = $this->gateway->fetchTransactions(
            array(
                'customerId' => $customerId
            )
        );

        $this->assertInstanceOf('Omnipay\Vindicia\Message\FetchTransactionsRequest', $request);
        $this->assertSame($customerId, $request->getCustomerId());
    }

    /**
     * @return void
     */
    public function testFetchProduct()
    {
        $productId = $this->faker->productId();

        $request = $this->gateway->fetchProduct(
            array(
                'productId' => $productId
            )
        );

        $this->assertInstanceOf('Omnipay\Vindicia\Message\FetchProductRequest', $request);
        $this->assertSame($productId, $request->getProductId());
    }

    /**
     * @return void
     */
    public function testFetchSubscription()
    {
        $subscriptionId = $this->faker->subscriptionId();

        $request = $this->gateway->fetchSubscription(
            array(
                'subscriptionId' => $subscriptionId
            )
        );

        $this->assertInstanceOf('Omnipay\Vindicia\Message\FetchSubscriptionRequest', $request);
        $this->assertSame($subscriptionId, $request->getSubscriptionId());
    }

    /**
     * @return void
     */
    public function testFetchSubscriptions()
    {
        $customerId = $this->faker->customerId();

        $request = $this->gateway->fetchSubscriptions(
            array(
                'customerId' => $customerId
            )
        );

        $this->assertInstanceOf('Omnipay\Vindicia\Message\FetchSubscriptionsRequest', $request);
        $this->assertSame($customerId, $request->getCustomerId());
    }

    /**
     * @return void
     */
    public function testFetchCustomer()
    {
        $customerId = $this->faker->customerId();

        $request = $this->gateway->fetchCustomer(
            array(
                'customerId' => $customerId
            )
        );

        $this->assertInstanceOf('Omnipay\Vindicia\Message\FetchCustomerRequest', $request);
        $this->assertSame($customerId, $request->getCustomerId());
    }

    /**
     * @return void
     */
    public function testFetchPaymentMethod()
    {
        $paymentMethodId = $this->faker->paymentMethodId();

        $request = $this->gateway->fetchPaymentMethod(
            array(
                'paymentMethodId' => $paymentMethodId
            )
        );

        $this->assertInstanceOf('Omnipay\Vindicia\Message\FetchPaymentMethodRequest', $request);
        $this->assertSame($paymentMethodId, $request->getPaymentMethodId());
    }

    /**
     * @return void
     */
    public function testFetchPaymentMethods()
    {
        $customerId = $this->faker->customerId();
        $customerReference = $this->faker->customerReference();

        $request = $this->gateway->fetchPaymentMethods(
            array(
                'customerId' => $customerId,
                'customerReference' => $customerReference
            )
        );

        $this->assertInstanceOf('Omnipay\Vindicia\Message\FetchPaymentMethodsRequest', $request);
        $this->assertSame($customerId, $request->getCustomerId());
        $this->assertSame($customerReference, $request->getCustomerReference());
    }

    /**
     * @return void
     */
    public function testFetchPlan()
    {
        $planId = $this->faker->planId();

        $request = $this->gateway->fetchPlan(
            array(
                'planId' => $planId
            )
        );

        $this->assertInstanceOf('Omnipay\Vindicia\Message\FetchPlanRequest', $request);
        $this->assertSame($planId, $request->getPlanId());
    }

    /**
     * @return void
     */
    public function testFetchRefunds()
    {
        $transactionId = $this->faker->transactionId();

        $request = $this->gateway->fetchRefunds(
            array(
                'transactionId' => $transactionId
            )
        );

        $this->assertInstanceOf('Omnipay\Vindicia\Message\FetchRefundsRequest', $request);
        $this->assertSame($transactionId, $request->getTransactionId());
    }

    /**
     * @return void
     */
    public function testFetchChargebacks()
    {
        $transactionId = $this->faker->transactionId();

        $request = $this->gateway->fetchChargebacks(
            array(
                'transactionId' => $transactionId
            )
        );

        $this->assertInstanceOf('Omnipay\Vindicia\Message\FetchChargebacksRequest', $request);
        $this->assertSame($transactionId, $request->getTransactionId());
    }

    /**
     * @return void
     */
    public function testCalculateSalesTax()
    {
        $currency = $this->faker->currency();
        $amount = $this->faker->monetaryAmount($currency);
        $card = $this->faker->card();
        $taxClassification = $this->faker->taxClassification();

        $request = $this->gateway->calculateSalesTax(
            array(
                'amount' => $amount,
                'currency' => $currency,
                'card' => $card,
                'taxClassification' => $taxClassification
            )
        );

        $this->assertSame($amount, $request->getAmount());
        $this->assertSame($card['country'], $request->getCard()->getCountry());
        $this->assertSame($currency, $request->getCurrency());
        $this->assertSame($taxClassification, $request->getTaxClassification());
    }

    /**
     * @return void
     */
    public function testDefaultMinChargebackProbability()
    {
        $currency = $this->faker->currency();
        $amount = $this->faker->monetaryAmount($currency);
        $customerId = $this->faker->customerId();
        $card = $this->faker->card();

        // if there's no min chargeback probability set, request should take its default

        $this->assertNull($this->gateway->getMinChargebackProbability());

        $request = $this->gateway->authorize(
            array(
                'amount' => $amount,
                'currency' => $currency,
                'card' => $card,
                'customerId' => $customerId
            )
        );

        $this->assertSame(AuthorizeRequest::DEFAULT_MIN_CHARGEBACK_PROBABILITY, $request->getMinChargebackProbability());

        // if a gateway default is set, the request should take that

        $minChargebackProbability = $this->faker->chargebackProbability();
        $this->gateway->setMinChargebackProbability($minChargebackProbability);

        $request = $this->gateway->authorize(
            array(
                'amount' => $amount,
                'currency' => $currency,
                'card' => $card,
                'customerId' => $customerId
            )
        );

        $this->assertSame($minChargebackProbability, $request->getMinChargebackProbability());

        // if a gateway default is set but the request overrides it, the request should take the override

        $overrideMinChargebackProbability = $this->faker->chargebackProbability();

        $request = $this->gateway->authorize(
            array(
                'amount' => $amount,
                'currency' => $currency,
                'card' => $card,
                'customerId' => $customerId,
                'minChargebackProbability' => $overrideMinChargebackProbability
            )
        );

        $this->assertSame($overrideMinChargebackProbability, $request->getMinChargebackProbability());
    }

    /**
     * @return void
     */
    protected function assertSameCard($card, $requestCard)
    {
        $this->assertSame($card['number'], $requestCard->getNumber());
        $this->assertSame(intval($card['expiryMonth']), $requestCard->getExpiryMonth());
        $this->assertSame(intval($card['expiryYear']), $requestCard->getExpiryYear());
    }
}
