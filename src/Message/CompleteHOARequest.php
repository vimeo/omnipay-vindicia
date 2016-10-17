<?php

namespace Omnipay\Vindicia\Message;

use stdClass;

class CompleteHOARequest extends AbstractRequest
{
    /**
     * The name of the function to be called in Vindicia's API
     *
     * @return string
     */
    protected function getFunction()
    {
        return 'finalize';
    }

    protected function getObject()
    {
        return self::$WEB_SESSION_OBJECT;
    }

    /**
     * Get the gateway's reference to the web session being used for HOA
     *
     * @return string
     */
    public function getWebSessionReference()
    {
        return $this->getParameter('webSessionReference');
    }

    /**
     * Set the gateway's reference to the web session being used for HOA
     *
     * @param string $value
     * @return static
     */
    public function setWebSessionReference($value)
    {
        return $this->setParameter('webSessionReference', $value);
    }

    public function getData()
    {
        $this->validate('webSessionReference');

        $session = new stdClass();
        $session->VID = $this->getWebSessionReference();

        $data = array();
        $data['session'] = $session;
        $data['action'] = $this->getFunction();

        return $data;
    }

    protected function buildResponse($response)
    {
        return new CompleteHOAResponse($this, $response);
    }
}
