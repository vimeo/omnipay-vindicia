<?php

namespace Omnipay\Vindicia\Message;

use stdClass;
use Omnipay\Common\Exception\InvalidRequestException;

/**
 * Create a new product or update an existing one (if called via updateProduct).
 *
 * Products are attached to subscriptions to define what the customer is purchasing
 * and what the price is in different currencies.
 *
 * Parameters:
 * - productId: Your identifier to represent the product.
 * - planId: Your identifier to represent the default billing plan for this product.
 * - planReference: The gateway's identifier to represent the default billing plan for this
 * product.
 * - prices: Prices in multiple currencies for this product. If provided, this will override
 * the prices specified by a plan.
 * - taxClassification: The tax classification of the plan. Values may vary depending
 * on your tax engine, consult with Vindicia to learn what values are available to you.
 * Common options include 'TaxExempt' (default) and 'OtherTaxable'.
 * - taxClassification: The tax classification of the plan. Values may vary depending
 * on your tax engine, consult with Vindicia to learn what values are available to you.
 * Common options include 'TaxExempt' (default) and 'OtherTaxable'.
 * - statementDescriptor: The description shown on the customers billing statement from the bank
 * This field’s value and its format are constrained by your payment processor; consult with
 * Vindicia Client Services before setting the value.
 * - attributes: Custom values you wish to have stored with the plan. They have
 * no affect on anything.
 * - duplicateBehavior: The behavior when the exact same product is submitted twice
 * with no id or reference. Options:
 *   CreateProductRequest::BEHAVIOR_DUPLICATE: Create the product as normal, resulting in two
 *       identical products
 *   CreateProductRequest::BEHAVIOR_FAIL: Does nothing and returns failure
 *   CreateProductRequest::BEHAVIOR_SUCCEED_IGNORE: Does nothing and returns success (default)
 *
 * See CreateSubscriptionRequest for a code example.
 */
class CreateProductRequest extends AbstractRequest
{
    /**
     * Possible values of the duplicateBehavior parameter, used for specifying
     * what to do when a duplicate product is added.
     *
     * @see setDuplicateBehavior
     */
    const BEHAVIOR_DUPLICATE = 'Duplicate';
    const BEHAVIOR_FAIL = 'Fail';
    const BEHAVIOR_SUCCEED_IGNORE = 'SucceedIgnore';

    /**
     * @return CreateProductRequest
     */
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

    /**
     * @return string
     */
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

        return array(
            'product' => $product,
            'duplicateBehavior' => $this->getDuplicateBehavior(),
            'action' => $this->getFunction()
        );
    }
}
