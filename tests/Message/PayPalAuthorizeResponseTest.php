<?php

namespace Omnipay\Vindicia\Message;

use Omnipay\Vindicia\TestFramework\Mocker;
use Omnipay\Vindicia\TestFramework\SoapTestCase;

class PayPalAuthorizeResponseTest extends SoapTestCase
{
    /**
     * @dataProvider provideForTestIsSuccessful
     */
    public function testIsSuccessful(string $return_code, ?string $redirect_url, bool $expected_is_successful): void
    {
        $response = Mocker::mock('\Omnipay\Vindicia\Message\PayPalAuthorizeResponse', [
            'getCode' => $return_code,
            'getRedirectUrl' => $redirect_url,
        ])->makePartial();
        $this->assertSame($expected_is_successful, $response->isSuccessful());
    }

    public function provideForTestIsSuccessful(): array
    {
        return [
            '200 return code with redirect url indicates success' => [
                'return_code' => '200',
                'redirect_url' => 'https://www.sandbox.paypal.com/webscr?cmd=_express-checkout&useraction=commit&token=1234',
                'expected_is_successful' => true,
            ],
            'redirect url is required' => [
                'return_code' => '200',
                'redirect_url' => null,
                'expected_is_successful' => false,
            ],
            '202 on an authorize means that the tax service was unavailable, but the request still succeeded' => [
                'return_code' => '202',
                'redirect_url' => 'https://www.sandbox.paypal.com/webscr?cmd=_express-checkout&useraction=commit&token=1234',
                'expected_is_successful' => true,
            ],
            '400 return code indicates failure' => [
                'return_code' => '400',
                'redirect_url' => 'https://www.sandbox.paypal.com/webscr?cmd=_express-checkout&useraction=commit&token=1234',
                'expected_is_successful' => false,
            ],
        ];
    }

    /**
     * @dataProvider provideForTestIsRedirect
     */
    public function testIsRedirect(bool $is_successful, bool $expected_is_redirect): void
    {
        $response = Mocker::mock('\Omnipay\Vindicia\Message\PayPalAuthorizeResponse', [
            'isSuccessful' => $is_successful,
        ])->makePartial();
        $this->assertSame($expected_is_redirect, $response->isRedirect());
    }

    public function provideForTestIsRedirect(): array
    {
        // PayPal auths are always redirects so long as they are successful
        return [
            ['is_success' => true, 'expected_is_redirect' => true],
            ['is_success' => false, 'expected_is_redirect' => false],
        ];
    }
}
