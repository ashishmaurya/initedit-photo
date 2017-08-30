<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of photo
 *
 * @author home
 */
class photo extends Controller {

    public function index($url = "") {
        $this->view("common/head", ["title" => "Initedit Photo", "description" => "Main Page"]);
        $this->view("common/header", ["isloggedin" => SessionManagement::sessionExists("userid")]);
        $this->view("common/container_start");


        $userid = -1;
        if (SessionManagement::sessionExists("userid")) {
            $userid = SessionManagement::getSession("userid");
        }
        $query = "select  view  as viewount,title as title,url as url,imgid as imgid,img as img,"
                . "(select username from usersignup where usersignup.userid=images.userid) as username,"
                . "(select img from usersignup where usersignup.userid=images.userid) as user_icon,"
                . "(select album from album where album.albumid=images.albumid) as albumname, "
                . "(select url from album where album.albumid=images.albumid) as albumurl, "
                . "(select count(*) from imagelike where imgid=images.imgid) as likecount,"
                . "(select count(*) from imagefav where imgid=images.imgid) as favount "
                . "from images where url=:url";
        $this->database->query($query);
        $this->database->bind("url", $url);
        $info = $this->database->single();
        $result = ["code" => 1];
        $result['title'] = $info['title'];
        $result['url'] = $info['url'];
        $result['username'] = $info['username'];
        $result['usericon'] = $info['user_icon'];
        $result['albumname'] = $info['albumname'];
        $result['albumurl'] = $info['albumurl'];
        $result['viewcount'] = $info['viewount'];
        $result['likecount'] = $info['likecount'];
        $result['favcount'] = $info['favount'];
        $result['img'] = $info['img'];
        $result['imgid'] = $info['imgid'];
        $photo['imgid'] = $info['imgid'];
        $photo['img'] = $info['img'];

        $query = "update images set view=view + 1 where imgid=:imgid";
        $this->database->query($query);
        $this->database->bind("imgid", $info['imgid']);
        $this->database->execute();



        list($width, $height, $type, $attr) = getimagesize("images/original/" . $photo['img']);

        $size = filesize("images/original/" . $photo['img']);
        $displaySize = $size . " Byte";
        if ($size > 1024) {
            $displaySize = round(($size / 1024)) . " KB";
        }
        if (($size / 1024) > 1024) {
            $displaySize = round(($size / 1024) / 1024) . " MB";
        }


        $result['size'] = $displaySize;
        $result['width'] = $width;
        $result['height'] = $height;
        $result['type'] = $type;
        $result['attr'] = $attr;



        $query = "select count(*) from imagefav where imgid=:imgid and userid=:userid";
        $this->database->query($query);
        $this->database->bind("userid", $userid);
        $this->database->bind("imgid", $photo['imgid']);
        $status = $this->database->firstColumn();
        $result['userfav'] = $status;


        $query = "select count(*) from imagelike where imgid=:imgid and userid=:userid";
        $this->database->query($query);
        $this->database->bind("userid", $userid);
        $this->database->bind("imgid", $photo['imgid']);
        $status = $this->database->firstColumn();
        $result['userlike'] = $status;

        $query = "select url from images where imgid<:imgid and enable=0 order by imgid desc limit 1";
        $this->database->query($query);
//        $this->database->bind("userid", $userid);
        $this->database->bind("imgid", $photo['imgid']);

        $result['next'] = $this->database->single();


        $query = "select url from images where imgid>:imgid  and enable=0 order by imgid asc limit 1";
        $this->database->query($query);
//        $this->database->bind("userid", $userid);
        $this->database->bind("imgid", $photo['imgid']);

        $result['previous'] = $this->database->single();

        $query_image = "select meta_key,meta_value from image_meta where imgid=:imgid and meta_key not in ('FileName','FileDateTime','FileSize','FileType','html','title','Height','Width','SectionsFound')";
        $this->database->query($query_image);
        $this->database->bind("imgid", $photo['imgid']);
        $result['extra'] = $this->database->resultSet();

        $this->view("photo/full", ["info" => $result]);
        $this->view("common/container_end");
        $this->view("common/footer");
    }

