<?php

namespace AWonderPHP\ResourceManager\abstract;

abstract class FileResource
{
    /**
     * The MIME type of the resource
     *
     * @var null|string
     */
    protected $mime = null;
  
    /**
     * Algorithm : checksum
     *
     * The checksum is either hex or base64 encoded. Example:
     * sha256:708c26ff77c1fa15ac9409a5cbe946fe50ce203a73c9b300960f2adb79e48c04
     *
     * @var null|string
     */
    protected $checksumAlgo = null;
  
    /**
     * Filesystem location, only applicable when a local resource
     *
     * @var null|string
     */
    protected $filepath = null;
  
    /**
     * Modification date of file - may not necessarily match the modification date of the actual
     * file as seen by the filesystem. ISO 8601 in 'Y-m-d\TH:i:sO' - aka date('c')
     *
     * @var null|string
     */
    protected $lastmod = null;
  
    // subset from parse_url
  
    protected $urlscheme = null;
    protected $urlhost = null;
    protected $urlpath = null;
    protected $urlquery = null;
  
  
  
    /**
     * Return the mime type
     *
     * @return null|string
     */
    public function showMime()
    {
        return $this->mime;
    }
  
    /**
     * Return the checksum
     *
     * @return null|string
     */
    public function showChecksum()
    {
        return $this->checksum;
    }
  
    /**
     * Returns the URI to the resource
     *
     * @return null|string
     */
    public function resourceURI()
    {
        $return = '';
        if (! is_null($this->urlscheme) {
            $return = $this->urlscheme . '://';
        }
        if (! is_null($this->urlhost) {
            $return .= $this->urlhost;
        }
        if (! is_null($this>urlpath) {
            $return .= $this->urlpath;
        }
        if (! is_null($this->urlquery) {
            $return .= '?' $this->urlquery;
        }
        if (strlen($return) === 0) {
            return null;
        }
        return $return;
    }

    /**
     * Returns the UNIX timestamp from the lastmod property
     *
     * @return null|int
     */
    public function getTimestamp()
    {
        if (is_null($this->lastmod) {
            return null;
        }
        if ($ts = strtotime($this->lastmod)) {
            return $ts;
        }
        return null;
    }
}

?>