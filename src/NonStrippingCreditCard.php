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
     * Sets the transaction identifier.
     *
     * @param string $value
     * @return static
     */
    public function setTransactionIdentifier($value)
    {
        /**
         * @var static
         */
        return $this->setParameter('transactionIdentifier', $value);
    }

    /**
     * Gets the transaction identifier.
     * 
     * @return string|null
     */
    public function getTransactionIdentifier()
    {
        return $this->getParameter('transactionIdentifier');
    }

    /**
     * Sets the country, zip code, expiration date and account holder name.
     *
     * @param string $value
     * @return static
     */
    public function setPaymentData($value)
    {
        /**
         * @var static
         */
        return $this->setParameter('paymentData', $value);
    }

    /**
     * Gets the country, zip code, expiration date and account holder name.
     *
     * @return string|null
     */
    public function getPaymentData()
    {
        return $this->getParameter('paymentData');
    }
}
