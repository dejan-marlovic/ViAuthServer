<?php
//  Copyright (c) 2017, Patrick Minogue. All rights reserved. Use of this source code
//  is governed by a BSD-style license that can be found in the LICENSE file.

const AUTH_BASE = "";
const HTTP_ORIGIN = "";

header('Accept-Charset: UTF-8');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Content-Type: application/json');

/// REMOVE IN PRODUCTION ///
header("Access-Control-Allow-Origin: " . HTTP_ORIGIN);
ini_set('display_errors', 1);
error_reporting(E_ALL);
////////////////////////////

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST')
{
    // Retrieve the table and key from the path, and input from php://input
    $request = explode('/', trim($_SERVER['PATH_INFO'], '/'));
    $command = preg_replace('/[^a-z0-9_]+/i','',array_shift($request));

    $input = json_decode(file_get_contents('php://input'), true);

    require_once(__DIR__ . AUTH_BASE);
    $auth = new ViAuth();

    $responseJSON = null;

    switch ($command)
    {
        case "login_with_token":
            validOrDie($input, ["Token"]);
            $responseJSON = $auth->loginWithToken($input['token']);
            break;

        case "login_with_username_password":
            validOrDie($input, ["Username", "Password"]);
            $responseJSON = $auth->loginWithUsernamePassword($input['username'], $input['password']);
            break;

        case "register":
            validOrDie($input, ["Username"]);
            $responseJSON = $auth->register($input['username'], isset($input['password']) ? $input['password'] : null);
            break;

        case "reset_password":
            validOrDie($input, ["Username", "Password", "Token"]);
            $responseJSON = $auth->resetPassword($input['username'], $input['password'], $input['token']);
            break;

        case "reset_token":
            validOrDie($input, ["Username"]);
            $responseJSON = $auth->resetToken($input['username']);
            break;

        case "unregister":
            validOrDie($input, ["Username"]);
            $responseJSON = $auth->unregister($input['username']);
            break;

        case "validate_token":
            validOrDie($input, ["Token"]);
            $responseJSON = $auth->validateToken($input['token']);
            break;

        default:
            respond(404, "Command '$command' not found");
            break;
    }

    $response = json_decode($responseJSON, true);
    respond($response['status'], $response['body']);
}

function validOrDie($input, $keys)
{
    foreach ($keys as $key)
    {
        if (empty($input[strtolower($key)])) respond(401, "$key not specified");
    }
}

function respond($status, $body)
{
    http_response_code($status);
    die($body);
}
