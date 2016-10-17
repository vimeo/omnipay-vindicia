<?php

namespace Omnipay\Vindicia\Message;

use stdClass;
use Omnipay\Common\Exception\InvalidRequestException;

class CreateProductRequest extends AbstractRequest
{
    // defines setPrices and getPrices, allowing you to set lists
    // of prices in multiple currencies
    use HasPrices;

    /**
     * Possible values of the duplicateBehavior parameter, used for specifying
     * what to do when a duplicate product is added.
     *
     * @see setDuplicateBehavior
     */
    const BEHAVIOR_DUPLICATE = 'Duplicate';
    const BEHAVIOR_FAIL = 'Fail';
    const BEHAVIOR_SUCCEED_IGNORE = 'SucceedIgnore';

    public function initialize(array $parameters = array())
    {
        if (!array_key_exists('duplicateBehavior', $parameters)) {
            $parameters['duplicateBehavior'] = self::BEHAVIOR_SUCCEED_IGNORE;
        }
        parent::initialize($parameters);

        return $this;
    }

    /**
     * The name of the function to be called in Vindicia's API
     *
     * @return string
     */
    protected function getFunction()
    {
        return 'update';
    }

    protected function getObject()
    {
        return self::$PRODUCT_OBJECT;
    }

    /**
     * Get the behavior if the exact same product is submitted twice with no id or reference
     *
     * @return string
     */
    public function getDuplicateBehavior()
    {
        return $this->getParameter('duplicateBehavior');
    }

    /**
     * Set the behavior if the exact same product is submitted twice with no id or reference
     * Options:
     *   self::BEHAVIOR_DUPLICATE: Create the product as normal, resulting in two identical products
     *   self::BEHAVIOR_FAIL: Does nothing and returns failure
     *   self::BEHAVIOR_SUCCEED_IGNORE: Does nothing and returns success (default)
     *
     * @param string $value
     * @return static
     */
    public function setDuplicateBehavior($value)
    {
        return $this->setParameter('duplicateBehavior', $value);
    }

    public function getData()
    {
        $productId = $this->getProductId();
        $productReference = $this->getProductReference();
        if (!$this->isUpdate()) {
            $this->validate('productId');
        } elseif (!$productId && !$productReference) {
            throw new InvalidRequestException('Either the productId or productReference parameter is required.');
        }

        $product = new stdClass();
        $product->billingStatementIdentifier = $this->getStatementDescriptor();
        $product->merchantProductId = $productId;
        $product->VID = $productReference;
        $product->taxClassification = $this->getTaxClassification();

        $planId = $this->getPlanId();
        $planReference = $this->getPlanReference();
        if (isset($planId) || isset($planReference)) {
            $plan = new stdClass();
            $plan->merchantBillingPlanId = $planId;
            $plan->VID = $planReference;
            $product->defaultBillingPlan = $plan;
        }

        $prices = $this->makePricesForVindicia();
        if (!empty($prices)) {
            $product->prices = $prices;
        }

        $attributes = $this->getAttributes();
        if ($attributes) {
            $product->nameValues = $this->buildNameValues($attributes);
        }

        $data['product'] = $product;
        $data['duplicateBehavior'] = $this->getDuplicateBehavior();
        $data['action'] = $this->getFunction();

        return $data;
    }
}
