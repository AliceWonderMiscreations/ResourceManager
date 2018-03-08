JavaScript JSON Configuration File
==================================

At the heart of this Resource Manager are JSON configuration files that tell
the web application how to reference the third party resource they need to
reference.

These configuration files are what tell the JavaScriptResource implementing
classes how to build the object for creating a link to a JavaScript resource.

Here is a current example of the concept:

    {
        "name": "jquery",
        "homepage": "https://jquery.com/",
        "version": "3.3.1",
        "license": [
            {
                "name": "MIT",
                "url": "https://jquery.org/license/"
            }
        ],
        "mime": "application/javascript",
        "checksum": "sha256:160a426ff2894252cd7cebbdd6d6b7da8fcd319c65b70468f10b6690c45d02ef",
        "filepath": "awonderphp/commonjs/js/jquery-3.3.1.min.js",
        "lastmod": "2018-01-20T17:24Z",
        "minified": true,
        "srcurl": "/js/jquery-3.3.1.min.js",
        "async": true
    }

The scripts do not have to be on the same server as the web application that
uses them, only a copy of the *configuration file does.*


Base Directory
--------------

All configuration files are to be contained within a single Base Directory. In
the case of a Composer install, that would be the `vendor` directory.

Within the `vendor` directory, there is a `VendorName/ProductName` structure
that Composer uses. The configuration files would be located in a `etc/`
directory within the ProductName. So for example, take the following github:

