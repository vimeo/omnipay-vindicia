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
        return $this->setParameter('number', $value);
    }
}
