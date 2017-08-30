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
class album_1 extends Controller {

    public function index($url = "") {
        $query = "select albumid from album where url=:url";
        $this->database->query($query);
        $this->database->bind("url", $url);
        $albumid = $this->database->firstColumn();
        $this->view("album/album-single", ["photos" => $this->single_albums($albumid),"albumname"=>$url]);
    }

    private function single_albums($albumid) {
        $userid = -1;
        if (SessionManagement::sessionExists("userid")) {
            $userid = SessionManagement::sessionExists("userid");
        }
        $query = " select imgid from images where albumid=:albumid  and enable=0 order by imgid desc limit " . App::$NUMBER_PHOTO_PER_PAGE;
        $this->database->query($query);
        $this->database->bind("albumid", $albumid);
        $img_ids = $this->database->columnArray();
        
        $images = array_map(function($id){
            return get_image_post($id);
        }, $img_ids);
        return $images;
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
        $img_ids = $this->database->columnArray();
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
