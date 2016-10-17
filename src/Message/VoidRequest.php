<?php

namespace Omnipay\Vindicia\Message;

class VoidRequest extends CaptureRequest
{
    /**
     * The name of the function to be called in Vindicia's API
     *
     * @return string
     */
    protected function getFunction()
    {
        return 'cancel';
    }
}
