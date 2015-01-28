#!/bin/bash

trap 'exit' ERR

SCRIPTFILE=$(readlink -f "$0")
SCRIPTDIR=$(dirname "$SCRIPTFILE")


if [ -z "$1" ]; then
 REPPORT_TYPE=summary
else
 REPPORT_TYPE=$1
fi



$SCRIPTDIR/../vendor/bin/phpcs --standard="$SCRIPTDIR/csrules.xml" --extensions=php --warning-severity=0 "$SCRIPTDIR/../lib/" --report="$REPPORT_TYPE"