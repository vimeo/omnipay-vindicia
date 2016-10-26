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
 * 4. If you used setNextResponse, you can call
 * getLastOptions/FunctionName/Arguments/Wsdl to see what components made
 * up the request
 */
namespace Omnipay\Vindicia;

use Omnipay\Common\Exception\BadMethodCallException;
use SoapClient;
use SplQueue;
use DOMDocument;
use Omnipay\Common\Exception\OmnipayException;

class TestableSoapClient extends SoapClient
{
    /**
     * @var \SplQueue
     */
    protected static $nextResponseOverrideQueue;

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
        if (!isset(self::$nextResponseOverrideQueue)) {
            self::$nextResponseOverrideQueue = new SplQueue();
        }

        if (self::$nextResponseOverrideQueue->isEmpty()) {
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
        if (!self::$nextResponseOverrideQueue->isEmpty()) {
            self::$lastArguments = $arguments;
            self::$lastFunctionName = $function_name;

            $return = self::$nextResponseOverrideQueue->dequeue();
            return $return;
        }

        return parent::__soapCall($function_name, $arguments);
    }

    /**
     * Set the response that should be returned by the next SOAP request.
     * If multiple responses are set before a SOAP call is made, they are
     * stored on a queue and will be used in the order they were inserted.
     *
     * @param object $response
     */
    public static function setNextResponse($response)
    {
        if (!isset(self::$nextResponseOverrideQueue)) {
            self::$nextResponseOverrideQueue = new SplQueue();
        }

        self::$nextResponseOverrideQueue->enqueue($response);
    }

    /**
     * Set the response that should be returned by the next SOAP request.
     * `$filename` is the name of the XML file in the tests/Mock directory
     * that contains the response.
     * $substitutions is an array of strings to strings that specifies
     * substitutions to be made to the contents of the XML file. For
     * example, if `$substitutions` is `array('NAME' => 'Fake Name')`,
     * then the function will replace all instances of `'[NAME]'` in the
     * XML with `'Fake Name'`. This is useful for randomizing tests, but
     * is not required.
     * Will throw an error if a response has already been set and not used
     * or if the file can't be opened.
     *
     * @param string $filename
     * @param array<string, string> $substitutions default array()
     * @throws Omnipay\Common\Exception\BadMethodCallException
     */
    public static function setNextResponseFromFile($filename, $substitutions = array())
    {
        $path = dirname(__DIR__) . '/tests/Mock/' . $filename;
        $soapResponse = file_get_contents($path);
        if ($soapResponse === false) {
            throw new OmnipayException('Could not open file ' . $path);
        }

        foreach ($substitutions as $search => $replace) {
            $soapResponse = str_replace('[' . $search . ']', $replace, $soapResponse);
        }

        $responseDom = new DOMDocument();
        $responseDom->loadXML($soapResponse);
        $simpleXmlResponse = simplexml_import_dom(
            $responseDom->documentElement->childNodes->item(1)->childNodes->item(1)
        );
        // convert SimpleXMLElement to normal object
        $responseObject = json_decode(json_encode($simpleXmlResponse));

        // When SOAP fields are supposed to be arrays but they only have one element in them,
        // the above method of parsing the XML just returns an object instead of a one element
        // array. The following code is a hack to restore the one element array structure for
        // the affected fields.
        $fields = array();
        if (isset($responseObject->results)) {
            $fields[] = &$responseObject->results;
        }
        if (isset($responseObject->account->paymentMethods)) {
            $fields[] = &$responseObject->account->paymentMethods;
        }
        if (isset($responseObject->refunds)) {
            $fields[] = &$responseObject->refunds;
        }

        // also affects the payment methods on the accounts on the transactions in the refunds, but
        // then we have to hack through all the refunds and we don't need that field anyway

        foreach ($fields as &$field) {
            if (is_object($field)) {
                $field = array($field);
            }
        }

        self::setNextResponse($responseObject);
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
