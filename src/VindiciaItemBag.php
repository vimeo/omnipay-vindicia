<?php

namespace Omnipay\Vindicia;

use Omnipay\Common\ItemBag;
use Omnipay\Common\ItemInterface;

/**
 * Class VindiciaItemBag
 *
 * @package Omnipay\Vindicia
 */
class VindiciaItemBag extends ItemBag
{
    /**
     * Add an item to the bag
     *
     * @param  ItemInterface|array $item An existing item, or associative array of item parameters
     * @return void
     */
    public function add($item)
    {
        if ($item instanceof ItemInterface) {
            $this->items[] = $item;
        } else {
            $this->items[] = new VindiciaItem($item);
        }
    }
}
