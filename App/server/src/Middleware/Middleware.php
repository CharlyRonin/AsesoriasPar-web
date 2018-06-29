<?php namespace App\Middleware;


use Slim\Http\Request;

abstract class Middleware
{
    /**
     * @param $req Request
     * @return array Route Params
     */
    public static function getRouteParams($req){
        return $params = $req->getAttributes()['routeInfo'][2];
    }


}