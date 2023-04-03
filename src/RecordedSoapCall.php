<?php

namespace Omnipay\Vindicia;

class RecordedSoapCall
{
    /**
     * @var string
     */
    private $function_name;

    /**
     * @var array
     */
    private $arguments;

    public function __construct(string $function_name, array $arguments) 
    {
        $this->function_name = $function_name;
        $this->arguments = $arguments;
    }

    public function getFunctionName(): string
    {
        return $this->function_name;
    }

    public function getArguments(): array
    {
        return $this->arguments;
    }
}
