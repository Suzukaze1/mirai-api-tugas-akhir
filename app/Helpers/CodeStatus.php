<?php

namespace App\Helpers;

class CodeStatus
{
    // The request succeeded. The result meaning of "success" depends on the HTTP method:
    public static $SUCCESS = 200;

    // The client does not have access rights to the content; that is, it is unauthorized, so the server is refusing to give the requested resource
    public static $FORBIDDEN = 403;

    // The server can not find the requested resource. In the browser, this means the URL is not recognized.
    public static $NOT_FOUND = 404;

    // Session expired
    public static $TOKEN_EXPIRED = 419;

    // The server has encountered a situation it does not know how to handle.
    public static $INTERNAL_SERVER_ERROR = 500;
}