<?php namespace App\Exceptions\Auth;


class TokenException extends \Exception
{
    /**
     * TokenException constructor.
     *
     * @param $message String
     */
    public function __construct($message)
    {
        parent::__construct($message);
    }
}