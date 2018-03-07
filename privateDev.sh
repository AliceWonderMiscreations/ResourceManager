#!/bin/bash

#This will be deleted before release

cd lib

cp Abstract/*.php /usr/share/ccm/custom/libraries/awonderphp/resourcemanager/Abstract/
[ -d Interface ] && cp Interface/* /usr/share/ccm/custom/libraries/awonderphp/resourcemanager/Interface/
[ -d Exception ] && cp Exception/* /usr/share/ccm/custom/libraries/awonderphp/resourcemanager/Exception/

cp *.php /usr/share/ccm/custom/libraries/awonderphp/resourcemanager/