    public function delete() {
        $photo = isset($_POST['id']) ? ($_POST['id']) : "";
        
        $result = ["code" => 100, "message" => "Unknown Error."];
        if (!SessionManagement::sessionExists("userid")) {
            $result = ["code" => 101, "message" => "Login First"];
        } else if (empty($photo)) {
            $result = ["code" => 102, "message" => "Unknown Error.<br/>Refresh Page."];
        } else if (!is_numeric($photo)) {
            $result = ["code" => 104, "message" => "Unknown Photo."];
        } else {
            $query = "select * from images where imgid=:imgid and enable=0";
            $this->database->query($query);
            $this->database->bind("imgid", $photo);
            $status = $this->database->single();
            if ($status) {
                $userid = SessionManagement::getSession("userid");
                $img = $status["img"];
                if ($userid == $status["userid"]) {
                    unlink("images/original/" . $img);
                    unlink("images/compressed/" . $img);
                    unlink("images/thumb/" . $img);
                    $query = "delete from images where imgid=:imgid";
                    $this->database->query($query);
                    $this->database->bind("imgid", $photo);
                    $status = $this->database->execute();
                    if ($status) {
                        $result = ["code" => 1, "message" => "Deleted."];
                        $result["url"]="/home";
                    } else {
                        $result = ["code" => 104, "message" => "Unknown Error."];
                    }
                } else {
                    $result = ["code" => 105, "message" => "Access denied."];
                }
            } else {
                $result = ["code" => 106, "message" => "Photo Not Found."];
            }
        }
        echo json_encode($result);
    }

    public function like() {
        $photo = isset($_POST['photo']) ? json_decode($_POST['photo']) : "";
        $photo = (array) $photo;
        $result = ["code" => 100, "message" => "Unknown Error."];
        if (!SessionManagement::sessionExists("userid")) {
            $result = ["code" => 101, "message" => "Login First"];
        } else if (empty($photo)) {
            $result = ["code" => 102, "message" => "Unknown Error.<br/>Refresh Page."];
        } else if (empty($photo['imgid'])) {
            $result = ["code" => 103, "message" => "Refresh Page."];
        } else if (!is_numeric($photo['imgid'])) {
            $result = ["code" => 104, "message" => "Unknown Photo."];
        } else {
            $query = "select count(*) from images where imgid=:imgid and enable=0";
            $this->database->query($query);
            $this->database->bind("imgid", $photo['imgid']);
            $status = $this->database->firstColumn();
            if ($status == 1) {
                $userid = SessionManagement::getSession("userid");
                $query = "select count(*) from imagelike where imgid=:imgid and userid=:userid";
                $this->database->query($query);
                $this->database->bind("userid", $userid);
                $this->database->bind("imgid", $photo['imgid']);
                $status = $this->database->firstColumn();
                if ($status == 0) {
                    $query = "insert into imagelike(userid,imgid) values(:userid,:imgid)";
                    $this->database->query($query);
                    $this->database->bind("userid", $userid);
                    $this->database->bind("imgid", $photo['imgid']);
                    $status = $this->database->execute();
                    if ($status == 1) {
                        $result = ["code" => 1, "message" => "Liked."];
                    } else {
                        $result = ["code" => 104, "message" => "Unknown Error."];
                    }
                } else {
                    $result = ["code" => 105, "message" => "Already Liked."];
                }
            } else {
                $result = ["code" => 106, "message" => "Photo Not Found."];
            }
        }
        echo json_encode($result);
    }

