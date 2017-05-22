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
     * Get the item reference
     *
     * @return null|string
     */
    public function getReference()
    {
        return $this->getParameter('reference');
    }

    /**
     * Set the item reference
     *
     * @param string $value
     * @return static
     */
    public function setReference($value)
    {
        return $this->setParameter('reference', $value);
    }

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
        return $this->getParameter('currency');
    }

    /**
     * Set the item sku
     *
     * @param string $value
     * @return static
     */
    public function setCurrency($value)
    {
        return $this->setParameter('currency', $value);
    }

    /**
     * Get the item tax classification
     *
     * @return null|string
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

    /**
     * @return void
     */
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
