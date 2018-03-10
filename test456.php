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

use \AWonderPHP\ResourceManager\ResourceManager as RMTest;

$foo = new RMTest('/usr/share/ccm/jscss');

var_dump($foo);

$jQuery1 = $foo->getJavaScript('awonderphp', 'commonjs', 'jquery', 1, "min");
var_dump($jQuery1);

$test = $jQuery1->validateFile();
if(is_null($test)) {
  echo "\nTest is null... why?\n\n";
} else {
  if($test === true) {
    echo "\nPassed Validation\n\n";
  } else {
    echo "\nFailed Validation... why?\n\n";
  }
}

$jQuery2 = $foo->getJavaScript('awonderphp', 'commonjs', 'jquery', "2.2.4");
var_dump($jQuery2);

$test = $jQuery2->validateFile();
if(is_null($test)) {
  echo "\nTest is null... why?\n\n";
} else {
  if($test === true) {
    echo "\nPassed Validation\n\n";
  } else {
    echo "\nFailed Validation... why?\n\n";
  }
}

$jQuery3 = $foo->getJavaScript('awonderphp', 'commonjs', 'jquery', 3, "slim.min");
var_dump($jQuery3);

$test = $jQuery3->validateFile();
if(is_null($test)) {
  echo "\nTest is null... why?\n\n";
} else {
  if($test === true) {
    echo "\nPassed Validation\n\n";
  } else {
    echo "\nFailed Validation... why?\n\n";
  }
}

$dom = new DOMDocument("1.0", "UTF-8");
$docstring = '<?xml version="1.0" encoding="UTF-8"?><!DOCTYPE html><html><head /><body /></html>';
$dom->loadXML($docstring);
$head = $dom->getElementsByTagName('head')->item(0);

$script = $jQuery3->generateScriptDomNode($dom);

$head->appendChild($script);

$dom->formatOutput = true;

echo "Testing DOMDocument generated script node via \$dom->saveXML()\n";
$string = $dom->saveXML();

print $string;



?>