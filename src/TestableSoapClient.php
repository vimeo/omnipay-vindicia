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

    /**
     * @var string|null
     */
    protected static $lastWsdl = null;

    /**
     * @var array<mixed>
     */
    protected static $lastOptions = [];

    /**
     * @var string|null
     */
    protected static $lastFunctionName = null;

    /**
     * @var array<mixed>
     */
    protected static $lastArguments = [];

    public function __construct($wsdl, array $options = array())
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
     * @param  object $response
     * @return void
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
     * @param  string $filename
     * @param  array<string, string> $substitutions default array()
     * @throws Omnipay\Common\Exception\BadMethodCallException
     * @return void
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
        if (isset($responseObject->transaction->items)) {
            $fields[] = &$responseObject->transaction->items;
        }
        if (isset($responseObject->billingPlan->periods)) {
            $fields[] = &$responseObject->billingPlan->periods;
        }
        if (isset($responseObject->billingPlan->nameValues)) {
            $fields[] = &$responseObject->billingPlan->nameValues;
        }
        if (isset($responseObject->product->prices)) {
            $fields[] = &$responseObject->product->prices;
        }
        if (isset($responseObject->product->nameValues)) {
            $fields[] = &$responseObject->product->nameValues;
        }
        if (isset($responseObject->product->defaultBillingPlan->periods)) {
            $fields[] = &$responseObject->product->defaultBillingPlan->periods;
        }
        if (isset($responseObject->product->defaultBillingPlan->nameValues)) {
            $fields[] = &$responseObject->product->defaultBillingPlan->nameValues;
        }
        if (isset($responseObject->autobill->billingPlan->periods)) {
            $fields[] = &$responseObject->autobill->billingPlan->periods;
        }
        if (isset($responseObject->autobill->items->product->defaultBillingPlan->periods)) {
            $fields[] = &$responseObject->autobill->items->product->defaultBillingPlan->periods;
        }
        if (isset($responseObject->autobills->billingPlan->periods)) {
            $fields[] = &$responseObject->autobills->billingPlan->periods;
        }
        if (isset($responseObject->autobills[0]->billingPlan->periods)) {
            $fields[] = &$responseObject->autobills[0]->billingPlan->periods;
        }
        if (isset($responseObject->autobills[1]->billingPlan->periods)) {
            $fields[] = &$responseObject->autobills[1]->billingPlan->periods;
        }
        if (isset($responseObject->autobill->items)) {
            $fields[] = &$responseObject->autobill->items;
        }
        if (isset($responseObject->autobills->items)) {
            $fields[] = &$responseObject->autobills->items;
        }
        if (isset($responseObject->autobills[0]->items)) {
            $fields[] = &$responseObject->autobills[0]->items;
        }
        if (isset($responseObject->autobills[1]->items)) {
            $fields[] = &$responseObject->autobills[1]->items;
        }
        if (isset($responseObject->autobills)) {
            $fields[] = &$responseObject->autobills;
        }
        if (isset($responseObject->autobill->nameValues)) {
            $fields[] = &$responseObject->autobill->nameValues;
        }
        if (isset($responseObject->session->apiReturnValues->accountUpdatePaymentMethod->account->paymentMethods)) {
            $fields[] = &$responseObject->session->apiReturnValues->accountUpdatePaymentMethod->account->paymentMethods;
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
     * @return null|string
     */
    public static function getLastWsdl()
    {
        return self::$lastWsdl;
    }

    /**
     * Returns the last options that were used to construct a SoapClient
     *
     * @return array<mixed>
     */
    public static function getLastOptions()
    {
        return self::$lastOptions;
    }

    /**
     * Returns the last function name that was used in a soap call
     *
     * @return null|string
     */
    public static function getLastFunctionName()
    {
        return self::$lastFunctionName;
    }

    /**
     * Returns the last arguments that were used in a soap call
     *
     * @return array<mixed>
     */
    public static function getLastArguments()
    {
        return self::$lastArguments;
    }
}
