<?php

namespace Omnipay\Vindicia\Message;

use stdClass;

/**
 * Complete a HOA request.
 *
 * After you call one of the initializeHOA functions, this function should be called
 * on the page the user is returned to (specified by the returnUrl) to complete the request.
 *
 * Parameters:
 * - webSessionReference: The gateway's identifier for the Web Session. This is provided
 * by the response for an initializeHOA call and is used to specify which Web Session should
 * be completed. Required.
 *
 * See HOAPurchaseRequest for a code example.
 */
class CompleteHOARequest extends AbstractRequest
{
    /**
     * The class to use for the response.
     *
     * @var string
     */
    protected static $RESPONSE_CLASS = '\Omnipay\Vindicia\Message\CompleteHOAResponse';

    /**
     * The name of the function to be called in Vindicia's API
     *
     * @return string
     */
    protected function getFunction()
    {
        return 'finalize';
    }

    /**
     * @return string
     */
    protected function getObject()
    {
        return self::$WEB_SESSION_OBJECT;
    }

    /**
     * Get the gateway's reference to the web session being used for HOA
     *
     * @return null|string
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

    /**
     * Overriding to provide a more precise return type
     * @return CompleteHOAResponse
     */
    public function send()
    {
        /**
         * @var CompleteHOAResponse
         */
        return parent::send();
    }
}
