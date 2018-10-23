<?php

namespace Omnipay\Vindicia\Message;

use Omnipay\Common\Exception\InvalidRequestException;

/**
 * Calculate the sales tax for a potential transaction.
 *
 * This is useful if you want to show the sales tax on your site before the purchase.
 *
 * Parameters:
 * - taxClassification: The tax classification to use for calculation. Values may vary depending
 * on your tax engine, consult with Vindicia to learn what values are available to you.
 * Common options include 'TaxExempt' (default) and 'OtherTaxable'. Can be overriden by a
 * taxClassification parameter in an item.
 * - amount: The amount to calculate tax based on. Either the amount or items parameter is required.
 * - items: Line-items for the transaction. Either the amount or items parameter is required.
 * Each item must contain a sku, price, quantity, and name. A description and taxClassification
 * are optional. If a taxClassification is specified, it will take precedence over the request-
 * level taxClassification parameter.
 * - currency: The three letter (capitalized) currency code for the amount, eg) 'USD'
 * If not specified, the default will be left up to Vindicia, which will probably be USD for
 * most users.
 * - card: Card details are not necessary, but this parameter can be used to provide the address
 * details to determine where the tax will be applied and what rate should be used.
 * - customerId: If the customer id or reference is provided, Vindicia can check them for
 * tax exemptions. (optional)
 * - customerReference: If the customer id or reference is provided, Vindicia can check them for
 * tax exemptions. (optional)
 *
 * Example:
 * <code>
 *   // set up the gateway
 *   $gateway = \Omnipay\Omnipay::create('Vindicia');
 *   $gateway->setUsername('your_username');
 *   $gateway->setPassword('y0ur_p4ssw0rd');
 *   $gateway->setTestMode(false);
 *
 *   $response = $gateway->calculateSalesTax(array(
 *       'items' => array(
 *           array('name' => 'Item 1', 'sku' => '1', 'price' => '4.00', 'quantity' => 1),
 *           array('name' => 'Item 2', 'sku' => '2', 'price' => '3.00', 'quantity' => 2)
 *       ),
 *       'amount' => '10.00', // not necessary since items are provided
 *       'currency' => 'USD',
 *       'card' => array(
 *           'country' => 'FR'
 *           // you can specify other address components, such as 'postcode', if desired
 *       ),
 *       'taxClassification' => 'OtherTaxable'
 *   ))->send();
 *
 *   if ($response->isSuccessful()) {
 *       echo "Sales tax: " . $response->getSalesTax() . PHP_EOL;
 *   }
 * </code>
 */
class CalculateSalesTaxRequest extends AbstractRequest
{
    /**
     * With the Avalara tax engine, a customer ID is required for tax to be calculated.
     * Vindicia's advice is to pass a dummy ID if you just want to calculate tax
     * without a specific user.
     */
    const DUMMY_CUSTOMER_ID = 'Dummy ID for tax calculation';

    /**
     * @return string
     */
    protected function getObject()
    {
        return self::$TRANSACTION_OBJECT;
    }

    /**
     * The name of the function to be called in Vindicia's API
     *
     * @return string
     */
    protected function getFunction()
    {
        return 'calculateSalesTax';
    }

    public function getData()
    {
        $amount = $this->getAmount();
        $items = $this->getItems();
        if (empty($amount) && empty($items)) {
            throw new InvalidRequestException('Either the amount or items parameter is required.');
        }

        if ($this->getCustomerId() === null && $this->getCustomerReference() === null) {
            $this->setCustomerId(self::DUMMY_CUSTOMER_ID);
        }

        // skip card validation since we only need the address info
        return array(
            'transaction' => $this->buildTransaction(),
            'action' => $this->getFunction()
        );
    }
}
