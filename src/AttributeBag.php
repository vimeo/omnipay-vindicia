<?php

namespace Omnipay\Vindicia;

/**
 * Attribute Bag
 *
 * This class defines a bag (multi element set or array) of attributes for a request.
 *
 * @see Attribute
 */
class AttributeBag implements \IteratorAggregate, \Countable
{
    /**
     * Attribute storage
     *
     * @var array
     */
    protected $attributes;

    /**
     * Constructor
     *
     * @param array $attributes An array of attributes
     */
    public function __construct(array $attributes = array())
    {
        $this->replace($attributes);
    }

    /**
     * Return all the attributes
     *
     * @return array An array of attributes
     */
    public function all()
    {
        return $this->attributes;
    }

    /**
     * Replace the contents of this bag with the specified attributes.
     * $attributes can be an array of Attributes, an array of
     * `['name' => XXX, 'value' => XXX]` pairs, or an array of values
     * indexed by name.
     *
     * @param  array $attributes An array of attributes
     * @return void
     */
    public function replace(array $attributes = array())
    {
        $this->attributes = array();

        foreach ($attributes as $key => $value) {
            if (is_array($value) || $value instanceof Attribute) {
                $this->add($value);
            } else {
                $this->add(array(
                    'name' => $key,
                    'value' => $value
                ));
            }
        }
    }

    /**
     * Add an attribute to the bag
     * Can add an Attribute object or an associative array of attribute
     * parameters (`['name' => XXX, 'value' => XXX]`)
     *
     * @param  Attribute|array $attribute
     * @return void
     */
    public function add($attribute)
    {
        if ($attribute instanceof Attribute) {
            $this->attributes[] = $attribute;
        } else {
            $this->attributes[] = new Attribute($attribute);
        }
    }

    /**
     * Returns an iterator for attributes
     *
     * @return \ArrayIterator An \ArrayIterator instance
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->attributes);
    }

    /**
     * Returns the number of attributes
     *
     * @return int The number of attributes
     */
    public function count()
    {
        return count($this->attributes);
    }
}
