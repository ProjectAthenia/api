<?php
declare(strict_types=1);

namespace Tests\Traits;

/**
 * Trait ReflectionHelpers
 * @package Tests\Traits
 */
trait ReflectionHelpers
{
    /**
     * @var string The same directory where the composer.json exists
     */
    static $appRoot = __DIR__ . "/../../";

    /**
     * Takes a namespace, and loads all php loadable object in a namespace
     *
     * @param $namespace
     * @return array
     */
    public function getObjectsInNamespace($namespace)
    {
        try {
            $files = scandir($this->getNamespaceDirectory($namespace));
        } catch (\ErrorException $e) {
            return [];
        }
        $classes = [];

        foreach ($files as $file) {
            if ($file != '.' && $file != '..') {

                $fullName = $namespace . '\\' . $file;

                if (is_dir($fullName)) {
                    $classes = array_merge($classes, $this->getObjectsInNamespace($fullName));
                } else if (strpos($fullName, '.php')) {
                    $classes[] = str_replace('.php', '', $fullName);
                }
            }
        }

        return $classes;
    }

    /**
     * Checks the composer json for defined namespaces
     *
     * @return array
     */
    private function getDefinedNamespaces()
    {
        $composerJsonPath = self::$appRoot . 'composer.json';
        $composerConfig = json_decode(file_get_contents($composerJsonPath));

        //Apparently PHP doesn't like hyphens, so we use variable variables instead.
        $psr4 = "psr-4";
        return (array) $composerConfig->autoload->$psr4;
    }

    /**
     * Gets the namespace directory of a passed in namespace or returns false if it is not found
     *
     * @param $namespace
     * @return bool|string
     */
    private function getNamespaceDirectory($namespace)
    {
        $composerNamespaces = $this->getDefinedNamespaces();

        $namespaceFragments = explode('\\', $namespace);
        $undefinedNamespaceFragments = [];

        while($namespaceFragments) {
            $possibleNamespace = implode('\\', $namespaceFragments) . '\\';

            if(array_key_exists($possibleNamespace, $composerNamespaces)){
                return realpath(self::$appRoot . $composerNamespaces[$possibleNamespace] . implode('/', array_reverse($undefinedNamespaceFragments)));
            }

            $undefinedNamespaceFragments[] = array_pop($namespaceFragments);
        }

        return false;
    }
}