<?php

namespace Omnipay\Vindicia;

use Omnipay\Common\ItemBag;

/**
 * Class TaxExemptionBag
 *
 * @package Omnipay\Vindicia
 */
class TaxExemptionBag extends ItemBag
{
    /**
     * Add an item to the bag
     *
     * @param  TaxExemption|array $item An existing item, or associative array of item parameters
     * @return void
     */
    public function add($item)
    {
        if ($item instanceof TaxExemption) {
            $this->items[] = $item;
        } else {
            $this->items[] = new TaxExemption($item);
        }
    }
}
