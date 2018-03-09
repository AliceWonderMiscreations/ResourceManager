#!/bin/bash

#This will be deleted before release

cd lib

cp Stract/*.php /usr/share/ccm/custom/libraries/awonderphp/resourcemanager/Stract/
[ -d Face ] && cp Face/* /usr/share/ccm/custom/libraries/awonderphp/resourcemanager/Face/
[ -d Exception ] && cp Exception/* /usr/share/ccm/custom/libraries/awonderphp/resourcemanager/Exception/

cp *.php /usr/share/ccm/custom/libraries/awonderphp/resourcemanager/