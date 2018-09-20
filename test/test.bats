#!/usr/bin/env bats

@test "Call of endpoint /content without login" {
    run curl -s http://localhost:1080/content
    [[ $status -eq 0 ]]
    [[ $output = '[{"loggedInStatus":false,"loggedInMessage":"Not logged in."}]' ]]
}

@test "Call of /login with POST data" {
    run curl -s --data "user_name=dev&user_password=dev" http://localhost:1080/login
    [[ $status -eq 0 ]]
    [[ $output = '[{"loggedInMessage":"You have been logged in.","loggedInStatus":true}]' ]]
}

@test "Call of endpoint /content with valid session should return a directory name" {
    run curl -s -c /tmp/test_cookie -o /dev/null --data "user_name=dev&user_password=dev" http://localhost:1080/login
    result=$(curl -s -b /tmp/test_cookie http://localhost:1080/content | jq .[1].name)
    echo $result
    [[ $status -eq 0 ]]
    [[ $result = '"Source1"' ]]
}

@test "Call of endpoint /content with valid session should return a infotext" {
    run curl -s -c /tmp/test_cookie -o /dev/null --data "user_name=dev&user_password=dev" http://localhost:1080/login
    result=$(curl -s -b /tmp/test_cookie http://localhost:1080/content | jq .[1].intro)
    [[ $status -eq 0 ]]
    [[ $result = '"Intro Source1.\n"' ]]
}
