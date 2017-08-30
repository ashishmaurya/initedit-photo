<?php

/**
 * Created by PhpStorm.
 * User: home
 * Date: 2/5/2016
 * Time: 7:41 PM
 */
class Settings
{
    public static function getPostPerPage() {
        return 15;
    }
    public static function getDefaultSort() {
        return "new";
    }
    public static function getSortArray() {
        return array("new","hot","top","rising","view");
    }
    public static function getErrorController() {
        return "error";
    }
    public static function getPrivateUser() {
        return array("admin");
    }
    public static function getDefaultUserImg() {
        return "default.jpg";
    }
    

}