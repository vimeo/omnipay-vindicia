<?php

namespace Omnipay\Vindicia;

use Omnipay\Common\Helper;
use Symfony\Component\HttpFoundation\ParameterBag;

class EcpAccount
{
    /**
     * Internal storage of all of the account parameters.
     *
     * @var \Symfony\Component\HttpFoundation\ParameterBag
     */
    protected $parameters;
    
    public function __construct($parameters = null)
    {
        $this->initialize($parameters);
    }

    /**
     * Initialize the object with parameters.
     * If any unknown parameters passed, they will be ignored.
     */
    public function initialize(array $parameters = null): self
    {
        $this->parameters = new ParameterBag;

        Helper::initialize($this, $parameters);

        return $this;
    }

    public function getParameters(): array
    {
        return $this->parameters->all();
    }

    /**
     * @return mixed
     */
    protected function getParameter($key)
    {
        return $this->parameters->get($key);
    }

    protected function setParameter($key, $value): self
    {
        $this->parameters->set($key, $value);

        return $this;
    }

    public function getMaskedAccountNumber(): string
    {
        return (string)$this->getParameter('maskedAccountNumber');
    }

    public function setMaskedAccountNumber(string $value): self
    {
        return $this->setParameter('maskedAccountNumber', $value);
    }

    public function getRoutingNumber(): string
    {
        return (string)$this->getParameter('routingNumber');
    }

    public function setRoutingNumber(string $value): self
    {
        return $this->setParameter('routingNumber', $value);
    }

    public function getAccountType(): string
    {
        return (string)$this->getParameter('accountType');
    }

    public function setAccountType(string $value): self
    {
        return $this->setParameter('accountType', $value);
    }

    public function getBillingPostcode(): string
    {
        return (string)$this->getParameter('billingPostcode');
    }

    public function setBillingPostcode(string $value): self
    {
        return $this->setParameter('billingPostcode', $value);
    }

    public function getBillingCountry(): string
    {
        return (string)$this->getParameter('billingCountry');
    }

    public function setBillingCountry(string $value): self
    {
        return $this->setParameter('billingCountry', $value);
    }
}