    public function dislike() {
        $photo = isset($_POST['photo']) ? json_decode($_POST['photo']) : "";
        $photo = (array) $photo;
        $result = ["code" => 100, "message" => "Unknown Error."];
        if (!SessionManagement::sessionExists("userid")) {
            $result = ["code" => 101, "message" => "Login First"];
        } else if (empty($photo)) {
            $result = ["code" => 102, "message" => "Unknown Error.<br/>Refresh Page."];
        } else if (empty($photo['imgid'])) {
            $result = ["code" => 103, "message" => "Refresh Page."];
        } else if (!is_numeric($photo['imgid'])) {
            $result = ["code" => 104, "message" => "Unknown Photo."];
        } else {
            $query = "select count(*) from images where imgid=:imgid and enable=0";
            $this->database->query($query);
            $this->database->bind("imgid", $photo['imgid']);
            $status = $this->database->firstColumn();
            if ($status == 1) {
                $userid = SessionManagement::getSession("userid");
                $query = "select count(*) from imagelike where imgid=:imgid and userid=:userid";
                $this->database->query($query);
                $this->database->bind("userid", $userid);
                $this->database->bind("imgid", $photo['imgid']);
                $status = $this->database->firstColumn();
                if ($status == 1) {
                    $query = "delete from imagelike where imgid=:imgid and userid=:userid";
                    $this->database->query($query);
                    $this->database->bind("userid", $userid);
                    $this->database->bind("imgid", $photo['imgid']);
                    $status = $this->database->execute();
                    if ($status == 1) {
                        $result = ["code" => 1, "message" => "Disliked."];
                    } else {
                        $result = ["code" => 104, "message" => "Unknown Error."];
                    }
                } else {
                    $result = ["code" => 105, "message" => "Already Disliked."];
                }
            } else {
                $result = ["code" => 106, "message" => "Photo Not Found."];
            }
        }
        echo json_encode($result);
    }

    public function fav() {
        $photo = isset($_POST['photo']) ? json_decode($_POST['photo']) : "";
        $photo = (array) $photo;
        $result = ["code" => 100, "message" => "Unknown Error."];
        if (!SessionManagement::sessionExists("userid")) {
            $result = ["code" => 101, "message" => "Login First"];
        } else if (empty($photo)) {
            $result = ["code" => 102, "message" => "Unknown Error.<br/>Refresh Page."];
        } else if (empty($photo['imgid'])) {
            $result = ["code" => 103, "message" => "Refresh Page."];
        } else if (!is_numeric($photo['imgid'])) {
            $result = ["code" => 104, "message" => "Unknown Photo."];
        } else {
            $query = "select count(*) from images where imgid=:imgid and enable=0";
            $this->database->query($query);
            $this->database->bind("imgid", $photo['imgid']);
            $status = $this->database->firstColumn();
            if ($status == 1) {
                $userid = SessionManagement::getSession("userid");
                $query = "select count(*) from imagefav where imgid=:imgid and userid=:userid";
                $this->database->query($query);
                $this->database->bind("userid", $userid);
                $this->database->bind("imgid", $photo['imgid']);
                $status = $this->database->firstColumn();
                if ($status == 0) {
                    $query = "insert into imagefav(userid,imgid) values(:userid,:imgid)";
                    $this->database->query($query);
                    $this->database->bind("userid", $userid);
                    $this->database->bind("imgid", $photo['imgid']);
                    $status = $this->database->execute();
                    if ($status == 1) {
                        $result = ["code" => 1, "message" => "Favorited."];
                    } else {
                        $result = ["code" => 104, "message" => "Unknown Error."];
                    }
                } else {
                    $result = ["code" => 105, "message" => "Already Favorited."];
                }
            } else {
                $result = ["code" => 106, "message" => "Photo Not Found."];
            }
        }
        echo json_encode($result);
    }

