<?php

namespace Nbj\Foundation;

use Countable;
use ArrayAccess;
use Traversable;
use ArrayIterator;
use IteratorAggregate;

class ParameterSet implements Countable, ArrayAccess, IteratorAggregate
{
    /**
     * The key/value store
     *
     * @var array $parameters
     */
    protected $parameters;

    /**
     * Static construct
     *
     * @param array $parameters
     *
     * @return static
     */
    public static function create(array $parameters)
    {
        return new static($parameters);
    }

    /**
     * Syntactic sugar for creating a empty set
     *
     * @return static
     */
    public static function createEmpty()
    {
        return new static([]);
    }

    /**
     * ParameterSet constructor.
     *
     * @param array $parameters
     */
    public function __construct(array $parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * Gets the value for a specific key
     *
     * @param string $key
     *
     * @return mixed|null
     */
    public function get($key)
    {
        if (!$this->has($key)) {
            return null;
        }

        return $this->parameters[$key];
    }

    /**
     * Checks if a specific key exists in the parameter set
     *
     * @param string $key
     *
     * @return bool
     */
    public function has($key)
    {
        return array_key_exists($key, $this->parameters);
    }

    /**
     * Gets all the keys in the parameter set
     *
     * @return array
     */
    public function keys()
    {
        return array_keys($this->parameters);
    }

    /**
     * Retrieve an external iterator
     *
     * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
     *
     * @return Traversable An instance of an object implementing <b>Iterator</b> or
     * <b>Traversable</b>
     *
     * @since 5.0.0
     */
    public function getIterator()
    {
        return new ArrayIterator($this->parameters);
    }

    /**
     * Whether a offset exists
     *
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     *
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     *
     * @return bool true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     *
     * @since 5.0.0
     */
    public function offsetExists($offset)
    {
        return isset($this->parameters[$offset]);
    }

    /**
     * Offset to retrieve
     *
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     *
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     *
     * @return mixed Can return all value types.
     *
     * @since 5.0.0
     */
    public function offsetGet($offset)
    {
        return $this->parameters[$offset];
    }

    /**
     * Offset to set
     *
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     *
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     *
     * @return void
     *
     * @since 5.0.0
     */
    public function offsetSet($offset, $value)
    {
        $this->parameters[$offset] = $value;
    }

    /**
     * Offset to unset
     *
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     *
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     *
     * @return void
     *
     * @since 5.0.0
     */
    public function offsetUnset($offset)
    {
        unset($this->parameters[$offset]);
    }

    /**
     * Count elements of an object
     *
     * @link http://php.net/manual/en/countable.count.php
     *
     * @return int The custom count as an integer.
     * </p>
     * <p>
     * The return value is cast to an integer.
     *
     * @since 5.1.0
     */
    public function count()
    {
        return count($this->parameters);
    }
}
