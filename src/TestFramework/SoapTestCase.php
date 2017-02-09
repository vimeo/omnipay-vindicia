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
namespace Omnipay\Vindicia\TestFramework;

use Omnipay\Tests\TestCase;
use Omnipay\Vindicia\TestableSoapClient;

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
     * @param  string $filename
     * @param  array<string, mixed> $substitutions default array()
     * @throws Omnipay\Common\Exception\BadMethodCallException
     * @return void
     */
    public function setMockSoapResponse($filename, $substitutions = array())
    {
        TestableSoapClient::setNextResponseFromFile($filename, $substitutions);
    }

    /**
     * @return null|string
     */
    public function getLastEndpoint()
    {
        return TestableSoapClient::getLastWsdl();
    }
}
