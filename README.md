# Omnipay: Vindicia

**Vindicia driver for the Omnipay PHP payment processing library**

[![Build Status](https://travis-ci.org/vimeo/omnipay-vindicia.png?branch=master)](https://travis-ci.org/vimeo/omnipay-vindicia)
[![Latest Stable Version](https://poser.pugx.org/vimeo/omnipay-vindicia/version.png)](https://packagist.org/packages/vimeo/omnipay-vindicia)
[![Total Downloads](https://poser.pugx.org/vimeo/omnipay-vindicia/d/total.png)](https://packagist.org/packages/vimeo/omnipay-vindicia)

[Omnipay](https://github.com/thephpleague/omnipay) is a framework agnostic, multi-gateway payment
processing library for PHP 5.3+. This package implements Vindicia support for Omnipay.

[Vindicia](https://vindicia.com/) is a payment services provider founded in Redwood City, CA in 2003 and focused on subscription billing. This driver interfaces with [Cashbox](https://www.vindicia.com/solutions/vindicia-cashbox), their subscription billing platform.

## Installation

Omnipay is installed via [Composer](http://getcomposer.org/). To install, simply add it
to your `composer.json` file:

```json
{
    "require": {
        "vimeo/omnipay-vindicia": "~2.0"
    }
}
```

And run composer to update your dependencies:

```
$ curl -s http://getcomposer.org/installer | php
$ php composer.phar update
```

## Basic Usage

The following gateways are provided by this package:

* [Vindicia](https://vindicia.com/)
* Vindicia_PayPal (Vindicia's PayPal Express implementation)
* Vindicia_HOA *[in progress]* ([Hosted Order Automation](https://www.vindicia.com/resources/data-sheets/hosted-order-automation), Vindicia's solution for minimizing your PCI compliance burden)

**NOTE:** Unlike many gateways, Vindicia requires that every purchase have a corresponding customer. Therefore, the `customerId` or `customerReference` must be provided for every authorize or purchase request. If you pass a `customerId` that does not exist, Vindicia will create the customer object for you as part of the same request. (A `customerReference` that does not exist is an error.)

### Simple Example

```php
$gateway = \Omnipay\Omnipay::create('Vindicia');
$gateway->setUsername('your_username');
$gateway->setPassword('y0ur_p4ssw0rd');
$gateway->setTestMode(false);

$response = $gateway->purchase(array(
    'amount' => '9.95',
    'currency' => 'USD',
    'customerId' => '123456', // if the customer does not exist, it will be created
    'card' => array(
        'number' => '5555555555554444',
        'expiryMonth' => '01',
        'expiryYear' => '2020',
        'cvv' => '123'
    ),
    'paymentMethodId' => 'cc-123456' // this ID will be assigned to the card
))->send();

if ($response->isSuccessful()) {
    echo "Transaction id: " . $purchaseResponse->getTransactionId() . PHP_EOL;
    echo "Transaction reference: " . $purchaseResponse->getTransactionReference() . PHP_EOL;
}
```

More documentation and examples are provided in the Gateway source files.

For general usage instructions, please see the main [Omnipay](https://github.com/thephpleague/omnipay)
repository.

## Test Mode

Vindicia accounts have a separate username and password for test mode. There is also a separate
test mode endpoint, which this library will use when set to test mode.

## Support

This driver is very new and at this point may not be completely reliable. If you believe you have found a bug, please report it using the [GitHub issue tracker](https://github.com/Vimeo/omnipay-vindicia/issues),
or better yet, fork the library and submit a pull request.

If you are having general issues with Omnipay, we suggest posting on
[Stack Overflow](http://stackoverflow.com/). Be sure to add the
[omnipay tag](http://stackoverflow.com/questions/tagged/omnipay) so it can be easily found.

If you want to keep up to date with release announcements, discuss ideas for the project,
or ask more detailed questions, there is also a [mailing list](https://groups.google.com/forum/#!forum/omnipay) which
you can subscribe to.
