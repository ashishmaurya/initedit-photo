<?php

/**
 * Created by PhpStorm.
 * User: home
 * Date: 2/5/2016
 * Time: 1:17 PM
 */
//http://culttt.com/2012/10/01/roll-your-own-pdo-php-class/

//
//SELECT CONCAT('truncate table ',table_name,';')
//FROM INFORMATION_SCHEMA.TABLES
//WHERE TABLE_SCHEMA = 'local_photo_initedit'
//AND TABLE_TYPE = 'BASE TABLE';


class recent extends Controller {

    public function index() {
        $this->view("common/head", ["title" => "Initedit Photo", "description" => "Main Page"]);
        $this->view("common/header", ["isloggedin" => SessionManagement::sessionExists("userid")]);
        $this->view("common/container_start");
        $this->view("home/home",["photos"=>  $this->photos()]);
        $this->view("common/container_end");
        $this->view("common/footer");
    }


    private function photos() {
        $userid = -1;
        if (SessionManagement::sessionExists("userid")) {
            $userid = SessionManagement::getSession("userid");
        }
        $query = " select 'home' as page, imgid,img,title,(select count(*) from imagelike where imagelike.imgid=images.imgid and userid=:userid) as likecount,(select count(*) from imagefav where imagefav.imgid=images.imgid and userid=:userid) as favcount
                  from images where enable=0 order by imgid desc limit ".  App::$NUMBER_PHOTO_PER_PAGE;
        $this->database->query($query);
        $this->database->bind("userid", $userid);
        return $this->database->resultset();
    }
    public function more() {
        $userid = -1;
        if (SessionManagement::sessionExists("userid")) {
            $userid = SessionManagement::sessionExists("userid");
        }
        $photo = isset($_POST['photo'])?json_decode($_POST['photo']):"";
        $photo = (array)$photo;
        $query = " select 'home' as page, imgid,img,title,(select count(*) from imagelike where imagelike.imgid=images.imgid and userid=:userid) as likecount,(select count(*) from imagefav where imagefav.imgid=images.imgid and userid=:userid) as favcount
                  from images where imgid<:imgid  and enable=0 order by imgid desc limit ".  App::$NUMBER_PHOTO_PER_PAGE;
        $this->database->query($query);
        $this->database->bind("userid", $userid);
        $this->database->bind("imgid", $photo['imgid']);
        $result["code"]=1;
        $result["photos"]=$this->database->resultset();
        echo json_encode($result);
    }
}