[AliceWonderMiscreations/CommonJS](https://github.com/AliceWonderMiscreations/CommonJS)

That project would install as `vendor/awonderphp/commonjs` so the jQuery
configuration files would be in `vendor/awonderphp/commonjs/etc`

Please note that github only exists for the purpose of testing things, I do not
expect that github to ever be listed on packagist.

When the web application needs jQuery 3 it would then ask for the object for it:

    $foo = new ResourceManager();
    $obj = $foo->getJavaScript('awonderphp', 'commonjs', 'jquery', '3', 'min;);

ResourceManager would then look for *script name* `jquery` identified as
*version 3* with the `min` variant.

That would tell the ResourceManager class to look for `jquery-3-min[.suffix]`
within the `vendor/awonderphp/commonjs/etc` directory. That naming scheme is
explained next.

Configuration File Naming Scheme
--------------------------------

Configuration files are to be named as `ScripName-Version-Variant.suffix`.

`ScriptName` is lower case name of script, e.g. `jquery` or `jquery-ui` or
whatever.

Version is the version of the script. In the standard `major.minor.point`
release scheme, the most recent version would have three different ways it
could be called. With jQuery 3.3.1 being the most recent in the jQuery 3
series at time of writing, these three would be equivalent:

* `jquery-3-min`
* `jquery-3.3-min`
* `jquery-3.3.1-min`

Variant is a variant of the script and version, if applicable. For example,
with jQuery 3.1.1 several variants exist:

* `jquery-3.3.1` (no variant)
* `jquery-3.3.1-min (minimified variant)
* `jquery-3.3.1-slim` (slim variant)
* `jquery-3.3.1-slim.min` (slim minified variant)

The suffix after the variant (or version when there is no variant) can be one
of two things:

1. `.json`
2. `.json.dist`

The idea is that when you want to customize something (e.g. put your company
CDN into the `srcurl` field) you can copy the `.json.dist` to `.json` and then
edit the `.json` version. The ResourceManager class will only use the
`.json.dist` variant when the `.json` variant does not exist.

Object Creation
---------------

The ResourceManager then uses the JSON configuration file to create an instance
of the JavaScriptResource class.

When writing the ResourceManager class, the plan actually will be to allow the
option of specifying a PSR-16 or PSR-6 cache implementation object so that the
ResourceManager can just fetch the object out of cache if it is there before
it looks for configuration file on the filesystem. The object will rarely need
to change so it is the perfect candidate for fast APCu caching.

Storing a serialized object in cache, especially one that can result in a
script node injection, is dangerous but the
[SimpleCacheAPCu](https://github.com/AliceWonderMiscreations/SimpleCacheAPCu)
implementation of PSR-16 allows for encryption of the object, the attacker
would have to get the 32-byte secret before they could pull off a successful
object injection cache poison attack. And caching the object would be optional
so the paranoid would not need to.

Object Use
----------

When the ResourceManager returns the object to the web application, there are
three ways to get a script node:

### `$obj->generateScriptDomNode($dom);`

Returns an instance of `\DOMNode` that can be appended to the document head.
Using DOMDocument is my personal preferred way of generating content.

### `$obj->generateScriptString();`

Returns an HTML style `<script></script>` string. Suitable for web applications
that echo or print content to the client.

### `$obj->generateScriptString(true);`

Returns an XML style `<script />` string. Suitable for web applications that
generate XHTML sent as XML but do it the wrong way (generating a `\DOMNode` is
better)


The JSON Specification
======================

Obviously this is subject to change, in fact I am kind of hoping the
[PHP-FIG](https://www.php-fig.org/) will adopt this concept and come up with
something that has way way more thought than I am capable of putting into it as
a single person.


System Admistrator Metadata Properties
--------------------------------------

What I love about JSON is that it is easy to human parse. Four suggested fields
in the JSON configuration file I have no actual code plans for, they are just
there for the benefit of the system administrator:

* `name`  
  The name of the script.
* `homepage`  
  The homepage of the project
* `version`  
  The version of the script.
* `license`  
  A JSON array containing licenses that apply to the script. Usually there will
  only be one applicable license, but some scripts may have more than one.

Those fields IMHO do not need to become part of the object generated by the
JavaScriptResoutce class.


Script Generation Benefit Properties
------------------------------------

These field help shape what the `<script>` node will end up looking like:

* `srcurl`  
  __REQUIRED__ String: The contents of the `src` attribute.
* `mime`  
  __REQUIRED__ String: I can not think of any cases where the MIME type would
  not be `application/javascript` but I still believe the config file should
  state it explicitly, especially since I want to do a similar thing with CSS
  and even images and media in the future (hence the name ResourceManager)
* `async`  
  *RECOMMENDED* Boolean: Whether or not the `async` attribute should be set. My
  understanding is this attribute has the same effect of putting the script
  node at the end of the `<body>` which is a practice I do not like for
  security reasons, I believe script nodes belong in the `<head>` so they can
  be forbidden in the `<body>` but I can not deny the benefit of content
  rendering above the fold before scripts have finished downloading. Anyway it
  defaults to `false` when not present, as with other boolean attributes.
* `crossorigin`  
  *Optional* String: If a `crossorigin` attribute is needed, what it should be
  set to.
* `defer`  
  *Optional* Boolean: I confess I have not researched the effects of this
  attribute, but it is boolean.
* `checksum`  
  *RECOMMENDED* String: A hex or base64 checksum in the form of `algo:checksum`
  that are then used to create the `integrity` attribute. To me this attribute
  is critical when the script ends up hosted on a third party CDN.
  

Script Wrapper Benefit Properties
---------------------------------

I would recommend that the default configuration always have a local path in
the `srcurl` attribute, and that web applications that use this standard have
a wrapper script in `/js/` to handle server requests for scripts that do not
belong to the web application itself.

That wrapper script can then use the same PHP JavaScriptResource class object
to get everything they need to serve the file. These properties would assist
in that endeavor.

* `mime`  
  __REQUIRED__ String: The MIME type to send the script with.
* `filepath`  
  __REQUIRED__ String: The path on the filesystem where the actual script
  resides. This property is not required if the script is only to be served
  from a remote server, but it should be defined in the `.json.dist` version
  of the configuration file that accompanies the script.
* `lastmod`  
  *RECOMMENDED* String: The timestamp of the script in a string that the PHP
  `strtotime()` function can parse. This is important because the modification
  timestamp on the server may not be accurate, which can cause client requests
  to see if their cached version is valid to have a false negative if load
  balancing is involved and the identical script is served from a different
  physical server but has a different timestamp on the file itself.
* `minified`  
  *RECOMMENDED* Boolean: Whether or not the script is minified. If it is set
  to false *and* the checksum is __NOT__ defined, then the script wrapper may
  want to use something like `patchwork/jsqueeze` to minify on the fly.


INCOMPLETE
==========

What is here is incomplete. For example I have not though out hoe to deal with
modules, I have simply never personally used them, but my understanding is that
the `type` attribute ends up being something different tnan
`application/javascript`

Anyway I will continue working on a reference implementation of this idea,
however I suspect it is something that is likely to only be adopted by app
developers if there is a standard created by a standards body behind it, and I
would like that to be PHP-FIG.

  







