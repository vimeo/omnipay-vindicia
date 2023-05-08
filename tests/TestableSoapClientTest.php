<?php

namespace Omnipay\Vindicia;

use Omnipay\Tests\TestCase;

class TestableSoapClientTest extends TestCase
{
    public function testSetNextResponseByStringDoesNotFail()
    {
        $soap_content = '<?xml version="1.0" encoding="UTF-8"?>
        <soap:Envelope
            xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
            xmlns:soapenc="http://schemas.xmlsoap.org/soap/encoding/"
            xmlns:vin="http://soap.vindicia.com/v18_0/Vindicia"
            xmlns:xsd="http://www.w3.org/2001/XMLSchema"
            soap:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"
            xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
          <soap:Body>
            <authResponse xmlns="http://soap.vindicia.com/v18_0/Transaction">
              <return xmlns="" xsi:type="vin:Return">
                <returnCode xsi:type="vin:ReturnCode">400</returnCode>
                <soapId xsi:type="xsd:string">1234567890abcdef1234567890abcdef</soapId>
                <returnString xsi:type="xsd:string">Data validation error Failed to create Payment-Type-Specific Payment Record: Credit Card conversion failed: Credit Card failed Luhn check. </returnString>
              </return>
            </authResponse>
          </soap:Body>
        </soap:Envelope>
        ';
        
        TestableSoapClient::setNextResponseByString($soap_content);
    }
}
