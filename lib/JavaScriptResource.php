<?php

namespace AWonderPHP\ResourceManager;

/**
 * A test implementation of the JavaScriptResource interface
 */
class JavaScriptResource extends \AWonderPHP\NotReallyPsrResourceManager\FileResource implements \AWonderPHP\NotReallyPsrResourceManager\JavaScriptResource
{
    // Inherited property from FileResource

    /**
     * The mime type is always application/javascript except when a module
     *
     * @var string
     */
    protected $mime = 'application/javascript';

    // JavaScript specific properties
    /**
     * Set to true by parseJson if needed
     *
     * @var bool
     */
    protected $async = false;

    /**
     * Set to valid string by parseJson if needed
     *
     * @var null|string
     */
    protected $crossorigin = null;

    /**
     * Set to true by parseJson if needed
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Set to true by parseJson if needed
     *
     * @var bool
     */
    protected $nomodule = false;
    
    /**
     * Set to a value by parseJson if set
     *
     * @var null|bool minified
     */
    protected $minified = null;
    
    /**
     * The directory where JavaScripts are installed. This would be the
     * vendor directory in composer installs.
     */
    protected $base;

    /**
     * Parses the $json object and sets class properties
     *
     * @param \stdClass $json The parsed JSON config file
     *
     * @return void
     */
    protected function parseJson($json)
    {
        if (! isset($json->srcurl)) {
            //FIXME exception
            $a = 'b';
        }
        if (! $url = parse_url($json->srcurl)) {
            //FIXME exception
            $a = 'b';
        }
        if (isset($url['scheme'])) {
            $scheme = strtolower($url['scheme']);
            if (! in_array($scheme, array('https', 'http'))) {
              //FIXME exception
                $a = 'b';
            }
            $this->urlscheme = $scheme;
            if (isset($url['host'])) {
                $host = $url['host'];
                if (function_exists('idn_to_ascii')) {
                    $host = idn_to_ascii($host);
                }
                if (filter_var('http://' . $host, FILTER_VALIDATE_URL)) {
                    $this->urlhost = $host;
                } else {
                  //FIXME throw exception
                    $a = 'b';
                }
            } else {
              //FIXME exception
                $a = 'b';
            }
        }
        if (isset($url['path'])) {
            $this->urlpath = $url['path'];
        }
        if (isset($url['query'])) {
            $this->urlquery = $url['query'];
        }
        if (isset($json->async)) {
            if (! is_bool($json->async)) {
                //FIXME exception
                $a = 'b';
            }
            $this->async = $json->async;
        }
        if (isset($json->nomodule)) {
            if (! is_bool($json->nomodule)) {
                //FIXME exception
            }
            $this->nomodule = $json->nomodule;
        }
        if (isset($json->minified)) {
            if (! is_bool($json->minified)) {
                //FIXME exception
            }
            $this->minified = $json->minified;
        }
        if (isset($json->crossorigin)) {
            $crossorigin = trim(strtolower($json->crossorigin));
            if (! in_array($crossorigin, array('anonymous', 'use-credentials'))) {
              //FIXME throw exception
                $a = 'b';
            }
            $this->crossorigin = $crossorigin;
        }
        if (isset($json->checksum)) {
            list($algo, $checksum) = explode(':', $json->checksum, 2);
            $algo = trim(strtolower($algo));
            $checksum = trim($checksum);
            if (ctype_xdigit($checksum)) {
                $checksum = strtolower($checksum);
            } elseif (base64_encode(base64_decode($checksum)) !== $checksum) {
              //FIXME exception
                $a = 'b';
            }
            $this->checksum = $algo . ':' . $checksum;
            if (is_null($this->crossorigin)) {
                $this->crossorigin = 'anonymous';
            }
        }
        if (isset($json->lastmod)) {
            if ($tstamp = strtotime($json->lastmod)) {
                $this->lastmod = date('c', $tstamp);
            }
        }
        if (isset($json->filepath)) {
            $filepath = $this->base . $json->filepath;
            if (file_exists($filepath)) {
                $this->filepath = $filepath;
            }
        }
    }

    /**
     * Returns the value to use with a script node src attribute
     *
     * @return string
     */
    /*
    public function getSrcAttribute(): string
    {
        if ((! is_null($this->urlscheme)) && (! is_null($this->urlhost))) {
            $string = $this->urlscheme . '://' . $this->urlhost;
        } else {
            $string = '';
        }
        if (! is_null($this->urlpath)) {
            $string .= $this->urlpath;
        }
        if (! is_null($this->urlquery)) {
            $string = $string . '?' . $this->urlquery;
        }
        return $string;
    }
    */

    /**
     * Returns the value to use with a script node src attribute
     *
     * @return string
     */

    /**
     * Returns the value to use with a script node type attribute
     *
     * @return string
     */
    public function getTypeAttribute(): string
    {
      //FIXME module
        return $this->mime;
    }

    /**
     * Returns whether or not to include a script node async attribute
     *
     * @return bool
     */
    public function getAsyncAttribute(): bool
    {
        return $this->async;
    }

