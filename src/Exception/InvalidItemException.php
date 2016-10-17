<?php

namespace Omnipay\Vindicia\Exception;

use Omnipay\Common\Exception\OmnipayException;

/**
 * Invalid Item Exception
 *
 * Thrown when an item is invalid/missing required fields
 */
class InvalidItemException extends \Exception implements OmnipayException
{
}
