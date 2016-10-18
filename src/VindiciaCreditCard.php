<?php

namespace Omnipay\Vindicia;

use Omnipay\Common\CreditCard;

class VindiciaCreditCard extends CreditCard
{
    /**
     * A list of attributes
     *
     * @return AttributeBag|null
     */
    public function getAttributes()
    {
        return $this->getParameter('attributes');
    }

    /**
     * Set the attributes in this order
     *
     * @param AttributeBag|array $attributes
     * @return static
     */
    public function setAttributes($attributes)
    {
        if ($attributes && !$attributes instanceof AttributeBag) {
            $attributes = new AttributeBag($attributes);
        }

        return $this->setParameter('attributes', $attributes);
    }

    /**
     * Override to document the correct return type.
     *
     * @return static
     */
    protected function setParameter($key, $value)
    {
        /**
         * @var VindiciaCreditCard
         */
        return parent::setParameter($key, $value);
    }
}
