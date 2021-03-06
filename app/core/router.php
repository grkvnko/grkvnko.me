<?php

class Router
{
    private static $routes;

    function __construct()
    {
        self::$routes = include '/app/routes/Routes.php';
    }

    private function getArrayFromURI()
    {
        $dummy_server_path = htmlentities($_SERVER['PHP_SELF']);
        $dummy_server_path = str_replace('index.php', '', $dummy_server_path);

        $CUT_REQUEST_URI = mb_substr($_SERVER['REQUEST_URI'], 0, 120);
        $m_request_uri = htmlentities($CUT_REQUEST_URI, ENT_QUOTES | ENT_HTML401);

        if ($dummy_server_path !== '/') {
            $m_request_uri = str_replace($dummy_server_path, "", $m_request_uri);
        } else {
            $m_request_uri = mb_substr($m_request_uri, 1);
        }

        return explode('/', $m_request_uri, 5);
    }

    public function run()
    {
        $routes_arr = $this->getArrayFromURI();
        $route_way = array_shift($routes_arr);

        if (array_key_exists($route_way, self::$routes)) {
            $route_control = self::$routes[$route_way];
        } else {
            $route_control = self::$routes['404'];
        }

        $controllerAction = explode('/', $route_control);
        $ch_controller = array_shift($controllerAction);
        $ch_controller = new $ch_controller;
        $ch_controller->$controllerAction[0](
            $routes_arr,
            (count($controllerAction) > 1) ? $controllerAction[1] : null
        );
    }
}