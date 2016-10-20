<?php

namespace Omnipay\Vindicia\Message;

use Omnipay\Common\Exception\InvalidRequestException;

/**
 * Fetch a Vindicia product object.
 *
 * Parameters:
 * - productId: Your identifier for the product to be fetched. Either productId
 * or productReference is required.
 * - productReference: The gateway's identifier for the product to be fetched. Either
 * productId or productReference is required.
 *
 * Example:
 * <code>
 *   // set up the gateway
 *   $gateway = \Omnipay\Omnipay::create('Vindicia');
 *   $gateway->setUsername('your_username');
 *   $gateway->setPassword('y0ur_p4ssw0rd');
 *   $gateway->setTestMode(false);
 *
 *   // create a product using that plan for the user to subscribe to
 *   $productResponse = $gateway->createProduct(array(
 *       'productId' => '123456789', // you choose this
 *       'statementDescriptor' => 'Statement descriptor',
 *       'duplicateBehavior' => CreateProductRequest::BEHAVIOR_SUCCEED_IGNORE,
 *       'prices' => array(
 *           'USD' => '9.99',
 *           'CAD' => '12.99'
 *       )
 *   ))->send();
 *
 *   if ($productResponse->isSuccessful()) {
 *       echo "Product id: " . $productResponse->getProductId() . PHP_EOL;
 *       echo "Product reference: " . $productResponse->getProductReference() . PHP_EOL;
 *   } else {
 *       // error handling
 *   }
 *
 *   // now we want to fetch the product for some reason
 *   $fetchResponse = $gateway->fetchProduct(array(
 *       'productId' => '123456'
 *   ))->send();
 *
 *   if ($fetchResponse->isSuccessful()) {
 *       var_dump($fetchResponse->getProduct());
 *   }
 *
 * </code>
 */
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
