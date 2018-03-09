<?php
header("Content-Type: text/plain");
// This is not part of this project, it is how I autoload stuff
// as I do not personally trust composer for deployment scenarios.
// it's hear to make testing easier for me.

$branchpath = 'custom:stable';

require('/usr/share/ccm/ClassLoader.php');
$CCM = new \CCM\ClassLoader();
$CCM->changeDefaultSearchPath($branchpath);

spl_autoload_register(function ($class) {
  global $CCM;
  $CCM->loadClass($class);
});

//spl_autoload_register(function ($class) {
//  global $CCM;
//  $CCM->localSystemClass($class);
//});

// this is testing what I have, also not part of the project, just finding the bugs...

use \AWonderPHP\ResourceManager\JavaScriptResource as JSTest;

$config = '/usr/share/ccm/jscss/awonderphp/commonjs/etc/jquery-3-min.json.dist';

$foo = new JSTest($config);

$rand = random_bytes(8);
$nonce = bin2hex($rand);

var_dump($foo);

echo "\n\n";

echo "The hash in the json is hex, this is what it should come to in PHP base64 conversion:\n\n----\n[alice@localhost js]$ cat jquery-3.3.1.min.js |libressl dgst -sha256 -binary |libressl enc -base64 -A\nFgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=\n----\n\n";

echo "Testing class html output:\n";
$string = $foo->generateScriptString();

echo $string;

echo "\n\n";

echo "Testing class html output with a nonce:\n";
$string = $foo->generateScriptString(false, $nonce);

echo $string;

echo "\n\n";

echo "Testing class xhtml output:\n";
$string = $foo->generateScriptString(true);

echo $string;

echo "\n\n";

echo "Testing class xhtml output with a nonce:\n";
$string = $foo->generateScriptString(true, $nonce);

echo $string;

echo "\n\n";

$dom = new DOMDocument("1.0", "UTF-8");
$docstring = '<?xml version="1.0" encoding="UTF-8"?><!DOCTYPE html><html><head /><body /></html>';
$dom->loadXML($docstring);
$head = $dom->getElementsByTagName('head')->item(0);

$script = $foo->generateScriptDomNode($dom);

$head->appendChild($script);

$dom->formatOutput = true;

echo "Testing DOMDocument generated script node via \$dom->saveXML()\n";
$string = $dom->saveXML();

print $string;

echo "\n\n";

echo "Testing DOMDocument generated script node via \$dom->saveHTML()\n";
$string = $dom->saveHTML();

print $string;





?>