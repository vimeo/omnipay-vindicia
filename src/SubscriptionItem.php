<?php

namespace Omnipay\Vindicia;

use Omnipay\Common\Item;
use Omnipay\Vindicia\Exception\InvalidItemException;

/**
 * Class SubscriptionItem
 *
 * @package Omnipay\Vindicia
 */
class SubscriptionItem extends Item
{
    /**
     * Get the item sku
     *
     * @return null|string
     */
    public function getSku()
    {
        return $this->getParameter('sku');
    }

    /**
     * Set the item sku
     *
     * @param string $value
     * @return static
     */
    public function setSku($value)
    {
        return $this->setParameter('sku', $value);
    }

    /**
     * Get the item currency
     *
     * @return null|string
     */
    public function getCurrency()
    {
        return $this->getParameter('sku');
    }

    /**
     * Set the item currency
     *
     * @param string $value
     * @return static
     */
    public function setCurrency($value)
    {
        return $this->setParameter('currency', $value);
    }

    /**
     * @return void
     */
    public function validate()
    {
        if ($this->getSku() === null) {
            throw new InvalidItemException('Item is missing sku.');
        }
        if ($this->getCurrency() === null) {
            throw new InvalidItemException('Item is missing currency.');
        }
    }
}
