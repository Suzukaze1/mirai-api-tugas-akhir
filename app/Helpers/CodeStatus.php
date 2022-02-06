<?php

namespace App\Helpers;

class CodeStatus
{
    // The request succeeded. The result meaning of "success" depends on the HTTP method:
    public static $code_ok = 200;

    // The client does not have access rights to the content; that is, it is unauthorized, so the server is refusing to give the requested resource
    public static $code_forbidden = 403;

    // The server can not find the requested resource. In the browser, this means the URL is not recognized.
    public static $code_not_found = 404;

    // The server has encountered a situation it does not know how to handle.
    public static $code_internal_server_error = 500;
}