    public function unfav() {
        $photo = isset($_POST['photo']) ? json_decode($_POST['photo']) : "";
        $photo = (array) $photo;
        $result = ["code" => 100, "message" => "Unknown Error."];
        if (!SessionManagement::sessionExists("userid")) {
            $result = ["code" => 101, "message" => "Login First"];
        } else if (empty($photo)) {
            $result = ["code" => 102, "message" => "Unknown Error.<br/>Refresh Page."];
        } else if (empty($photo['imgid'])) {
            $result = ["code" => 103, "message" => "Refresh Page."];
        } else if (!is_numeric($photo['imgid'])) {
            $result = ["code" => 104, "message" => "Unknown Photo."];
        } else {
            $query = "select count(*) from images where imgid=:imgid and enable=0";
            $this->database->query($query);
            $this->database->bind("imgid", $photo['imgid']);
            $status = $this->database->firstColumn();
            if ($status == 1) {
                $userid = SessionManagement::getSession("userid");
                $query = "select count(*) from imagefav where imgid=:imgid and userid=:userid";
                $this->database->query($query);
                $this->database->bind("userid", $userid);
                $this->database->bind("imgid", $photo['imgid']);
                $status = $this->database->firstColumn();
                if ($status == 1) {
                    $query = "delete from imagefav where imgid=:imgid and userid=:userid";
                    $this->database->query($query);
                    $this->database->bind("userid", $userid);
                    $this->database->bind("imgid", $photo['imgid']);
                    $status = $this->database->execute();
                    if ($status == 1) {
                        $result = ["code" => 1, "message" => "."];
                    } else {
                        $result = ["code" => 104, "message" => "Unknown Error."];
                    }
                } else {
                    $result = ["code" => 105, "message" => ""];
                }
            } else {
                $result = ["code" => 106, "message" => "Photo Not Found."];
            }
        }
        echo json_encode($result);
    }

