<?php

class hash extends Controller {

    public function index($hash = "") {
        $this->view("common/head", ["title" => "Initedit Photo", "description" => "Main Page"]);
        $this->view("common/header", ["isloggedin" => SessionManagement::sessionExists("userid")]);
        $this->view("common/container_start");

        if (empty($hash)) {
            $this->view("hash/all", ["hash" => $this->allhash(),"popular"=>  $this->getPopularHash(),"trending"=>$this->getTrendingHash()]);
        } else {
            $this->view("hash/single", ["photos" => $this->singlehash($hash), "hash" => $hash]);
        }
        $this->view("common/container_end");
        $this->view("common/footer");
    }

    private function allhash() {
        $userid = -1;
        if (SessionManagement::sessionExists("userid")) {
            $userid = SessionManagement::sessionExists("userid");
        }
        $query = "select count(*) as total,hash from hash group by hash";
        $this->database->query($query);
        return $this->database->resultset();
    }

    private function getPopularHash() {
        $userid = -1;
        if (SessionManagement::sessionExists("userid")) {
            $userid = SessionManagement::sessionExists("userid");
        }
        $query = "select count(*) as total,hash,(select images.img from images where images.imgid=hash.imgid) as hashimg from hash group by hash order by total desc limit 4";
        $this->database->query($query);
        $result = $this->database->resultset();
        return $result;
    }
    
    private function getTrendingHash() {
        $userid = -1;
        if (SessionManagement::sessionExists("userid")) {
            $userid = SessionManagement::sessionExists("userid");
        }
        $query = "select count(*) as total,hash,(select images.img from images where images.imgid=hash.imgid) as hashimg from (select * from hash order by hashid desc limit 30) as hash group by hash order by total desc limit 4";
        $this->database->query($query);
        $result = $this->database->resultset();
        return $result;
    }

    private function singlehash($hash) {
        $userid = -1;
        if (SessionManagement::sessionExists("userid")) {
            $userid = SessionManagement::sessionExists("userid");
        }
        $query = " select 'hash' as page,:hash as hashtag,imgid,img,title,(select count(*) from imagelike where imagelike.imgid=images.imgid and userid=:userid) as likecount,(select count(*) from imagefav where imagefav.imgid=images.imgid and userid=:userid) as favcount
                  from images where enable=0 and imgid in (select imgid from hash where hash=:hash) order by imgid desc limit " . App::$NUMBER_PHOTO_PER_PAGE;
        $this->database->query($query);
        $this->database->bind("userid", $userid);
        $this->database->bind("hash", $hash);

        return $this->database->resultset();
    }

    public function more() {
        $userid = -1;
        if (SessionManagement::sessionExists("userid")) {
            $userid = SessionManagement::sessionExists("userid");
        }
        $photo = isset($_POST['photo']) ? json_decode($_POST['photo']) : "";
        $photo = (array) $photo;
        $query = " select 'hash' as page,imgid,img,title,(select count(*) from imagelike where imagelike.imgid=images.imgid and userid=:userid) as likecount,(select count(*) from imagefav where imagefav.imgid=images.imgid and userid=:userid) as favcount
                  from images where enable=0 and imgid in (select imgid from hash where hash=:hash) and imgid<:imgid order by imgid desc limit " . App::$NUMBER_PHOTO_PER_PAGE;
        $this->database->query($query);
        $this->database->bind("userid", $userid);
        $this->database->bind("imgid", $photo['imgid']);
        $this->database->bind("hash", $photo['hashtag']);
        $result["code"] = 1;
        $result["photos"] = $this->database->resultset();
        echo json_encode($result);
    }

}
