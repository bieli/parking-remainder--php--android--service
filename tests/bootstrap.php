<?php

// Unit Test Bootstrap and Slim PHP Testing Framework
//
// Run with command:
// tests/$ phpunit --bootstrap bootstrap.php integration/GetPositionTest.php
//

define('SLIM_TEST_ENV', true);

error_reporting(-1);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
date_default_timezone_set('UTC');

set_include_path(
    get_include_path() .
    PATH_SEPARATOR . dirname(__DIR__) .
    PATH_SEPARATOR . dirname(__DIR__) . '/../src'
);

require_once __DIR__ . '/../vendor/autoload.php';

class Slim_Framework_TestCase extends PHPUnit_Framework_TestCase
{
    // We support these methods for testing. These are available via
    // `this->get()` and `$this->post()`. This is accomplished with the
    // `__call()` magic method below.
    private $testingMethods = array('get', 'post', 'patch', 'put', 'delete', 'head');

    // Run for each unit test to setup our slim app environment
    public function setUp()
    {
        // Initialize our own copy of the slim application
        $app = new \Slim\Slim(array(
            'version'        => '0.0.0',
            'debug'          => false,
            'mode'           => 'testing'
        ));

        // Include our core application file
        require __DIR__ . '/../src/index.php';

        // Establish a local reference to the Slim app object
        $this->app = $app;
    }

    // Abstract way to make a request to SlimPHP, this allows us to mock the
    // slim environment
    private function request($method, $path, $formVars = array(), $optionalHeaders = array())
    {
        // Capture STDOUT
        ob_start();

        // Prepare a mock environment
        \Slim\Environment::mock(array_merge(array(
            'REQUEST_METHOD' => strtoupper($method),
            'PATH_INFO'      => $path,
            'SERVER_NAME'    => 'local.dev',
            'slim.input'     => http_build_query($formVars)
        ), $optionalHeaders));

        // Establish some useful references to the slim app properties
        $this->request  = $this->app->request();
        $this->response = $this->app->response();

        // Execute our app
        $this->app->run();

        // Return the application output. Also available in `response->body()`
        return ob_get_clean();
    }

    // Implement our `get`, `post`, and other http operations
    public function __call($method, $arguments) {
        if (in_array($method, $this->testingMethods)) {
            list($path, $formVars, $headers) = array_pad($arguments, 3, array());
            return $this->request($method, $path, $formVars, $headers);
        }
        throw new \BadMethodCallException(strtoupper($method) . ' is not supported');
    }
}

