<?php

namespace Omnipay\Vindicia\Exception;

use Omnipay\Common\Exception\OmnipayException;

/**
 * Invalid Price Bag Exception
 *
 * Thrown when a price bag is invalid (typically because it contains multiple
 * price for the same currency)
 */
class InvalidPriceBagException extends \Exception implements OmnipayException
{
}