    public function info() {
        $photo = isset($_POST['photo']) ? json_decode($_POST['photo']) : "";
        $photo = (array) $photo;
        $result = ["code" => 100, "message" => "Unknown Error."];
        if (empty($photo)) {
            $result = ["code" => 102, "message" => "Unknown Error.<br/>Refresh Page."];
        } else if (empty($photo['imgid'])) {
            $result = ["code" => 103, "message" => "Refresh Page."];
        } else if (!is_numeric($photo['imgid'])) {
            $result = ["code" => 104, "message" => "Unknown Photo."];
        } else {
            $query = "select count(*) from images where imgid=:imgid and enable=0";
            $this->database->query($query);
            $this->database->bind("imgid", $photo['imgid']);
            $status = $this->database->firstColumn();
            if ($status == 1) {
                $userid = -1;
                if (SessionManagement::sessionExists("userid")) {
                    $userid = SessionManagement::getSession("userid");
                }
                $query = "select  view  as viewount,title as title,url as url,albumid as albumid,"
                        . "(select userid from usersignup where usersignup.userid=images.userid) as userid,"
                        . "(select username from usersignup where usersignup.userid=images.userid) as username,"
                        . "(select img from usersignup where usersignup.userid=images.userid) as user_icon,"
                        . "(select album from album where album.albumid=images.albumid) as albumname, "
                        . "(select url from album where album.albumid=images.albumid) as albumurl, "
                        . "(select count(*) from imagelike where imgid=:imgid) as likecount,"
                        . "(select count(*) from imagefav where imgid=:imgid) as favount "
                        . "from images where imgid=:imgid and enable=0";
                $this->database->query($query);
                $this->database->bind("imgid", $photo['imgid']);
                $info = $this->database->single();
                $result = ["code" => 1];
                $result['title'] = $info['title'];
                $result['url'] = $info['url'];
                $result['username'] = $info['username'];
                $result['userid'] = $info['userid'];
                $result['usericon'] = $info['user_icon'];
                $result['albumname'] = $info['albumname'];
                $result['albumurl'] = $info['albumurl'];
                $result['albumid'] = $info['albumid'];
                $result['viewcount'] = $info['viewount'];
                $result['likecount'] = $info['likecount'];
                $result['favcount'] = $info['favount'];

                list($width, $height, $type, $attr) = getimagesize("images/original/" . $photo['img']);

                $size = filesize("images/original/" . $photo['img']);
                $displaySize = $size . " Byte";
                if ($size > 1024) {
                    $displaySize = round(($size / 1024)) . " KB";
                }
                if (($size / 1024) > 1024) {
                    $displaySize = round(($size / 1024) / 1024) . " MB";
                }


                $result['size'] = $displaySize;
                $result['width'] = $width;
                $result['height'] = $height;
                $result['type'] = $type;
                $result['attr'] = $attr;



                $query = "select count(*) from imagefav where imgid=:imgid and userid=:userid";
                $this->database->query($query);
                $this->database->bind("userid", $userid);
                $this->database->bind("imgid", $photo['imgid']);
                $status = $this->database->firstColumn();
                $result['userfav'] = $status;


                $query = "select count(*) from imagelike where imgid=:imgid and userid=:userid";
                $this->database->query($query);
                $this->database->bind("userid", $userid);
                $this->database->bind("imgid", $photo['imgid']);
                $status = $this->database->firstColumn();
                $result['userlike'] = $status;

                $query = "update images set view=view + 1 where imgid=:imgid";
                $this->database->query($query);
                $this->database->bind("imgid", $info['imgid']);
                $this->database->execute();

                $query_image = "select meta_key,meta_value from image_meta where imgid=:imgid";
                $this->database->query($query_image);
                $this->database->bind("imgid", $photo['imgid']);
                $result['extra'] = $this->database->resultSet();
                $result['imgid'] = $photo['imgid'];

//
//                $albumcondition = "";
//                $photocondition = "";
//                $likecondition = "";
//                $favcondition = "";
//                $hashcondition = "";
//                $editorcondition = "";
//                $searchcondition = "";
//                $searchselect = "";
//                $selectlike = "";
//                $selectfav = "";
//                $page = $photo['page'];
//                if ($page == "album") {
//                    $albumcondition = " and albumid=" . $result['albumid'] . " ";
//                } else if ($page == "user_photo") {
//                    $photocondition = " and userid=" . $result['userid'] . " ";
//                } else if ($page == "user_like") {
//                    $likecondition = " and imgid in (select imgid from imagelike where userid=" . $photo['user_profile'] . ") ";
//                    $selectlike = " ," . $photo['user_profile'] . " as user_profile ";
//                } else if ($page == "user_favorite") {
//                    $favcondition = " and imgid in (select imgid from imagefav where userid=" . $photo['user_profile'] . ") ";
//                    $selectfav = " ," . $photo['user_profile'] . " as user_profile ";
//                } else if ($page == "hash") {
//                    $hashcondition = " and imgid in (select imgid from hash where hash.hash in (select hash from hash where imgid=:imgid)) ";
//                } else if ($page == "editor") {
//                    $editorcondition = " and editors_pick!=0 ";
//                } else if ($page == "search_photo") {
//                    $searchselect = ",'" . $photo['search_query'] . "' as search_query ";
//                    $searchcondition = " and title like :title ";
//                }
//                $query = " select '" . $page . "' as page " . $searchselect . $selectlike . $selectfav . " , imgid,img,title,(select count(*) from imagelike where imagelike.imgid=images.imgid and userid=:userid) as likecount,(select count(*) from imagefav where imagefav.imgid=images.imgid and userid=:userid) as favcount
//                  from images where imgid<:imgid " . $albumcondition . $photocondition . $likecondition . $favcondition . $hashcondition . $editorcondition . $searchcondition . "  and enable=0 order by imgid desc limit 1";
//                $this->database->query($query);
//                $this->database->bind("userid", $userid);
//                $this->database->bind("imgid", $photo['imgid']);
//                if ($page == "search_photo") {
//                    $this->database->bind("title", "%" . $photo['search_query'] . "%");
//                }
//                $result['next'] = $this->database->single();
//
//
//                $query = " select '" . $page . "' as page " . $searchselect . $selectlike . $selectfav . ", imgid,img,title,(select count(*) from imagelike where imagelike.imgid=images.imgid and userid=:userid) as likecount,(select count(*) from imagefav where imagefav.imgid=images.imgid and userid=:userid) as favcount
//                  from images where imgid>:imgid " . $albumcondition . $photocondition . $likecondition . $favcondition . $hashcondition . $editorcondition . $searchcondition . "  and enable=0 order by imgid asc limit 1";
//                $this->database->query($query);
//                $this->database->bind("userid", $userid);
//                if ($page == "search_photo") {
//                    $this->database->bind("title", "%" . $photo['search_query'] . "%");
//                }
//                $this->database->bind("imgid", $photo['imgid']);
//
//                $result['previous'] = $this->database->single();
            } else {
                $result = ["code" => 106, "message" => "Photo Not Found."];
            }
        }
        echo json_encode($result);
    }

}
