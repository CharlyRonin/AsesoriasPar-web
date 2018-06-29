<?php namespace App\Exceptions\Request;


use App\Utils;

class ForbiddenException extends RequestException
{
    public function __construct($message = "")
    {
        parent::__construct($message, Utils::$FORBIDDEN);
    }

}