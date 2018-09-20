<?php

use Tools\Login;
use Slim\Http\Request;
use Slim\Http\Response;

// Login user
$app->post('/login', function (Request $request, Response $response, array $args) {
    $login = new Login();
    $retValue = $login->doLoginWithPostData();
    $retStatus = $retValue === 'You have been logged in.';

    return $response->withJSON(
        [[
            'loggedInMessage' => $retValue,
            'loggedInStatus' => $retStatus
        ]],
        200,
        JSON_UNESCAPED_UNICODE
    );
});

// Logout user
$app->get('/logout', function (Request $request, Response $response, array $args) {
    $login = new Login();
    $retValue = $login->doLogout();
    $retStatus = $retValue === 'You have been logged out.';

    return $response->withJSON(
        [[
            'loggedInMessage' => $retValue,
            'loggedInStatus' => $retStatus
        ]],
        200,
        JSON_UNESCAPED_UNICODE
    );
});

// Get page content
$app->get('/content', function (Request $request, Response $response, array $args) {
    $login = new Login();
    $loggedInStatusMessage = $login->isUserloggedIn();
    $retValue = array();
    $path = '/app/download/';

    try {
        if ($loggedInStatusMessage === 'Logged in.') {
            // the user is logged in
            // add the status to response
            $loggedIn = new stdClass();
            $loggedIn->loggedIn = true;
            $loggedIn->loggedInMessage = $loggedInStatusMessage;
            array_push($retValue, $loggedIn);
            // get the contents from file system and build the response
            if ($handle = opendir($path)) {
                while (false !== ($entry = readdir($handle))) {
                    if ($entry != '.' && $entry != '..') {
                        $contentEntry = new stdClass();
                        $contentEntry->name = $entry;
                        if ($subHandle = opendir($path . $entry)) {
                            $retSubValue = array();
                            // get intro
                            $introFilePath = $path . $entry . '/Intro.txt';
                            if (file_exists($introFilePath)) {
                                $contentEntry->intro = file_get_contents($introFilePath);
                            }
                            // Sketch, images, PDF, ...
                            while (false !== ($subEntry = readdir($subHandle))) {
                                if ($subEntry != 'Intro.txt' && $subEntry != '.' && $subEntry != '..') {
                                    $contentSubEntry = new stdClass();
                                    $contentSubEntry->name = $subEntry;
                                    if ($subFileHandle = opendir($path . $entry . '/' . $subEntry)    ) {
                                        $retSubFileValue = array();
                                        // iOS, Android,  ...
                                        while (false !== ($subFileEntry = readdir($subFileHandle))) {
                                            if ($subFileEntry != '.' && $subFileEntry != '..') {
                                                $contentSubFileEntry = new stdClass();
                                                $contentSubFileEntry->name = $subFileEntry;
                                                // download files (zip)
                                                $fileDir = $path . $entry . '/' . $subEntry . '/' . $subFileEntry;
                                                if (is_dir($fileDir) && $fileHandle = opendir($fileDir)) {
                                                    $contentSubFileEntry->files = array();
                                                    while (false !== ($fileEntry = readdir($fileHandle))) {
                                                        if (!is_dir($fileEntry) && $fileEntry != '.' && $fileEntry != '..') {
                                                            $fileListEntry = new stdClass();
                                                            $fileListEntry->name = $fileEntry;
                                                            $fileListEntry->size = filesize($fileDir . '/' . $fileEntry);
                                                            $fileListEntry->path = $entry . '/' . $subEntry . '/' . $subFileEntry . '/';
                                                            array_push($contentSubFileEntry->files, $fileListEntry);
                                                        }
                                                    }
                                                }
                                                array_push($retSubFileValue, $contentSubFileEntry);
                                            }
                                        }
                                        $contentSubEntry->subContent = $retSubFileValue;
                                    }
                                    array_push($retSubValue, $contentSubEntry);
                                }
                            }
                            $contentEntry->subContent = $retSubValue;
                        }
                        array_push($retValue, $contentEntry);
                    }
                }
                closedir($handle);
            }
        } else {
            // the user is not logged in
            $loggedIn = new stdClass();
            $loggedIn->loggedInStatus = false;
            $loggedIn->loggedInMessage = 'Not logged in.';
            array_push($retValue, $loggedIn);
        }
    } catch (Exception $e) {
        // the user is not logged in
        $loggedIn = new stdClass();
        $loggedIn->loggedInStatus = $loggedInStatusMessage === 'Logged in.';
        $loggedIn->loggedInMessage = $loggedInStatusMessage;
        array_push($retValue, $loggedIn);
    }
    // send the response to the client
    return $response->withJSON($retValue, 200);
});

// Get files
$app->get('/download', function(Request $request, Response $response, array $args) {
    $login = new Login();
    $file = '/app/download/' . $request->getQueryParams()['file'];

    if ($login->isUserloggedIn() === 'Not logged in.') {
        return $response->withJSON(
            [
                'loggedInMessage' => 'Not logged in.',
                'loggedIn' => false
            ],
            200,
            JSON_UNESCAPED_UNICODE
        );
    }

    $r = $response->withHeader('Content-Description', 'File Transfer')
        ->withHeader('Content-Type', mime_content_type($file))
        ->withHeader('Content-Disposition', 'attachment;filename="' . basename($file) . '"')
        ->withHeader('Expires', '0')
        ->withHeader('Cache-Control', 'must-revalidate')
        ->withHeader('Pragma', 'public')
        ->withHeader('Content-Length', filesize($file));

    readfile($file);
    return $r;
});
