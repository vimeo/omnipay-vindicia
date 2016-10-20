<?php

namespace Omnipay\Vindicia\Message;

/**
 * Calculate the sales tax for a potential transaction.
 *
 * This is useful if you want to show the sales tax on your site before the purchase.
 *
 * Parameters:
 * - taxClassification: The tax classification to use for calculation. Values may vary depending
 * on your tax engine, consult with Vindicia to learn what values are available to you.
 * Common options include 'TaxExempt' (default) and 'OtherTaxable'.
 * - amount: The amount to calculate tax based on. Required.
 * - currency: The three letter (capitalized) currency code for the amount, eg) 'USD'
 * If not specified, the default will be left up to Vindicia, which will probably be USD for
 * most users.
 * - card: Card details are not necessary, but this parameter can be used to provide the address
 * details to determine where the tax will be applied and what rate should be used.
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
 *       'amount' => '10.00',
 *       'currency' => 'USD',
 *       'card' => array(
 *           'country' => 'FR'
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
        $this->validate('amount');

        // skip card validation since we only need the address info
        return array(
            'transaction' => $this->buildTransaction(),
            'action' => $this->getFunction()
        );
    }
}
