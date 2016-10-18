<?php

namespace Omnipay\Vindicia\Message;

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

    /**
     * @psalm-suppress TooManyArguments because psalm can't see validate's func_get_args call
     */
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
