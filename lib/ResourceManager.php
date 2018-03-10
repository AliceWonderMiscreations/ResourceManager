<?php

namespace AWonderPHP\ResourceManager;

/**
 * A proof of concept implementation of the ResourceManager interface.
 *
 * This implementation only allows for one base path but the interface does not
 * specify only one.
 *
 * This implementation optionally utilizes PSR-16 cache but the interface does
 * not specify caching.
 */
class ResourceManager implements \AWonderPHP\NotReallyPsrResourceManager\ResourceManager
{
    /**
     * The base directory where JS/CSS are installed
     *
     * @var string
     */
    protected $base = '/usr/share/ccm/jscss/';
    
    /**
     * Null or a PSR-16 cache implementation
     *
     * @var null|\Psr\SimpleCache\CacheInterface
     */
    protected $cacheObj = null;
    
    /**
     * The TTL to store the cached object for. Set by not-yet written public function
     *
     * @var int
     */
    protected $ttl = 259200;
    
    /**
     * Fetches the resource from the cache, or false on failure
     *
     * @param string $key The key the object would be cached with
     *
     * @return bool|\AWonderPHP\NotReallyPsrResourceManager\FileResource
     */
    protected function getCachedResource(string $key)
    {
        if (is_null($this->cacheObj)) {
            return false;
        }
        $obj = $this->cacheObj->get($key);
        if ($obj instanceof \AWonderPHP\NotReallyPsrResourceManager\FileResource) {
            return $obj;
        }
        return false;
    }
    
    /**
     * Caches the resource
     *
     * @param string                                          $key The key the object is to be cached with
     * @param \AWonderPHP\NotReallyPsrResourceManager\FileResource $obj The object to cache
     *
     * @return void
     */
    protected function setCachedResource(string $key, $obj)
    {
        if (is_null($this->cacheObj)) {
            return;
        }
        $this->cacheObj->set($key, $obj, $this->ttl);
    }
    
    /**
     * Attempts to find the JSON configuration file for the specified JavaScript and when
     * found, returns a JavaScriptResource object
     *
     * @param string      $vendor  The top level vendor of the script, lower case
     * @param string      $product The product name the script is part of, lower case
     * @param string      $name    The basic name of the script (e.g. jquery), lower case
     * @param int|string  $version The version of the script requested. If the argument is
     *                             an integer, it should be recast as a string.
     * @param null|string $variant The variant of the script requested
     *
     * @return null|\AWonderPHP\NotReallyPsrResourceManager\FileResource
     */
    public function getJavaScript(string $vendor, string $product, string $name, $version, $variant = null)
    {
        if (is_int($version)) {
            // this is strictly academic but it makes type checking easier
            $version = (string)$version;
        }
        $baseConf = $name . '-' . $version;
        if (! is_null($variant)) {
            $baseConf = $baseConf . '-' . $variant;
        }
        $key = $vendor . '-' . $product . '-' . $baseConf;
        $obj = $this->getCachedResource($key);
        if ($obj instanceof \AWonderPHP\NotReallyPsrResourceManager\FileResource) {
            return $obj;
        }
        //okay see if the file exists
        $filepath = $this->base . $vendor . '/' . $product . '/etc/' . $baseConf . '.json';
        if (file_exists($filepath)) {
            $conf = $filepath;
        } else {
            $filepath .= '.dist';
            if (file_exists($filepath)) {
                $conf = $filepath;
            }
        }
        if (! isset($conf)) {
            //oops, it's not found
            return null;
        }
        if (! $obj = new \AWonderPHP\ResourceManager\JavaScriptResource($conf, $this->base)) {
            return null;
        }
        $this->setCachedResource($key, $obj);
        return $obj;
    }
    
    /**
     * Attempts to find the JSON configuration file for the specified CSS and when
     * found, returns a CssResource object
     *
     * @param string      $vendor  The top level vendor of the css, lower case
     * @param string      $product The product name the css is part of, lower case
     * @param string      $name    The basic name of the css (e.g. normalize), lower case
     * @param int|string  $version The version of the css requested. If the argument is
     *                             an integer, it should be recast as a string.
     * @param null|string $variant The variant of the css requested
     *
     * @return null|\AWonderPHP\NotReallyPsrResourceManager\FileResource
     */
    public function getCss(string $vendor, string $product, string $name, $version, $variant = null)
    {
        return null;
    }
    
    /**
     * The constructor
     *
     * @param string                               $base The base path where static JS/CSS are installed
     * @param null|\Psr\SimpleCache\CacheInterface $cacheObj A PSR-16 cache interface
     */
    public function __construct(string $base, $cacheObj = null)
    {
        if (substr($base, -1) === "/") {
            $this->base = $base;
        } else {
            $base = $base . '/';
            $this->base = $base;
        }
        if ($cacheObj instanceof \Psr\SimpleCache\CacheInterface) {
            $this->cacheObj = $cacheObj;
        }
    }
}


?>