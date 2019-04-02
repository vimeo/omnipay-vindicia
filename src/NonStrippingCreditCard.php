<?php

namespace Omnipay\Vindicia;

use Omnipay\Common\CreditCard;

/**
 * Extension of the Credit Card class that does NOT strip non-numeric
 * characters from the credit card number. This is useful for handling
 * responses from Vindicia, since Vindicia masks card numbers with X's.
 */
class NonStrippingCreditCard extends CreditCard
{
    /**
     * Set Card Number
     *
     * Unlike the parent class, non-numeric characters are NOT stripped out of
     * the card number, so it's NOT safe to pass in strings such as
     * "4444-3333 2222 1111" etc.
     *
     * @param string $value
     * @return static
     */
    public function setNumber($value)
    {
        /**
         * @var static
         */
        return $this->setParameter('number', $value);
    }

    /**
     * Sets the payment instrument name.
     *
     * @param string $value
     * @return static
     */
    public function setPaymentInstrumentName($value)
    {
        /**
         * @var static
         */
        return $this->setParameter('paymentInstrumentName', $value);
    }

    /**
     * Gets the payment instrument name.
     *
     * @return string|null
     */
    public function getPaymentInstrumentName()
    {
        return $this->getParameter('paymentInstrumentName');
    }

    /**
     * Sets the payment network.
     *
     * @param string $value
     * @return static
     */
    public function setPaymentNetwork($value)
    {
        /**
         * @var static
         */
        return $this->setParameter('paymentNetwork', $value);
    }

    /**
     * Gets the payment network.
     *
     * @return string|null
     */
    public function getPaymentNetwork()
    {
        return $this->getParameter('paymentNetwork');
    }

    /**
     * Sets Apple Pay's transaction reference.
     * Apple Pay calls this a transactionIdentifier.
     *
     * @param string $value
     * @return static
     */
    public function setApplePayTransactionReference($value)
    {
        /**
         * @var static
         */
        return $this->setParameter('applePayTransactionReference', $value);
    }

    /**
     * Gets Apple Pay's transaction reference.
     * Apple Pay calls this a transactionIdentifier.
     * 
     * @return string|null
     */
    public function getApplePayTransactionReference()
    {
        return $this->getParameter('applePayTransactionReference');
    }

    /**
     * Sets the token receieved from Apple Pay payment sheet.
     * Includes the country, zip code, expiration date and account holder name.
     *
     * @param string $value
     * @return static
     */
    public function setToken($value)
    {
        /**
         * @var static
         */
        return $this->setParameter('token', $value);
    }

    /**
     * Gets the token receieved from Apple Pay payment sheet.
     * Includes the country, zip code, expiration date and account holder name.
     *
     * @return string|null
     */
    public function getToken()
    {
        return $this->getParameter('token');
    }
}
