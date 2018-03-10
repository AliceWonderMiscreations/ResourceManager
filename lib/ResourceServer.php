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
     * Serves a file specified in a FileResource object.
     *
     * @param \AWonderPHP\NotReallyPsrResourceManager\FileResource $fileResource The FileResource object
     *                                                                           for what we want to serve.
     * @param bool                                                 $minify       Should we attempt to minify?
     *
     * @return bool True on success, False on failure
     */
    public function serveFileResource($fileResource, bool $minify = false)
    {
        $filepath = $fileResource->getFilepath();
        if (is_null($filepath)) {
          //FIXME throw exception
            $a = 'b';
        }
        if (! file_exists($filepath)) {
          //FIXME throw exception
            $a = 'b';
        }
        $mime = $fileResource->showMime();
      //wrap in has property?
        $minified = $fileResource->getMinified();
        if (is_null($minified)) {
            $minify = false;
        } else {
            if ($minified) {
                $minify = false;
            }
        }
        $chk = $fileResource->showChecksum();
        if (! is_null($chk)) {
            $minify = false;
        }
        $ts = $fileResource->getTimestamp();
        $origin = null;
        $crossorigin = $fileResource->getCrossOriginAttribute();
        if (! is_null($crossorigin)) {
            $origin = '*';
        }
      
        $wrapper = new \AWonderPHP\ResourceManager\FileWrapper($filepath, $mime, $ts, $origin, $this->maxage, $minify);
        return $wrapper->sendfile();
    }
}

?>