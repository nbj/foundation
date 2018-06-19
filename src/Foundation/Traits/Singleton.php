<?php

namespace Nbj\Foundation\Traits;

trait Singleton
{
    /**
     * Holds the singleton instance
     *
     * @var Singleton $instance
     */
    protected static $instance;

    /**
     * Static construct
     *
     * @return static
     */
    public static function create()
    {
        if (static::$instance) {
            return static::$instance;
        }

        static::$instance = new static;

        return static::$instance;
    }

    /**
     * Gets the instance of the singleton
     *
     * @return static
     */
    public static function getInstance()
    {
        if (!static::$instance) {
            return static::create();
        }

        return static::$instance;
    }

    /**
     * Constructor.
     *
     * This is protected to prevent developers from creating
     * new instances of classes that inherits this
     */
    protected function __construct()
    {
    }
}
