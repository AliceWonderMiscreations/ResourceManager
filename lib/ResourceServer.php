<?php

namespace AWonderPHP\ResourceManager;

/**
 * A proof of concept implementation of the ResourceServer interface.
 */

// okay this is broken but soon it won't be
class ResourceServer implements \AWonderPHP\NotReallyPsrResourceManager\ResourceServer
{
    /**
     * How long the client should cache the file for. Set by constructor.
     *
     * @var int
     */
    protected $maxage = 604800;
    
    /**
     * The base directory where JS/CSS are installed
     *
     * @var string
     */
    protected $base = '/usr/share/ccm/jscss/';

    
    /**
     * Serves a file specified in a FileResource object.
     *
     * @param \AWonderPHP\NotReallyPsrResourceManager\FileResource $fileResource The FileResource object
     *                                                                           for what we want to serve.
     * @param bool                                                 $minify       Should we attempt to minify?
     *
     * @return bool True on success, False on failure
     */
    public function serveFileResource($fileResource, bool $minify = false): bool
    {
        $filepath = $fileResource->getFilepath();
        if (is_null($filepath)) {
            // FIXME throw exception
            return false;
        }
        if (! file_exists($filepath)) {
            // FIXME throw exception
            return false;
        }
        $mime = $fileResource->getMimeType();

        if (method_exists($fileResource, 'getMinified')) {
            $minified = $fileResource->getMinified();
            if (is_null($minified)) {
                $minify = false;
            } else {
                if ($minified) {
                    $minify = false;
                }
            }
        } else {
            $minify = false;
        }
        $chk = $fileResource->getChecksum();
        if (! is_null($chk)) {
            $minify = false;
        }
        $ts = $fileResource->getTimestamp();
        $origin = null;
        $crossorigin = $fileResource->getCrossOrigin();
        if (! is_null($crossorigin)) {
            $origin = '*';
        }
      
        $wrapper = new \AWonderPHP\ResourceManager\FileWrapper($filepath, $mime, $ts, $origin, $this->maxage, $minify);
        return $wrapper->sendfile();
    }
    
    /**
     * Fetches the JavaScriptResource object described by the parameters and then serves the file.
     *
     * @param string      $vendor  The top level vendor of the script, lower case
     * @param string      $product The product name the script is part of, lower case
     * @param string      $name    The basic name of the script (e.g. jquery), lower case
     * @param int|string  $version The version of the script requested. If the argument is
     *                             an integer, it should be recast as a string.
     * @param null|string $variant The variant of the script requested
     * @param boolean     $minify  Whether or not an attempt to minify non-minified scripts should be done
     *
     * @return bool True on success, False on failure
     */
    public function serveJavaScript(string $vendor, string $product, string $name, $version, $variant = null, bool $minify = false): bool
    {
        $RM = new ResourceManager($this->base);
        $obj = $RM->getJavaScript($vendor, $product, $name, $version, $variant);
        if (! $obj instanceof \AWonderPHP\NotReallyPsrResourceManager\FileResource) {
            return false;
        }
        return $this->serveFileResource($obj, $minify);
    }
    
    /**
     * Fetches the CssResource object described by the parameters and then serves the file.
     *
     * @param string      $vendor  The top level vendor of the css, lower case
     * @param string      $product The product name the css is part of, lower case
     * @param string      $name    The basic name of the css (e.g. normalize), lower case
     * @param int|string  $version The version of the css requested. If the argument is
     *                             an integer, it should be recast as a string.
     * @param null|string $variant The variant of the css requested
     * @param boolean     $minify  Whether or not an attempt to minify non-minified css should be done
     *
     * @return bool True on success, False on failure
     */
    public function serveCss(string $vendor, string $product, string $name, $version, $variant = null, bool $minify = false)
    {
        return false;
    }
    
    /**
     * The constructor function
     *
     * @param string $base   The base path where static JS/CSS are installed
     * @param int    $maxage How many seconds the requesting client should cache the file for
     *
     * @return void
     */
    public function __construct(string $base, int $maxage = 604800)
    {
        $this->base = trim($base);
        $this->maxage = $maxage;
    }
}

?>