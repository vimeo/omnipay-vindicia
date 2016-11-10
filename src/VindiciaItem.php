<?php

namespace Omnipay\Vindicia;

use Omnipay\Common\Item;
use Omnipay\Vindicia\Exception\InvalidItemException;

/**
 * Class VindiciaItem
 *
 * @package Omnipay\Vindicia
 */
class VindiciaItem extends Item
{
    /**
     * Get the item sku
     *
     * @return string
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
     * Get the item tax classification
     *
     * @return string
     */
    public function getTaxClassification()
    {
        return $this->getParameter('taxClassification');
    }

    /**
     * Set the item tax classification
     *
     * @param string $value
     * @return static
     */
    public function setTaxClassification($value)
    {
        return $this->setParameter('taxClassification', $value);
    }

    public function validate()
    {
        if ($this->getName() === null) {
            throw new InvalidItemException('Item is missing name.');
        }
        if ($this->getPrice() === null) {
            throw new InvalidItemException('Item is missing price.');
        }
        if ($this->getQuantity() === null) {
            throw new InvalidItemException('Item is missing quantity.');
        }
        if ($this->getSku() === null) {
            throw new InvalidItemException('Item is missing sku.');
        }
    }
}