    /**
     * Returns null or the value to use with a script node crossorigin attribute
     *
     * @return null|string
     */
    public function getCrossOriginAttribute()
    {
        return $this->crossorigin;
    }

    /**
     * Returns whether or not to include a script node defer attribute
     *
     * @return bool
     */
    public function getDeferAttribute(): bool
    {
        return $this->defer;
    }

    /**
     * Returns null or the value to use with a script node integrity attribute
     *
     * @return null|string
     */
    public function getIntegrityAttribute()
    {
        if (is_null($this->checksum)) {
            return null;
        }
        list($algo, $checksum) = explode(':', $this->checksum);
        if (ctype_xdigit($checksum)) {
            $checksum = hex2bin($checksum);
            $checksum = base64_encode($checksum);
        }
        return $algo . '-' . $checksum;
    }

    /**
     * Returns whether or not to include a script node nomodule attribute
     *
     * @return bool
     */
    public function getNoModuleAttribute(): bool
    {
        return $this->nomodule;
    }

    /**
     * Generates a \DOMNode script node
     *
     * @param \DOMDocument $dom   The DOMDocument object to use
     * @param null|string  $nonce A nonce for use with Content Security Policy
     *
     * @psalm-suppress RedundantConditionGivenDocblockType
     *
     * @return \DOMNode
     */
    public function generateScriptDomNode($dom, $nonce = null)
    {
        if (! $dom instanceof \DOMDocument) {
            //FIXME throw exception
            $a = 'b';
            //throw \AWonderPHP\ResourceManager\Exception\InvalidArgumentException('first argument to generateScriptDomNode must be an instance of DOMDocument');
        }
        $script = $dom->createElement('script');
        $src = $this->getSrcAttribute();
        if (! is_null($src)) {
            $script->setAttribute('src', $src);
        }
        $script->setAttribute('type', $this->getTypeAttribute());
        if ($this->async) {
            $script->setAttribute('async', 'async');
        }
        if (! is_null($this->crossorigin)) {
            $script->setAttribute('crossorigin', $this->crossorigin);
        }
        if ($this->defer) {
            $script->setAttribute('defer', 'defer');
        }
        if (! is_null($this->checksum)) {
            $attr = $this->getIntegrityAttribute();
            if (! is_null($attr)) {
                $script->setAttribute('integrity', $attr);
            }
        }
        if ($this->nomodule) {
            $script->setAttribute('nomodule', 'nomodule');
        }
        if (! is_null($nonce)) {
            if (is_string($nonce)) {
                $script->setAttribute('nonce', $nonce);
            }
            // else invalid argument exception ??
        }
        return $script;
    }

    /**
     * Generates an (X)HTML <script> string node for the script to be served.
     *
     * @param bool        $xml Set to True to generate XML style self-closing tag instead of HTML
     *                    style that can not be self-closing and must have a 0 length string
     *                    child node.
     * @param null|string $nonce A nonce to use with Content Security Policy
     *
     * @psalm-suppress RedundantConditionGivenDocblockType
     *
     * @return string
     */
    public function generateScriptString(bool $xml = false, $nonce = null)
    {
        $string  = '<script';
        $src = $this->getSrcAttribute();
        if (! is_null($src)) {
            $string = $string . ' src="' . $src . '"';
        }
        $string .= ' type ="' . $this->getTypeAttribute() . '"';
        if ($this->async) {
            if ($xml) {
                $string .= ' async="async"';
            } else {
                $string .= ' async';
            }
        }
        if (! is_null($this->crossorigin)) {
            $string .= ' crossorigin="' . $this->crossorigin . '"';
        }
        if ($this->defer) {
            if ($xml) {
                $string .= ' defer="defer"';
            } else {
                $string .= ' defer';
            }
        }
        if (! is_null($this->checksum)) {
            $attr = $this->getIntegrityAttribute();
            if (! is_null($attr)) {
                $string .= ' integrity="' . $attr . '"';
            }
        }
        if ($this->nomodule) {
            if ($xml) {
                $string .= ' nomodule="nomodule"';
            } else {
                $string .= ' nomodule';
            }
        }
        if (! is_null($nonce)) {
            if (is_string($nonce)) {
                $string .= ' nonce="' . $nonce . '"';
            }
            // else invalid argument exception?
        }
        if ($xml) {
            $string .= ' />';
        } else {
            $string .= '></script>';
        }
        return $string;
    }

    /**
     * Constructor Function
     *
     * @param string $config The path to a JSON file that defines the script to be served
     * @param string $base   The base directory for where JavaScript libraries get installed
     */
    public function __construct(string $config, string $base = '/usr/share/ccm/jscss')
    {
        if (substr($base, -1) === "/") {
            $this->base = $base;
        } else {
            $base = $base . '/';
            $this->base = $base;
        }
        if (! file_exists($config)) {
            return false;
        }
        if (! $contents = file_get_contents($config)) {
            return false;
        }
        if (! $json = json_decode($contents)) {
            return false;
        }
        $this->parseJson($json);
    }
// end of class
}


?>