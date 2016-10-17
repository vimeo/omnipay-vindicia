<?php

namespace Omnipay\Vindicia\Message;

use Omnipay\Common\Exception\InvalidRequestException;

class FetchProductRequest extends AbstractRequest
{
    /**
     * The name of the function to be called in Vindicia's API
     *
     * @return string
     */
    protected function getFunction()
    {
        return $this->getProductId() ? 'fetchByMerchantProductId' : 'fetchByVid';
    }

    protected function getObject()
    {
        return self::$PRODUCT_OBJECT;
    }

    public function getData()
    {
        $productId = $this->getProductId();
        $productReference = $this->getProductReference();

        if (!$productId && !$productReference) {
            throw new InvalidRequestException('Either the productId or productReference parameter is required.');
        }

        $data = array(
            'action' => $this->getFunction()
        );

        if ($productId) {
            $data['merchantProductId'] = $this->getProductId();
        } else {
            $data['vid'] = $this->getProductReference();
        }

        return $data;
    }
}
