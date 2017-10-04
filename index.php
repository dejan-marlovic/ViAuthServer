<?php
//  Copyright (c) 2017, Patrick Minogue. All rights reserved. Use of this source code
//  is governed by a BSD-style license that can be found in the LICENSE file.

const AUTH_BASE = "";
const HTTP_ORIGIN = "";

header('Accept-Charset: UTF-8');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header("Access-Control-Allow-Origin: " . HTTP_ORIGIN);
header('Content-Type: application/json');

/// REMOVE IN PRODUCTION ///
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
    if (!isset($input['client'])) respond(400, "Client not specified");

    require_once(__DIR__ . AUTH_BASE);
    $auth = new ViAuth($input['client']);

    $response = null;

    switch ($command)
    {
        case "fetch_last_login":
            validOrDie($input, ["Username"]);
            $response = $auth->fetchLastLogin($input['username']);
            break;

        case "login_with_token":
            validOrDie($input, ["Token"]);
            $response = $auth->loginWithToken($input['token']);
            break;

        case "login_with_username_password":
            validOrDie($input, ["Username", "Password"]);
            $response = $auth->loginWithUsernamePassword($input['username'], $input['password']);
            break;

        case "register":
            validOrDie($input, ["Username"]);
            $response = $auth->register($input['username'], isset($input['password']) ? $input['password'] : null);
            break;

        case "update_password":
            validOrDie($input, ["Username", "Password", "Token"]);
            $response = $auth->updatePassword($input['username'], $input['password'], $input['token']);
            break;

        case "send_credentials":
            validOrDie($input, ["username"]);
            if (empty($input['email_message']) && empty($input['sms_message']))
            {
                respond(401, "error_no_message_specified");
            }

            /**
             * Parse placeholders
             */
            $emailMessage = urldecode($input['email_message']);
            $smsMessage = urldecode($input['sms_message']);

            // Reset password
            if (strpos($emailMessage, "%password%") >= 0 || strpos($smsMessage, "%password%") >= 0)
            {
                $password = bin2hex(openssl_random_pseudo_bytes(4));
                $emailMessage = str_replace("%password%", $password, $emailMessage);
                $smsMessage = str_replace("%password%", $password, $smsMessage);

                $token = $auth->resetToken($input['username']);
                if (http_response_code() != 200) die("Could not reset token for user " . $input['username']);

                $token = $auth->updatePassword($input['username'], $password, $token);
                if (http_response_code() != 200) die("Could not update password for user " . $input['username']);

                $emailMessage = str_replace("%token%", $token, $emailMessage);
                $smsMessage = str_replace("%token%", $token, $smsMessage);
            }

            $emailMessage = str_replace("%username%", $input['username'], $emailMessage);
            $smsMessage = str_replace("%username%", $input['username'], $smsMessage);

            /**
             * Call logic implemented by client
             */
            sendCredentials(
                $input['client'],
                $input['username'],
                $input['email_from'],
                $emailMessage,
                $input['email_subject'],
                $input['sms_from'],
                $smsMessage);

            $response = null;
            break;

        case "unregister":
            validOrDie($input, ["Username"]);
            $response = $auth->unregister($input['username']);
            break;

        case "validate_token":
            validOrDie($input, ["Token"]);
            $response = $auth->validateToken($input['token']);
            break;

        default:
            respond(404, "error_command_not_found");
            break;
    }

    respond(http_response_code(), $response);
}

function validOrDie($input, $keys)
{
    foreach ($keys as $key)
    {
        if (empty($input[strtolower($key)])) respond(401, "Missing required key: '${key}'");
    }
}

function respond($status, $body)
{
    http_response_code($status);
    die($body);
}

function sendCredentials($client, $username, $emailFrom, $emailMessage, $emailSubject, $smsFrom, $smsMessage)
{
    /**
     * TODO send credentials to the user within your system
     */

}