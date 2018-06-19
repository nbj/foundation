<?php

namespace Nbj\Http;

use Nbj\Foundation\Support\ParameterSet;

class Request
{
    /**
     * Holds all parameters from $_GET
     *
     * @var ParameterSet $getParameters
     */
    protected $getParameters;

    /**
     * Holds all parameters from $_POST
     *
     * @var ParameterSet $postParameters
     */
    protected $postParameters;

    /**
     * Holds all parameters from $_SERVER
     *
     * @var ParameterSet $serverParameters
     */
    protected $serverParameters;

    /**
     * Holds all files
     *
     * @var ParameterSet $files
     */
    protected $files;

    /**
     * Captures the incoming request (Static construct)
     *
     * @return static
     */
    public static function capture()
    {
        return new static;
    }

    /**
     * Request constructor.
     */
    public function __construct()
    {
        $this->getParameters = ParameterSet::create($_GET);
        $this->postParameters = ParameterSet::create($_POST);
        $this->serverParameters = ParameterSet::create($_SERVER);
        $this->files = ParameterSet::create($_FILES);
    }

    /**
     * Gets the uri of the request
     *
     * @return string
     */
    public function getUri()
    {
        return parse_url($this->serverParameters->get('REQUEST_URI'), PHP_URL_PATH);
    }

    /**
     * Gets the method of the request
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->serverParameters->get('REQUEST_METHOD');
    }

    /**
     * Checks if the request is a post request
     *
     * @return bool
     */
    public function isPost()
    {
        return $this->getMethod() == 'POST';
    }

    /**
     * Gets the value of a specific parameter
     *
     * @param $parameter
     *
     * @return mixed|null
     */
    public function get($parameter)
    {
        if ($this->getParameters->has($parameter)) {
            return $this->getParameters->get($parameter);
        }

        if ($this->postParameters->has($parameter)) {
            return $this->postParameters->get($parameter);
        }

        return null;
    }

    /**
     * Checks if the request has a specific parameter
     *
     * @param string $parameter
     *
     * @return bool
     */
    public function has($parameter)
    {
        if ($this->getParameters->has($parameter)) {
            return true;
        }

        if ($this->postParameters->has($parameter)) {
            return true;
        }

        return false;
    }
}
