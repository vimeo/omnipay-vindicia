<?php

namespace Omnipay\Vindicia;

use Omnipay\Common\CreditCard;
use Omnipay\Vindicia\Message\FetchPaymentMethodRequest;
use Omnipay\Vindicia\Message\FetchTransactionRequest;
use Omnipay\Vindicia\TestFramework\DataFaker;
use Omnipay\Vindicia\TestFramework\SoapTestCase;

class ObjectHelperTest extends SoapTestCase
{
    /**
     * @dataProvider provideBuildCreditCard
     * @return void
     */
    public function testBuildCreditCard(array $response_info, array $expected_results)
    {
        $request = new FetchPaymentMethodRequest($this->getHttpClient(), $this->getHttpRequest());
        $request->initialize(
            array(
                'paymentMethodId' => $response_info['params']['PAYMENT_METHOD_ID']
            )
        );
        $this->setMockSoapResponse($response_info['file'], $response_info['params']);

        $response = $request->send();
        $data = $response->getData();
        $data = isset($data->account) ? $data->account->paymentMethods : array($data->paymentMethod);
        self::assertCount(1, $data);

        $payment_object = $data[0];
        $objectHelper = new ObjectHelper();
        $card = $objectHelper->buildCreditCard($payment_object);

        self::assertInstanceOf('\Omnipay\Vindicia\NonStrippingCreditCard', $card);
        self::assertSame($expected_results['brand'], $card->getBrand());
        self::assertSame($expected_results['number'], $card->getNumber());
        self::assertSame($expected_results['last_four'], $card->getNumberLastFour());
        self::assertSame($expected_results['expiry_month'], $card->getExpiryMonth());
        self::assertSame($expected_results['expiry_year'], $card->getExpiryYear());
        self::assertSame($expected_results['expiry_year'], $card->getExpiryYear());
    }

    /**
     * @return array[][]
     */
    public function provideBuildCreditCard()
    {
        $faker = new DataFaker();

        $paymentMethodId = $faker->paymentMethodId();
        $paymentData = $faker->applePayPaymentData();
        $transactionReference = $faker->transactionId();
        $paymentMethodReference = $faker->paymentMethodReference();
        $card = $faker->card();
        $card_object = new CreditCard($card);

        return array(
            array(
                'response_info' => array(
                    'file' => 'FetchApplePayPaymentMethodSuccess.xml',
                    'params' => array(
                        'EXPIRATION_DATE' => $card['expiryYear'] . $card['expiryMonth'],
                        'PAYMENT_METHOD_ID' => $paymentMethodId,
                        'PAYMENT_METHOD_REFERENCE' => $paymentMethodReference,
                        'PAYMENT_INSTRUMENT_NAME' => CreditCard::BRAND_MASTERCARD . ' 1234',
                        'PAYMENT_NETWORK' => CreditCard::BRAND_MASTERCARD,
                        'TRANSACTION_REFERENCE' => $transactionReference,
                        'PAYMENT_DATA' => $paymentData,
                        'COUNTRY' => $card['country'],
                        'POSTCODE' => $card['postcode']
                    ),
                ),
                'expected_info' => array(
                    'brand' => CreditCard::BRAND_MASTERCARD,
                    'number' => '1234',
                    'last_four' => '1234',
                    'expiry_month' => (int) $card['expiryMonth'],
                    'expiry_year' => (int) $card['expiryYear']
                )
            ),
            array(
                'response_info' => array(
                    'file' => 'FetchPaymentMethodSuccess.xml',
                    'params' => array(
                        'PAYMENT_METHOD_ID' => $paymentMethodId,
                        'PAYMENT_METHOD_REFERENCE' => $paymentMethodReference,
                        'CARD_NUMBER' => $card['number'],
                        'CARD_FIRST_SIX' => substr($card['number'], 0, 6),
                        'CARD_LAST_FOUR' => substr($card['number'], -4),
                        'EXPIRY_MONTH' => $card['expiryMonth'],
                        'EXPIRY_YEAR' => $card['expiryYear'],
                        'COUNTRY' => $card['country'],
                        'POSTCODE' => $card['postcode']
                    )
                ),
                'expected_info' => array(
                    'brand' => $card_object->getBrand(),
                    'number' => $card['number'],
                    'last_four' => substr($card['number'], -4),
                    'expiry_month' => (int) $card['expiryMonth'],
                    'expiry_year' => (int) $card['expiryYear']
                )
            )
        );
    }

    /**
     * @dataProvider provideBuildTransaction
     * @return void
     */
    public function testBuildTransaction(array $response_info, array $expected_results)
    {
        $request = new FetchTransactionRequest($this->getHttpClient(), $this->getHttpRequest());
        $request->initialize(
            array(
                'transactionId' => $response_info['params']['TRANSACTION_ID']
            )
        );
        $this->setMockSoapResponse($response_info['file'], $response_info['params']);

        $response = $request->send();
        $data = $response->getData();

        $objectHelper = new ObjectHelper();
        $transaction = $objectHelper->buildTransaction($data->transaction);

        self::assertInstanceOf('\Omnipay\Vindicia\Transaction', $transaction);
        self::assertSame($expected_results['transaction_id'], $transaction->getTransactionId());
        self::assertSame($expected_results['timestamp'], $transaction->getTimestamp());
        $items = $transaction->getItems();
        $this->assertSame(2, count($items));
        foreach ($items as $i => $item) {
            $this->assertInstanceOf('\Omnipay\Vindicia\VindiciaItem', $item);
            if ($i == 0) {
                $this->assertSame($item->getAutoBillItemVid(), $expected_results['autobill_item_vid']);
                $this->assertSame($expected_results['price'], $item->getPrice());
            } else if ($i === 1) {
                $this->assertSame('Total Tax', $item->getSku());
                $this->assertSame('0', $item->getPrice());
            }
        }
    }

    public function provideBuildTransaction()
    {
        $faker = new DataFaker();

        $transactionId = $faker->transactionId();
        $transactionReference = $faker->transactionReference();
        $timestamp = date('Y-m-d\T12:00:00-04:00');
        $autoBillItemVid = $faker->autoBillItemVid();

        return array(
            array(
                'response_info' => array(
                    'file' => 'FetchTransactionByReferenceSuccess.xml',
                    'params' => array(
                        'TRANSACTION_ID' => $transactionId,
                        'TRANSACTION_REFERENCE' => $transactionReference,
                        'TIMESTAMP' => $timestamp,
                        'AUTOBILL_ITEM_VID' => $autoBillItemVid
                    ),
                ),
                'expected_info' => array(
                    'transaction_id' => $transactionId,
                    'timestamp' => $timestamp,
                    'price' => '200',
                    'autobill_item_vid' => $autoBillItemVid
                )
            ),
            array(
                'response_info' => array(
                    'file' => 'FetchTransactionSuccess.xml',
                    'params' => array(
                        'TRANSACTION_ID' => $transactionId,
                        'TRANSACTION_REFERENCE' => $transactionReference,
                        'TIMESTAMP' => $timestamp,
                        'TAX_AMOUNT' => 0,
                        'AMOUNT' => 200
                    ),
                ),
                'expected_info' => array(
                    'transaction_id' => $transactionId,
                    'timestamp' => $timestamp,
                    'price' => '200',
                    'autobill_item_vid' => null
                )
            )
        );
    }
}
