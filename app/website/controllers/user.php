<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of user
 *
 * @author home
 */
class user extends Controller {

    public function index($username = "", $page = "photo") {
        $this->view("common/head", ["title" => "Initedit Photo", "description" => "Main Page"]);
        $this->view("common/header", ["isloggedin" => SessionManagement::sessionExists("userid")]);
        $this->view("common/container_start");

        $userid = 1;
        $query = "select userid,img,cover from usersignup where username=:username";
        $this->database->query($query);
        $this->database->bind("username", $username);
        $userInfo = $this->database->single();
        $userid = $userInfo['userid'];
        $userimg = $userInfo['img'];
        $usercover = $userInfo['cover'];
        $this->view("user/top", ["menu" => $page, "username" => $username,"userInfo"=>$userInfo,]);
        if ($page == "photo") {
            $photos = $this->photo($userid);
            if (count($photos) == 0) {
                $this->view("user/empty", []);
            } else {
                $this->view("user/photo", ["photos" => $photos]);
            }
        } else if ($page == "like") {
            $photos = $this->like($userid);
            if (count($photos) == 0) {
                $this->view("user/empty", []);
            } else {
                $this->view("user/photo", ["photos" => $photos]);
            }
        } else if ($page == "favorite") {
            $photos = $this->favorite($userid);
            if (count($photos) == 0) {
                $this->view("user/empty", []);
            } else {
                $this->view("user/photo", ["photos" => $photos]);
            }
        } else if ($page == "album") {
            $albums = $this->album(["userid" => $userid]);
            if (count($albums) == 0) {
                $this->view("user/empty", []);
            } else {
                $this->view("user/album", ["albums" => $albums]);
            }
        }
        $this->view("common/container_end");
        $this->view("common/footer");
    }

    private function album($info) {

        $query = " select 'user_album' as page,images.* ,
                    (select album from album where album.albumid=images.albumid) as album,
                    (select url from album where album.albumid=images.albumid) as albumurl
                  from images where userid=:userid  and enable=0 group by albumid order by imgid desc  limit " . App::$NUMBER_PHOTO_PER_PAGE;

        $this->database->query($query);
        $this->database->bind("userid", $info['userid']);
//        $this->database->bind("albumid", $info['albumid']);
        return $this->database->resultset();
    }

    private function photo($userid) {
        $query = " select 'user_photo' as page,imgid,img,title,(select count(*) from imagelike where imagelike.imgid=images.imgid and userid=:userid) as likecount,(select count(*) from imagefav where imagefav.imgid=images.imgid and userid=:userid) as favcount
                  from images where userid=:userid  and enable=0 order by imgid desc limit " . App::$NUMBER_PHOTO_PER_PAGE;
        $this->database->query($query);
        $this->database->bind("userid", $userid);
        return $this->database->resultset();
    }

    private function like($userid) {
        $query = " select 'user_like' as page,:userid as user_profile,imgid,img,title,(select count(*) from imagelike where imagelike.imgid=images.imgid and userid=:userid) as likecount,(select count(*) from imagefav where imagefav.imgid=images.imgid and userid=:userid) as favcount
                  from images where  enable=0 and imgid in (select imgid from imagelike where userid=:userid) order by imgid desc limit " . App::$NUMBER_PHOTO_PER_PAGE;
        $this->database->query($query);
        $this->database->bind("userid", $userid);
        return $this->database->resultset();
    }

    private function favorite($userid) {
        $query = " select 'user_favorite' as page,:userid as user_profile,imgid,img,title,(select count(*) from imagelike where imagelike.imgid=images.imgid and userid=:userid) as likecount,(select count(*) from imagefav where imagefav.imgid=images.imgid and userid=:userid) as favcount
                  from images where  enable=0 and imgid in (select imgid from imagefav where userid=:userid) order by imgid desc limit " . App::$NUMBER_PHOTO_PER_PAGE;
        $this->database->query($query);
        $this->database->bind("userid", $userid);
        return $this->database->resultset();
    }

    public function more($page = "photo") {
        $userid = -1;
        $photo = isset($_POST['photo']) ? json_decode($_POST['photo']) : "";
        
        $photo = (array) $photo;
        $page = $photo['page'];
        $query = "select userid from images where imgid=:imgid";
        $this->database->query($query);
        $this->database->bind("imgid", $photo['imgid']);
        $userid = $this->database->firstColumn();
        if ($page == "user_photo") {
            $query = "select 'user_photo' as page,imgid,img,title,(select count(*) from imagelike where imagelike.imgid=images.imgid and userid=:userid) as likecount,(select count(*) from imagefav where imagefav.imgid=images.imgid and userid=:userid) as favcount
                  from images where userid=:userid  and enable=0 and imgid<:imgid order by imgid desc limit " . App::$NUMBER_PHOTO_PER_PAGE;
            $this->database->query($query);
            $this->database->bind("userid", $userid);
            $this->database->bind("imgid", $photo['imgid']);
        } else if ($page == "user_like") {
            $query = " select 'user_like' as page,:userid as user_profile,imgid,img,title,(select count(*) from imagelike where imagelike.imgid=images.imgid and userid=:userid) as likecount,(select count(*) from imagefav where imagefav.imgid=images.imgid and userid=:userid) as favcount
                  from images where  enable=0 and imgid<:imgid and imgid in (select imgid from imagelike where userid=:userid) order by imgid desc limit " . App::$NUMBER_PHOTO_PER_PAGE;
            $this->database->query($query);
            $this->database->bind("userid", $userid);
            $this->database->bind("imgid", $photo['imgid']);
        } else if ($page == "user_favorite") {
            $query = " select 'user_favorite' as page,:userid as user_profile,imgid,img,title,(select count(*) from imagelike where imagelike.imgid=images.imgid and userid=:userid) as likecount,(select count(*) from imagefav where imagefav.imgid=images.imgid and userid=:userid) as favcount
                  from images where enable=0 and imgid<:imgid and imgid in (select imgid from imagefav where userid=:userid) order by imgid desc limit " . App::$NUMBER_PHOTO_PER_PAGE;
            $this->database->query($query);
            $this->database->bind("userid", $userid);
            $this->database->bind("imgid", $photo['imgid']);
        } else if ($page == "user_album") {
            $query = " select 'user_album' as page,images.* ,
                    (select album from album where album.albumid=images.albumid) as album,
                    (select url from album where album.albumid=images.albumid) as albumurl
                  from images where userid=:userid  and enable=0 and albumid<:albumid group by albumid order by imgid desc  limit " . App::$NUMBER_PHOTO_PER_PAGE;
            $this->database->query($query);
            $this->database->bind("userid", $userid);
            $this->database->bind("albumid", $photo['albumid']);
        }


        $result["code"] = 1;

        $result["photo"] = $photo;
        $result["photos"] = $this->database->resultset();
        echo json_encode($result);
    }

}
