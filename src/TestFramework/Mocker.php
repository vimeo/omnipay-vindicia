<?php

namespace Omnipay\Vindicia\TestFramework;

use Mockery;
use ReflectionClass;

class Mocker extends Mockery
{
    /**
     * Mocks and returns a HOA request (anything based off of
     * Omnipay\Vindicia\AbstractHOARequest).
     * Since those requests fake double inheritance, some extra work is
     * needed to mock them correctly.
     *
     * @param string $requestClass The full class name of the HOA request to mock
     * @return Mockery\MockInterface
     */
    public static function mockHOARequest($requestClass)
    {
        $request = self::mock($requestClass)->makePartial();
        $requestReflection = new ReflectionClass($request);
        $regularRequestProperty = $requestReflection->getProperty('regularRequest');
        $regularRequestProperty->setAccessible(true);
        // the regularRequest instance object must be mocked as well
        $regularRequest = self::mock($requestClass::$REGULAR_REQUEST_CLASS)->makePartial()->shouldAllowMockingProtectedMethods();
        $regularRequestProperty->setValue($request, $regularRequest);

        return $request;
    }

    /**
     * Redefining to tell psalm it's variadic
     *
     * @psalm-variadic
     */
    public static function mock()
    {
        return call_user_func_array('parent::mock', func_get_args());
    }
}
