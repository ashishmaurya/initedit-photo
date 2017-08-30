<?php

/**
 * Created by PhpStorm.
 * User: home
 * Date: 2/5/2016
 * Time: 1:11 PM
 */
class App {

    protected $controller = "home";
    protected $method = "index";
    protected $params = [];
    public static $NUMBER_PHOTO_PER_PAGE = 12;

    public function __construct() {
        if (!SessionManagement::sessionExists("userid") && CookieManagment::getCookie("remember") == "true") {
            $loginInfo = CookieManagment::getCookie("loginInfo");
            $controller = new Controller();
            $query = "select userid,img,username,logininfo from usersignup where logininfo=:logininfo";
            $controller->database->query($query);
            $controller->database->bind("logininfo", $loginInfo);
            $info = $controller->database->single();
            if (isset($info['username'])) {
                SessionManagement::sessionStart();
                SessionManagement::setSession("userid", $info['userid']);
                SessionManagement::setSession("img", $info['img']);
                SessionManagement::setSession("username", $info['username']);
                SessionManagement::setSession("logininfo", $info['logininfo']);    
            }
        }

        $url = $this->parseUrl();

        if (file_exists("../app/website/controllers/" . $url[0] . ".php")) {
            $this->controller = $url[0];
            unset($url[0]);
        }
        require_once "../app/website/controllers/" . $this->controller . ".php";

        $this->controller = new $this->controller;

        if (isset($url[1])) {
            if (method_exists($this->controller, $url[1])) {
                $this->method = $url[1];
                unset($url[1]);
            }
        }
        $this->params = $url ? array_values($url) : [];

        //call_user_func([$this->controller,$this->method],$this->params);
        call_user_func_array([$this->controller, $this->method], $this->params);
    }

    public function parseUrl() {
        if (isset($_GET['url'])) {
            return $url = explode("/", filter_var(rtrim($_GET['url'], "/"), FILTER_SANITIZE_URL));
        }
    }

}
