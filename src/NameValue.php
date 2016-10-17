<?php

namespace Omnipay\Vindicia;

use InvalidArgumentException;

/**
 * Class to represent Vindicia's NameValue objects, which are used to build
 * many requests.
 */
class NameValue
{
    public $name;
    public $value;

    /**
     * Constructs NameValue. Values can be string, ints, bools, or null and will be converted
     * to strings, as is required for Vindicia's API.
     *
     * @param string $name
     * @param string|int|bool|null $value
     */
    public function __construct($name, $value)
    {
        if (!is_string($name)) {
            throw new InvalidArgumentException('Parameter name must be type string, not ' . gettype($name));
        }
        $this->name = $name;

        if ($value === null) {
            $this->value = 'null';
        } elseif (is_int($value)) {
            $this->value = strval($value);
        } elseif (is_bool($value)) {
            $this->value = $value ? 'true' : 'false';
        } elseif (is_string($value)) {
            $this->value = $value;
        } else {
            throw new InvalidArgumentException('Invalid type ' . gettype($value) . ' for parameter value');
        }
    }
}
