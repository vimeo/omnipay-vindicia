<?php

namespace Omnipay\Vindicia\Message;

class PayPalPurchaseResponse extends Response
{
    /**
     * Is the response successful?
     * Throws an exception if there's no code.
     *
     * @return boolean
     * @throws \Omnipay\Common\Exception\InvalidResponseException
     */
    public function isSuccessful()
    {
        return intval($this->getCode()) === self::SUCCESS_CODE && $this->getRedirectUrl();
    }

    /**
     * Is the response a redirect?
     * Throws an exception if there's no code.
     * Successful PayPal purchase responses are always redirects.
     *
     * @return boolean
     * @throws \Omnipay\Common\Exception\InvalidResponseException
     */
    public function isRedirect()
    {
        return $this->isSuccessful();
    }

    public function getRedirectUrl()
    {
        $transaction = $this->getTransaction();
        if (isset($transaction) && isset($transaction->statusLog)) {
            foreach ($transaction->statusLog as $status) {
                if (isset($status->status)
                    && $status->status = 'AuthorizationPending'
                    && isset($status->payPalStatus->redirectUrl)
                ) {
                    return $status->payPalStatus->redirectUrl;
                }
            }
        }
        return null;
    }
}
