<?php
/**
 * Extension of PHP's SoapClient that allows you to override the next
 * response you will get from a SoapClient, which is useful for unit
 * testing (see Omnipay\VindiciaTest\SoapTestCase).
 *
 * Basic usage:
 *
 * 1. (optional) Call setNextResponse() to set the next response that
 * should be returned. Skip this step if you're not testing.
 * 2. Construct the TestableSoapClient as you would a regular SoapClient.
 * 3. Call __soapCall as you would with a regular SoapClient.
 * 4. After this, you can call getLastOptions/FunctionName/Arguments/Wsdl
 * to see what components made up the request
 */
namespace Omnipay\Vindicia;

use Omnipay\Common\Exception\BadMethodCallException;
use SoapClient;

class TestableSoapClient extends SoapClient
{
    protected static $nextResponseOverride;
    protected static $lastWsdl;
    protected static $lastOptions;
    protected static $lastFunctionName;
    protected static $lastArguments;

    /**
     * @psalm-suppress UndefinedMethod SoapClient doesn't have __construct. PHP knows to look for SoapClient,
     * Psalm does not.
     */
    public function __construct($wsdl, array $options = null)
    {
        if (!isset(self::$nextResponseOverride)) {
            parent::__construct($wsdl, $options);
        } else {
            self::$lastWsdl = $wsdl;
            self::$lastOptions = $options;
        }
    }

    public function __soapCall(
        $function_name,
        $arguments,
        $options = null,
        $input_headers = null,
        &$output_headers = null
    ) {
        if (isset(self::$nextResponseOverride)) {
            self::$lastArguments = $arguments;
            self::$lastFunctionName = $function_name;

            $return = self::$nextResponseOverride;
            self::$nextResponseOverride = null;
            return $return;
        }

        return parent::__soapCall($function_name, $arguments);
    }

    /**
     * Set the response that should be returned by the next SOAP request.
     * Will throw an error if a response has already been set and not used.
     *
     * @param object $response
     * @throws BadMethodCallException
     */
    public static function setNextResponse($response)
    {
        if (isset(self::$nextResponseOverride)) {
            throw new BadMethodCallException('Cannot set a next response--it\'s already been set!');
        }

        self::$nextResponseOverride = $response;
    }

    /**
     * Returns the last wsdl that was used to construct a SoapClient
     *
     * @return string
     */
    public static function getLastWsdl()
    {
        return self::$lastWsdl;
    }

    /**
     * Returns the last options that were used to construct a SoapClient
     *
     * @return string
     */
    public static function getLastOptions()
    {
        return self::$lastOptions;
    }

    /**
     * Returns the last function name that was used in a soap call
     *
     * @return string
     */
    public static function getLastFunctionName()
    {
        return self::$lastFunctionName;
    }

    /**
     * Returns the last arguments that were used in a soap call
     *
     * @return string
     */
    public static function getLastArguments()
    {
        return self::$lastArguments;
    }
}
