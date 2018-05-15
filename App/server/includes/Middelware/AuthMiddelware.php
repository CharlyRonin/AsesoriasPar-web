<?php namespace Middelware;

use Slim\Http\Request;
use Slim\Http\Response;

class AuthMiddelware
{

    /**
     * @param $req Request
     * @param $res Response
     * @param $params array
     * @param $next callable
     * @return Response
     */
    public function __invoke($req, $res, $next)
    {
        //$res->getBody()->write("MID");
        $res = $next($req, $res);
        return $res;
    }

}