<?php

/**
 * Created by PhpStorm.
 * User: home
 * Date: 2/5/2016
 * Time: 1:13 PM
 */
class Controller
{
    public $database;
    
    /**
     * Controller constructor.
     */
    public function __construct()
    {
        $this->database = new Database();
    }

    public function model($model)
    {
        require_once "../app/website/models/".$model.".php";
        return new $model();
    }
    public function view($view,$data=[])
    {
        //require_once "app/views/".$view.".php";
        include "../app/website/views/".$view.".php";

    }

    public function loadController($controllerPath)
    {
        if (file_exists("../app/website/controllers/" . $controllerPath . ".php")) {
            require_once "../app/website/controllers/" . $controllerPath . ".php";
            return new $controllerPath;
        }else{
            return new Exception("Controller Class Not Found.");
        }

    }
    public function loadTools($controllerPath)
    {
        if (file_exists("../app/website/tools/" . $controllerPath . ".php")) {
            require_once "../app/website/tools/" . $controllerPath . ".php";
        }else{
            return new Exception("tools Class Not Found.");
        }

    }


    public function error($ERROR_NO = 404,$ERROR_REASON = "")
    {
        $this->view("common/error",["error_no"=>$ERROR_NO,"error_reason"=>$ERROR_REASON]);
    }

}