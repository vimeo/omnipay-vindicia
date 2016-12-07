<?php

namespace Omnipay\Vindicia\Message;

class CreatePaymentMethodResponse extends Response
{
    protected static $SUCCESS_CODES = array(
        200,
        228, // Payment method saved but missing associated account - unable to replace on autobills
        261  // All active AutoBills were updated. AutoBills which are both expired and Suspended cannot be updated.
    );
}
