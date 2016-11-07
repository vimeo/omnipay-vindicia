<?php
/**
 * The responses for HOA completion are a bit different since
 * they include a response for the method that was actually
 * called by HOA. If the request itself fails, we return its
 * error message and code. Otherwise, we return the status
 * for the method called by HOA. getFailureType can be used to
 * determine if it was a request failure or method failure.
 */
namespace Omnipay\Vindicia\Message;

use Omnipay\Common\Exception\InvalidResponseException;
use Omnipay\Common\Message\RequestInterface;

class CompleteHOAResponse extends Response
{
    /**
     * Since parsing the structure is a bit complex, we parse once in the
     * constructor and then store the results in instance variables.
     */
    /**
     * @var string
     */
    protected $code;
    /**
     * @var string
     */
    protected $message;
    /**
     * @var string|null
     */
    protected $failureType;

    /**
     * Constants to indicate whether it was the HOA request that failed
     * or the method HOA called.
     */
    const REQUEST_FAILURE = 'request_failure';
    const METHOD_FAILURE = 'method_failure';

    /**
     * Constructor
     *
     * @param RequestInterface $request the initiating request.
     * @param mixed $data
     */
    public function __construct(RequestInterface $request, $data)
    {
        parent::__construct($request, $data);

        // if available, we want the response from the method
        if (isset($this->data->session->apiReturn)) {
            $this->code = $this->data->session->apiReturn->returnCode;
            $this->message = $this->data->session->apiReturn->returnString;
            if (!$this->isSuccessful()) {
                $this->failureType = self::METHOD_FAILURE;
            }
            return;
        }

        // otherwise, we want the response from the request
        $this->code = parent::getCode();
        $this->message = parent::getMessage();
        if (!$this->isSuccessful()) {
            $this->failureType = self::REQUEST_FAILURE;
        }
    }

    /**
     * Get the response message from the payment gateway.
     * Throws an exception if it's not present.
     *
     * @return string
     * @throws \Omnipay\Common\Exception\InvalidResponseException
     */
    public function getMessage()
    {
        if (!empty($this->message)) {
            return $this->message;
        }
        throw new InvalidResponseException('Response has no message.');
    }

    /**
     * Get the response code from the payment gateway.
     * Throws an exception if it's not present.
     *
     * @return string
     * @throws \Omnipay\Common\Exception\InvalidResponseException
     */
    public function getCode()
    {
        if (!empty($this->code)) {
            return $this->code;
        }
        throw new InvalidResponseException('Response has no code.');
    }

    /**
     * If the response failed, returns self::REQUEST_FAILURE to indicate that
     * the HOA request failed or self::METHOD_FAILURE to indicate that the
     * method called by HOA failed. Returns null if the request was successful.
     *
     * @return string|null
     */
    public function getFailureType()
    {
        return $this->failureType;
    }

    // @todo will probably need to add some functions to get the responses
    // from the HOA methods
}
