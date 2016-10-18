<?php
/**
 * Extension of the Omnipay TestCase that provides better testing functionality
 * for SOAP requests.
 *
 * The Omnipay TestCase mocks the HTTP client, but you can't make PHP's SOAP
 * library use that client. This class, in conjunction with
 * Omnipay\Vindicia\TestableSoapClient, allow you to have the same testing
 * interface while still using PHP's SOAP client.
 */
namespace Omnipay\VindiciaTest;

require_once(dirname(__DIR__) . '/vendor/autoload.php');

use Omnipay\Tests\TestCase;
use Omnipay\Vindicia\TestableSoapClient;
use DOMDocument;
use Omnipay\VindiciaTest\Mocker;
use Omnipay\Common\Exception\OmnipayException;

class SoapTestCase extends TestCase
{
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
    public function setMockSoapResponse($filename, $substitutions = array())
    {
        $path = __DIR__ . '/Mock/' . $filename;
        $soapResponse = file_get_contents($path);
        if ($soapResponse === false) {
            throw new OmnipayException('Could not open file ' . $path);
        }

        foreach ($substitutions as $search => $replace) {
            $soapResponse = str_replace('[' . $search . ']', $replace, $soapResponse);
        }

        $responseDom = new DOMDocument();
        $responseDom->loadXML($soapResponse);
        $simpleXmlResponse = simplexml_import_dom($responseDom->documentElement->childNodes->item(1)->childNodes->item(1));
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

        TestableSoapClient::setNextResponse($responseObject);
    }

    public function getLastEndpoint()
    {
        return TestableSoapClient::getLastWsdl();
    }
}
