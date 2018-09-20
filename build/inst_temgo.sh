#!/usr/bin/env bash

set -o pipefail  # trace ERR through pipes
set -o errtrace  # trace ERR through 'time command' and other functions
set -o nounset   ## set -u : exit the script if you try to use an uninitialised variable
set -o errexit   ## set -e : exit the script if any statement returns a non-true return value

###############################################################################
### define/initialize script wide variables

TG_API_URL="https://api.github.com/repos/malud/temgo/releases/latest"


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

# download temgo
temgo_latest="$( curl -s ${TG_API_URL} | \
    jq -r ".assets[] | select(.name | test(\"tg\")) | .browser_download_url" )"
curl -s -o /usr/local/bin/tg -L "${temgo_latest}"
chmod +x /usr/local/bin/tg
type -p tg

# vim: ts=4 sw=4 expandtab ft=sh
