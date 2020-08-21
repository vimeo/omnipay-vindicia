<?php

namespace Omnipay\Vindicia;

use Omnipay\Common\CreditCard;
use Omnipay\Vindicia\Message\FetchPaymentMethodRequest;
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
}
