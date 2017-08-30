<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of album
 *
 * @author home
 */
class album extends Controller {

    public function index($url = "") {
        $this->view("common/head", ["title" => "Initedit Photo", "description" => "Main Page"]);
        $this->view("common/header", ["isloggedin" => SessionManagement::sessionExists("userid")]);
        $this->view("common/container_start");

        $query = "select albumid from album where url=:url";
        $this->database->query($query);
        $this->database->bind("url", $url);
        $albumid = $this->database->firstColumn();
        $this->view("album/single", ["photos" => $this->singlealbums($albumid),"albumname"=>$url]);
        $this->view("common/container_end");
        $this->view("common/footer");
    }

    private function singlealbums($albumid) {
        $userid = -1;
        if (SessionManagement::sessionExists("userid")) {
            $userid = SessionManagement::sessionExists("userid");
        }
        $query = " select 'album' as page,imgid,img,title,(select count(*) from imagelike where imagelike.imgid=images.imgid and userid=:userid) as likecount,(select count(*) from imagefav where imagefav.imgid=images.imgid and userid=:userid) as favcount
                  from images where albumid=:albumid  and enable=0 order by imgid desc limit " . App::$NUMBER_PHOTO_PER_PAGE;
        $this->database->query($query);
        $this->database->bind("userid", $userid);
        $this->database->bind("albumid", $albumid);
        return $this->database->resultset();
    }

    public function more() {
        $userid = -1;
        if (SessionManagement::sessionExists("userid")) {
            $userid = SessionManagement::sessionExists("userid");
        }
        $photo = isset($_POST['photo']) ? json_decode($_POST['photo']) : "";
        $photo = (array) $photo;
        $query = " select 'album' as page,imgid,img,title,(select count(*) from imagelike where imagelike.imgid=images.imgid and userid=:userid) as likecount,(select count(*) from imagefav where imagefav.imgid=images.imgid and userid=:userid) as favcount
                  from images where albumid=(select albumid from images where imgid=:imgid)  and enable=0 and imgid<:imgid order by imgid desc limit " . App::$NUMBER_PHOTO_PER_PAGE;
        $this->database->query($query);
        $this->database->bind("userid", $userid);
        
        $this->database->bind("imgid", $photo['imgid']);
        $result["code"] = 1;
        $result["photos"] = $this->database->resultset();
        echo json_encode($result);
    }

}
