#!/bin/sh
# $Id: makedoc.sh 229 2008-05-18 19:03:54Z plamendp $

#/**
#  * Generate documentation using phpDocumentor (http://www.phpdoc.org)
#  */

#/**
#  * path of PHPDoc executable
#  *
#  * @var               string PATH_PHPDOC
#  */
PATH_PHPDOC=/usr/local/bin/phpdoc

#/**
#  * Configuration file
#  *
#  * @var               string PATH_CONFIG
#  */
PATH_CONFIG=./makedoc.ini

# make documentation
"$PATH_PHPDOC" -c "$PATH_CONFIG"

