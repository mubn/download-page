#!/usr/bin/env bash

set -x

set -o pipefail  # trace ERR through pipes
set -o errtrace  # trace ERR through 'time command' and other functions
set -o nounset   ## set -u : exit the script if you try to use an uninitialised variable
set -o errexit   ## set -e : exit the script if any statement returns a non-true return value

###############################################################################
### define/initialize script wide variables

DEBUG=0
PROGNAME="${0##*/}"

################################################################################
### functions
# echo to STDERR
function _err_echo () {
    local msg
    msg="$*"
    local d
    d=$(date '+%b %d %H:%M:%S')
    echo "$d $msg" >&2
}
# echo to STDERR and exit
function _bail () {
    local message
    message="$*"
    _err_echo "$message"
    exit 2
}

### main script
# Setup application configuration
tg -s < /app/src/classes/constants.php.tpl > /app/src/classes/Constants.php

# Run default entrypoint
exec /entrypoint "$@"

# vim: ts=4 sw=4 expandtab ft=sh
