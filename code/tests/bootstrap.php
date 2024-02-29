<?php
/**
 * Set up test helpers here
 *
 * Putting some stuff in global scope cuz its ok... its ok... relax... its ok...
 */
declare(strict_types=1);

//laravel me up, yo
require __DIR__ . '/../vendor/autoload.php';

DG\BypassFinals::enable();

if (!function_exists('mock')) {

    /**
     * Shortcut to mock an item
     *
     * @return \Tests\CustomMockInterface
     */
    function mock()
    {
        // fix so that if you mock a method that is not on the class it will fail
        \Mockery::getConfiguration()->allowMockingNonExistentMethods(false);
        return call_user_func_array('Mockery::mock', func_get_args());
    }
}

if (!function_exists('callMethod')) {

    /**
     * Call protected or private method
     *
     * @param $object
     * @param $methodName
     * @param array $arguments
     * @return mixed
     */
    function callMethod($object, $methodName, array $arguments = [])
    {
        $class = new ReflectionClass($object);
        $method = $class->getMethod($methodName);
        $method->setAccessible(true);
        return empty($arguments) ? $method->invoke($object) : $method->invokeArgs($object, $arguments);
    }
}

if (!function_exists('getProperty')) {

    /**
     * Get protected or private property
     *
     * @param $object
     * @param $propertyName
     * @return mixed
     */
    function getProperty($object, $propertyName)
    {
        $reflection = new ReflectionClass($object);
        $property = $reflection->getProperty($propertyName);
        $property->setAccessible(true);
        return $property->getValue($object);
    }
}