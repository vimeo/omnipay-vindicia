# Omnipay: Vindicia

**Vindicia driver for the Omnipay PHP payment processing library**

[Omnipay](https://github.com/thephpleague/omnipay) is a framework agnostic, multi-gateway payment
processing library for PHP 5.3+. This package implements Vindicia support for Omnipay.

## Installation

Omnipay is installed via [Composer](http://getcomposer.org/). To install, simply add it
to your `composer.json` file:

```json
{
    "require": {
        "omnipay/vindicia": "~2.0"
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

Unlike many gateways, Vindicia **requires** that a customer be created before a purchase can be made.

### Simple Example

```php
$gateway = \Omnipay\Omnipay::create('Vindicia');
$gateway->setUsername('your_username');
$gateway->setPassword('y0ur_p4ssw0rd');
$gateway->setTestMode(false);

$createCustomerResponse = $gateway->createCustomer(array(
    'name' => 'Test Customer',
    'email' => 'customer@example.com',
    'customerId' => '123456789'
))->send();

if ($createCustomerResponse->isSuccessful()) {
    echo "Customer id: " . $createCustomerResponse->getCustomerId() . PHP_EOL;
    echo "Customer reference: " . $createCustomerResponse->getCustomerReference() . PHP_EOL;
} else {
    // error handling
}

$purchaseResponse = $gateway->purchase(array(
    'amount' => '9.95',
    'currency' => 'USD',
    'customerId' => $createCustomerResponse->getCustomerId(),
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
