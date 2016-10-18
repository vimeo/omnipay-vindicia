<?php

namespace Omnipay\Vindicia;

use Symfony\Component\HttpFoundation\ParameterBag;
use Omnipay\Common\Helper;
use Omnipay\Vindicia\Exception\InvalidItemException;

/**
 * Refund Item
 *
 * This class defines a single refund item. VindiciaRefundItems are used to
 * specify VindiciaItems to refund.
 */
class VindiciaRefundItem
{
    /**
     * @var \Symfony\Component\HttpFoundation\ParameterBag
     */
    protected $parameters;

    /**
     * Create a new item with the specified parameters
     *
     * @param array $parameters An array of parameters to set on the new object
     */
    public function __construct($parameters = array())
    {
        $this->initialize($parameters);
    }

    /**
     * Initialize this item with the specified parameters
     *
     * @param array $parameters An array of parameters to set on this object
     * @return static
     */
    public function initialize($parameters = array())
    {
        $this->parameters = new ParameterBag();

        Helper::initialize($this, $parameters);

        return $this;
    }

    public function getParameters()
    {
        return $this->parameters->all();
    }

    protected function getParameter($key)
    {
        return $this->parameters->get($key);
    }

    protected function setParameter($key, $value)
    {
        $this->parameters->set($key, $value);

        return $this;
    }

    /**
     * Get the amount to refund
     *
     * @return string
     */
    public function getAmount()
    {
        return $this->getParameter('amount');
    }

    /**
     * Set the amount to refund
     *
     * @param string $value
     * @return static
     */
    public function setAmount($value)
    {
        return $this->setParameter('amount', $value);
    }

    /**
     * Get the sku of the item to refund
     *
     * @return string
     */
    public function getSku()
    {
        return $this->getParameter('sku');
    }

    /**
     * Set the sku of the item to refund
     *
     * @param string $value
     * @return static
     */
    public function setSku($value)
    {
        return $this->setParameter('sku', $value);
    }

    /**
     * Get the transaction item index number of the item to refund.
     * This is the index number of the item in the original transaction
     * and can be used as an alternative to sku to identify which item
     * to refund. Indexing starts at 1.
     *
     * @return int
     */
    public function getTransactionItemIndexNumber()
    {
        return $this->getParameter('transactionItemIndexNumber');
    }

    /**
     * Set the transaction item index number of the item to refund.
     * This is the index number of the item in the original transaction
     * and can be used as an alternative to sku to identify which item
     * to refund. Indexing starts at 1.
     *
     * @param int $value
     * @return static
     */
    public function setTransactionItemIndexNumber($value)
    {
        return $this->setParameter('transactionItemIndexNumber', $value);
    }

    /**
     * Get boolean indicating whether you only want to refund the tax for the item
     *
     * @return bool
     */
    public function getTaxOnly()
    {
        return $this->getParameter('taxOnly');
    }

    /**
     * Set to true if you only want to refund the tax for the item
     *
     * @param bool $value
     * @return static
     */
    public function setTaxOnly($value)
    {
        return $this->setParameter('taxOnly', $value);
    }

    public function validate()
    {
        if ($this->getAmount() === null && $this->getTaxOnly() !== true) {
            throw new InvalidItemException('Refund item requires amount if taxOnly is not set to true.');
        }
        if ($this->getTransactionItemIndexNumber() === null && $this->getSku() === null) {
            throw new InvalidItemException('Refund item requires sku or transactionItemIndexNumber.');
        }
    }
}
