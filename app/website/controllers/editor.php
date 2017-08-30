<?php

class editor extends Controller {

    public function index() {
        $this->view("common/head", ["title" => "Initedit Photo", "description" => "Main Page"]);
        $this->view("common/header", ["isloggedin" => SessionManagement::sessionExists("userid")]);
        $this->view("common/container_start");


        $this->view("editor/all", ["photos" => $this->photos()]);

        $this->view("common/container_end");
        $this->view("common/footer");
    }

    private function photos() {
        $userid = -1;
        if (SessionManagement::sessionExists("userid")) {
            $userid = SessionManagement::sessionExists("userid");
        }
        $query = " select 'editor' as page,imgid,img,title,(select count(*) from imagelike where imagelike.imgid=images.imgid and userid=:userid) as likecount,(select count(*) from imagefav where imagefav.imgid=images.imgid and userid=:userid) as favcount
                  from images where editors_pick!=0  and enable=0 order by imgid desc limit " . App::$NUMBER_PHOTO_PER_PAGE;
        $this->database->query($query);
        $this->database->bind("userid", $userid);
        

        return $this->database->resultset();
    }

    public function more() {
        $userid = -1;
        if (SessionManagement::sessionExists("userid")) {
            $userid = SessionManagement::sessionExists("userid");
        }
        $photo = isset($_POST['photo']) ? json_decode($_POST['photo']) : "";
        $photo = (array) $photo;
        $query = " select 'editor' as page,imgid,img,title,(select count(*) from imagelike where imagelike.imgid=images.imgid and userid=:userid) as likecount,(select count(*) from imagefav where imagefav.imgid=images.imgid and userid=:userid) as favcount
                  from images where editors_pick!=0  and enable=0 and imgid<:imgid order by imgid desc limit " . App::$NUMBER_PHOTO_PER_PAGE;
        $this->database->query($query);
        $this->database->bind("userid", $userid);
        $this->database->bind("imgid", $photo['imgid']);
        $result["code"] = 1;
        $result["photos"] = $this->database->resultset();
        echo json_encode($result);
    }

}
