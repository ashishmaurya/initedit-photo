<?php

include_once "php/class/query.php";
include_once "php/class/captcha.php";
include_once "php/class/images.php";

$p = $_SERVER['REQUEST_URI'];
$p = str_replace("http://", "", $p);
$pArray = explode("/", $p);

if (@$pArray[2] == 1) {
    if (!isset($pArray[3]) || $pArray[3] == "" || is_numeric($pArray[3]) || $pArray[3] == "h") {
        $PAGE = "HOME";
    } else if ($pArray[3] == "p") {
        $PAGE = "PROFILE";
    } else if ($pArray[3] == "a") {
        $PAGE = "ALBUM";
    } else if ($pArray[3] == "u") {
        $PAGE = "USER";
    }else if ($pArray[3] == "s") {
        $PAGE = "SEARCH";
    }else if ($pArray[3] == "e") {
        $PAGE = "EDITOR";
    }
    $POST_PER_PAGE = 10;



    if ($PAGE == "HOME") {
        $PAGE_NO = isset($pArray[3]) ? (is_numeric($pArray[3])) ? $pArray[3] : ($pArray[3] == "h") ? (isset($pArray[4])) ? (is_numeric($pArray[4])) ? $pArray[4] : 1 : 1 : 1  : 1;
        $START_FROM = $POST_PER_PAGE * ($PAGE_NO - 1);
        $myArray = array();
        $result = QUERY::query("select  concat('http://photo.initedit.com/uploads/thumb/',img) as thumbUrl
,concat('http://photo.initedit.com/uploads/original/',img) as originalUrl
,(select username from usersignup where usersignup.userid=images.userid) as user
,concat('http://photo.initedit.com/uploads/profile/thumb/',(select img from usersignup where usersignup.userid=images.userid)) as user_img
,concat('http://photo.initedit.com/uploads/original/',(select cover from usersignup where usersignup.userid=images.userid)) as user_cover
,(select album from album where album.albumid=images.albumid) as album
,UNIX_TIMESTAMP(time) as time
,img as img
,view as view_count
,(select count(*) from imagelike where imagelike.imgid=images.imgid) as like_count
,(select count(*) from imagefav where imagefav.imgid=images.imgid) as fav_count
from images where privacy=0 order by time desc limit $START_FROM,$POST_PER_PAGE");
        if (mysqli_num_rows($result) == 0) {
            $myArray = array();
            $myNewArray = array();
            $myNewArray['code'] = 404;
            $myNewArray['message'] = "Page Not Found";
            $myArray[] = $myNewArray;
            echo json_encode(array("error" => $myArray));
            exit;
        }
        while ($row = $result->fetch_array(MYSQL_ASSOC)) {
            $photo = $row['img'];
            $image_properties = exif_read_data("./uploads/original/$photo", 0, true);

            $WIDTH = "0";
            $HEIGHT = "0";
            $TYPE = "";
            $SIZE = "";
            $TITLE = "";
            $IMG_NAME = "";
            $CAMERA = "";
            $SOFTWARE = "";
            $TIME = "";
            $TITLE = QUERY::c("select title from images where img='{$photo}'");
            $IMG_NAME = QUERY::c("select name from images where img='{$photo}'");

            foreach ($image_properties as $key => $section) {
                foreach ($section as $name => $val) {
                    if (strtolower($name) == "filename") {
                        //  echo "<tr><td> Name </td><td>$val</td></tr>";
                    } else if (strtolower($name) == "filesize") {
                        $SIZE = ceil($val / 1024) . "KB";
                    } else if (strtolower($name) == "filedatetime") {
                        $TIME = date("Y-m-d", $val);
                    } else if (strtolower($name) == "software") {
                        $SOFTWARE = $val;
                    } else if (strtolower($name) == "mimetype") {
                        $TYPE = $val;
                    } else if (strtolower($name) == "width") {
                        $WIDTH = $val;
                    } else if (strtolower($name) == "height") {
                        $HEIGHT = $val;
                    } else if (strtolower($name) == "model") {
                        $CAMERA = $val;
                    }
                }
            }
            $detailArray = array("width" => $WIDTH, "height" => $HEIGHT, "type" => $TYPE,
                "size" => $SIZE, "title" => $TITLE, "name" => $IMG_NAME, "camera" => $CAMERA, "software" => $SOFTWARE, "time" => $TIME);
            $row['detail'] = $detailArray;

            $myArray[] = $row;
        }
        echo json_encode(array("images" => $myArray));
    } else if ($PAGE == "PROFILE") 
        {
        $PROFILE_NAME = isset($pArray[4]) ? $pArray[4] : "";
        if (QUERY::c("select count(*) from usersignup where username='{$PROFILE_NAME}'") == "0") {
            $myArray = array();
            $myNewArray = array();
            $myNewArray['code'] = 404;
            $myNewArray['message'] = "User Not Found";
            $myArray[] = $myNewArray;
            echo json_encode(array("error" => $myArray));
            exit;
        }
        $PROFILE_USERID = QUERY::c("select userid from usersignup where username='{$PROFILE_NAME}'");
        $PROFILE_OP = isset($pArray[5]) ? $pArray[5] : "";

        if ($PROFILE_OP == "") {
            //To Make it consistant with other
            $myArray = array();
            $result = QUERY::query("select username as name,
            concat('http://photo.initedit.com/uploads/profile/thumb/',img) as img,
            concat('http://photo.initedit.com/uploads/original/',cover) as coverImg,
            (select count(*) from images where images.userid=usersignup.userid) as photos,
            (select count(*) from album where album.userid=usersignup.userid) as albums
             from usersignup where username='{$PROFILE_NAME}'");
            while ($row = $result->fetch_array(MYSQL_ASSOC)) {
                $myArray["profile"] = $row;
            }
            $albumArray = array();
            $result = QUERY::query("select album from album where userid=(select userid from usersignup where username='{$PROFILE_NAME}')");
            while ($row = $result->fetch_array(MYSQL_ASSOC)) {
                $albumArray[] = $row;
            }
            $myArray["albumlist"] = $albumArray;
            echo json_encode($myArray);
        } else if ($PROFILE_OP == "photo") {
            $PAGE_NO = isset($pArray[6]) ? (is_numeric($pArray[6])) ? $pArray[6] : 1 : 1;
            $START_FROM = $POST_PER_PAGE * ($PAGE_NO - 1);
            $myArray = array();
            $result = QUERY::query("select  concat('http://photo.initedit.com/uploads/thumb/',img) as thumbUrl
,concat('http://photo.initedit.com/uploads/original/',img) as originalUrl
,(select username from usersignup where usersignup.userid=images.userid) as user
,concat('http://photo.initedit.com/uploads/profile/thumb/',(select img from usersignup where usersignup.userid=images.userid)) as user_img
,concat('http://photo.initedit.com/uploads/original/',(select cover from usersignup where usersignup.userid=images.userid)) as user_cover
,(select album from album where album.albumid=images.albumid) as album
,UNIX_TIMESTAMP(time) as time
,img as img
,view as view_count
,(select count(*) from imagelike where imagelike.imgid=images.imgid) as like_count
,(select count(*) from imagefav where imagefav.imgid=images.imgid) as fav_count
from images where privacy=0 and  userid=$PROFILE_USERID order by time desc limit $START_FROM,$POST_PER_PAGE");
            if (mysqli_num_rows($result) == 0) {
                $myArray = array();
                $myNewArray = array();
                $myNewArray['code'] = 404;
                $myNewArray['message'] = "Page Not Found";
                $myArray[] = $myNewArray;
                echo json_encode(array("error" => $myArray));
                exit;
            }
            while ($row = $result->fetch_array(MYSQL_ASSOC)) {
                $photo = $row['img'];
                $image_properties = exif_read_data("./uploads/original/$photo", 0, true);

                $WIDTH = "0";
                $HEIGHT = "0";
                $TYPE = "";
                $SIZE = "";
                $TITLE = "";
                $IMG_NAME = "";
                $CAMERA = "";
                $SOFTWARE = "";
                $TIME = "";
                $TITLE = QUERY::c("select title from images where img='{$photo}'");
                $IMG_NAME = QUERY::c("select name from images where img='{$photo}'");

                foreach ($image_properties as $key => $section) {
                    foreach ($section as $name => $val) {
                        if (strtolower($name) == "filename") {
                            //  echo "<tr><td> Name </td><td>$val</td></tr>";
                        } else if (strtolower($name) == "filesize") {
                            $SIZE = ceil($val / 1024) . "KB";
                        } else if (strtolower($name) == "filedatetime") {
                            $TIME = date("Y-m-d", $val);
                        } else if (strtolower($name) == "software") {
                            $SOFTWARE = $val;
                        } else if (strtolower($name) == "mimetype") {
                            $TYPE = $val;
                        } else if (strtolower($name) == "width") {
                            $WIDTH = $val;
                        } else if (strtolower($name) == "height") {
                            $HEIGHT = $val;
                        } else if (strtolower($name) == "model") {
                            $CAMERA = $val;
                        }
                    }
                }
                $detailArray = array("width" => $WIDTH, "height" => $HEIGHT, "type" => $TYPE,
                    "size" => $SIZE, "title" => $TITLE, "name" => $IMG_NAME, "camera" => $CAMERA, "software" => $SOFTWARE, "time" => $TIME);
                $row['detail'] = $detailArray;

                $myArray[] = $row;
            }
            echo json_encode(array("images" => $myArray));
        } else if ($PROFILE_OP == "albumlist") {
            $albumArray = array();
            $result = QUERY::query("select album,UNIX_TIMESTAMP(time) as time,'{$PROFILE_NAME}' as user from album where userid=(select userid from usersignup where userid=$PROFILE_USERID)");
            while ($row = $result->fetch_array(MYSQL_ASSOC)) {
                $albumArray[] = $row;
            }
            $myArray["albumlist"] = $albumArray;
            echo json_encode($myArray);
        } else if ($PROFILE_OP == "album") {
            $PAGE_NO = isset($pArray[7]) ? (is_numeric($pArray[7])) ? $pArray[7] : 1 : 1;
            $START_FROM = $POST_PER_PAGE * ($PAGE_NO - 1);
            $USER_NAME = isset($pArray[4]) ? $pArray[4] : "";
            $ALBUM_NAME = isset($pArray[6]) ? $pArray[6] : "";
            $USER_ID = QUERY::c("select userid from usersignup where username='{$USER_NAME}'");
            $ALBUM_ID = QUERY::c("select albumid from album where album='{$ALBUM_NAME}' and userid=$USER_ID");
            if ($ALBUM_ID == "" || $USER_ID == "") {
                $myArray = array();
                $myNewArray = array();
                $myNewArray['code'] = 404;
                $myNewArray['message'] = "Album Not Found";
                $myArray[] = $myNewArray;
                echo json_encode(array("error" => $myArray));
                exit;
            }
            $myArray = array();
            $result = QUERY::query("select  concat('http://photo.initedit.com/uploads/thumb/',img) as thumbUrl
,concat('http://photo.initedit.com/uploads/original/',img) as originalUrl
,(select username from usersignup where usersignup.userid=images.userid) as user
,concat('http://photo.initedit.com/uploads/profile/thumb/',(select img from usersignup where usersignup.userid=images.userid)) as user_img
,concat('http://photo.initedit.com/uploads/original/',(select cover from usersignup where usersignup.userid=images.userid)) as user_cover
,(select album from album where album.albumid=images.albumid) as album
,UNIX_TIMESTAMP(time) as time
,img as img
,view as view_count
,(select count(*) from imagelike where imagelike.imgid=images.imgid) as like_count
,(select count(*) from imagefav where imagefav.imgid=images.imgid) as fav_count
from images where images.privacy=0 and images.userid=$PROFILE_USERID and images.albumid=$ALBUM_ID order by time desc limit $START_FROM,$POST_PER_PAGE");
            while ($row = $result->fetch_array(MYSQL_ASSOC)) {
                $photo = $row['img'];
                $image_properties = exif_read_data("./uploads/original/$photo", 0, true);

                $WIDTH = "0";
                $HEIGHT = "0";
                $TYPE = "";
                $SIZE = "";
                $TITLE = "";
                $IMG_NAME = "";
                $CAMERA = "";
                $SOFTWARE = "";
                $TIME = "";
                $TITLE = QUERY::c("select title from images where img='{$photo}'");
                $IMG_NAME = QUERY::c("select name from images where img='{$photo}'");

                foreach ($image_properties as $key => $section) {
                    foreach ($section as $name => $val) {
                        if (strtolower($name) == "filename") {
                            //  echo "<tr><td> Name </td><td>$val</td></tr>";
                        } else if (strtolower($name) == "filesize") {
                            $SIZE = ceil($val / 1024) . "KB";
                        } else if (strtolower($name) == "filedatetime") {
                            $TIME = date("Y-m-d", $val);
                        } else if (strtolower($name) == "software") {
                            $SOFTWARE = $val;
                        } else if (strtolower($name) == "mimetype") {
                            $TYPE = $val;
                        } else if (strtolower($name) == "width") {
                            $WIDTH = $val;
                        } else if (strtolower($name) == "height") {
                            $HEIGHT = $val;
                        } else if (strtolower($name) == "model") {
                            $CAMERA = $val;
                        }
                    }
                }
                $detailArray = array("width" => $WIDTH, "height" => $HEIGHT, "type" => $TYPE,
                    "size" => $SIZE, "title" => $TITLE, "name" => $IMG_NAME, "camera" => $CAMERA, "software" => $SOFTWARE, "time" => $TIME);
                $row['detail'] = $detailArray;

                $myArray[] = $row;
            }
            echo json_encode(array("images" => $myArray));
            ;
        } else if ($PROFILE_OP == "like") {
             $PAGE_NO = isset($pArray[6]) ? (is_numeric($pArray[6])) ? $pArray[6] : 1 : 1;
            $START_FROM = $POST_PER_PAGE * ($PAGE_NO - 1);
            $myArray = array();
            $result = QUERY::query("select  concat('http://photo.initedit.com/uploads/thumb/',img) as thumbUrl
,concat('http://photo.initedit.com/uploads/original/',img) as originalUrl
,(select username from usersignup where usersignup.userid=images.userid) as user
,concat('http://photo.initedit.com/uploads/profile/thumb/',(select img from usersignup where usersignup.userid=images.userid)) as user_img
,concat('http://photo.initedit.com/uploads/original/',(select cover from usersignup where usersignup.userid=images.userid)) as user_cover
,(select album from album where album.albumid=images.albumid) as album
,UNIX_TIMESTAMP(time) as time
,img as img
,view as view_count
,(select count(*) from imagelike where imagelike.imgid=images.imgid) as like_count
,(select count(*) from imagefav where imagefav.imgid=images.imgid) as fav_count
from images where privacy=0 and imgid in (select imgid from imagelike where userid=$PROFILE_USERID) order by time desc limit $START_FROM,$POST_PER_PAGE");
            if (mysqli_num_rows($result) == 0) {
                $myArray = array();
                $myNewArray = array();
                $myNewArray['code'] = 404;
                $myNewArray['message'] = "Page Not Found";
                $myArray[] = $myNewArray;
                echo json_encode(array("error" => $myArray));
                exit;
            }
            while ($row = $result->fetch_array(MYSQL_ASSOC)) {
                $photo = $row['img'];
                $image_properties = exif_read_data("./uploads/original/$photo", 0, true);

                $WIDTH = "0";
                $HEIGHT = "0";
                $TYPE = "";
                $SIZE = "";
                $TITLE = "";
                $IMG_NAME = "";
                $CAMERA = "";
                $SOFTWARE = "";
                $TIME = "";
                $TITLE = QUERY::c("select title from images where img='{$photo}'");
                $IMG_NAME = QUERY::c("select name from images where img='{$photo}'");

                foreach ($image_properties as $key => $section) {
                    foreach ($section as $name => $val) {
                        if (strtolower($name) == "filename") {
                            //  echo "<tr><td> Name </td><td>$val</td></tr>";
                        } else if (strtolower($name) == "filesize") {
                            $SIZE = ceil($val / 1024) . "KB";
                        } else if (strtolower($name) == "filedatetime") {
                            $TIME = date("Y-m-d", $val);
                        } else if (strtolower($name) == "software") {
                            $SOFTWARE = $val;
                        } else if (strtolower($name) == "mimetype") {
                            $TYPE = $val;
                        } else if (strtolower($name) == "width") {
                            $WIDTH = $val;
                        } else if (strtolower($name) == "height") {
                            $HEIGHT = $val;
                        } else if (strtolower($name) == "model") {
                            $CAMERA = $val;
                        }
                    }
                }
                $detailArray = array("width" => $WIDTH, "height" => $HEIGHT, "type" => $TYPE,
                    "size" => $SIZE, "title" => $TITLE, "name" => $IMG_NAME, "camera" => $CAMERA, "software" => $SOFTWARE, "time" => $TIME);
                $row['detail'] = $detailArray;

                $myArray[] = $row;
            }
            echo json_encode(array("images" => $myArray));
        }
    }else if ($PAGE == "EDITOR") {
        $PAGE_NO = isset($pArray[4]) ? (is_numeric($pArray[4])) ? $pArray[4] : 1 : 1;
        $START_FROM = $POST_PER_PAGE * ($PAGE_NO - 1);
        $myArray = array();
        $result = QUERY::query("select  concat('http://photo.initedit.com/uploads/thumb/',img) as thumbUrl
,concat('http://photo.initedit.com/uploads/original/',img) as originalUrl
,(select username from usersignup where usersignup.userid=images.userid) as user
,concat('http://photo.initedit.com/uploads/profile/thumb/',(select img from usersignup where usersignup.userid=images.userid)) as user_img
,concat('http://photo.initedit.com/uploads/original/',(select cover from usersignup where usersignup.userid=images.userid)) as user_cover
,(select album from album where album.albumid=images.albumid) as album
,UNIX_TIMESTAMP(time) as time
,img as img
,view as view_count
,(select count(*) from imagelike where imagelike.imgid=images.imgid) as like_count
,(select count(*) from imagefav where imagefav.imgid=images.imgid) as fav_count
from images where privacy=0 and editors_pick=1 order by time desc limit $START_FROM,$POST_PER_PAGE");
        if (mysqli_num_rows($result) == 0) {
            $myArray = array();
            $myNewArray = array();
            $myNewArray['code'] = 404;
            $myNewArray['message'] = "Page Not Found";
            $myArray[] = $myNewArray;
            echo json_encode(array("error" => $myArray));
            exit;
        }
        while ($row = $result->fetch_array(MYSQL_ASSOC)) {
            $photo = $row['img'];
            $image_properties = exif_read_data("./uploads/original/$photo", 0, true);

            $WIDTH = "0";
            $HEIGHT = "0";
            $TYPE = "";
            $SIZE = "";
            $TITLE = "";
            $IMG_NAME = "";
            $CAMERA = "";
            $SOFTWARE = "";
            $TIME = "";
            $TITLE = QUERY::c("select title from images where img='{$photo}'");
            $IMG_NAME = QUERY::c("select name from images where img='{$photo}'");

            foreach ($image_properties as $key => $section) {
                foreach ($section as $name => $val) {
                    if (strtolower($name) == "filename") {
                        //  echo "<tr><td> Name </td><td>$val</td></tr>";
                    } else if (strtolower($name) == "filesize") {
                        $SIZE = ceil($val / 1024) . "KB";
                    } else if (strtolower($name) == "filedatetime") {
                        $TIME = date("Y-m-d", $val);
                    } else if (strtolower($name) == "software") {
                        $SOFTWARE = $val;
                    } else if (strtolower($name) == "mimetype") {
                        $TYPE = $val;
                    } else if (strtolower($name) == "width") {
                        $WIDTH = $val;
                    } else if (strtolower($name) == "height") {
                        $HEIGHT = $val;
                    } else if (strtolower($name) == "model") {
                        $CAMERA = $val;
                    }
                }
            }
            $detailArray = array("width" => $WIDTH, "height" => $HEIGHT, "type" => $TYPE,
                "size" => $SIZE, "title" => $TITLE, "name" => $IMG_NAME, "camera" => $CAMERA, "software" => $SOFTWARE, "time" => $TIME);
            $row['detail'] = $detailArray;

            $myArray[] = $row;
        }
        echo json_encode(array("images" => $myArray));
    }else if ($PAGE == "SEARCH") {
        $PAGE_NO = isset($pArray[5]) ? (is_numeric($pArray[5])) ? $pArray[5] : 1 : 1;
        $SEARCH = isset($pArray[4]) ?$pArray[4]:"";
        $START_FROM = $POST_PER_PAGE * ($PAGE_NO - 1);
        $myArray = array();
        $result = QUERY::query("select  concat('http://photo.initedit.com/uploads/thumb/',img) as thumbUrl
,concat('http://photo.initedit.com/uploads/original/',img) as originalUrl
,(select username from usersignup where usersignup.userid=images.userid) as user
,concat('http://photo.initedit.com/uploads/profile/thumb/',(select img from usersignup where usersignup.userid=images.userid)) as user_img
,concat('http://photo.initedit.com/uploads/original/',(select cover from usersignup where usersignup.userid=images.userid)) as user_cover
,(select album from album where album.albumid=images.albumid) as album
,UNIX_TIMESTAMP(time) as time
,img as img
,view as view_count
,(select count(*) from imagelike where imagelike.imgid=images.imgid) as like_count
,(select count(*) from imagefav where imagefav.imgid=images.imgid) as fav_count
from images where privacy=0 and title like '%{$SEARCH}%' order by time desc limit $START_FROM,$POST_PER_PAGE");
        /*echo "select  concat('http://photo.initedit.com/uploads/thumb/',img) as thumbUrl
,concat('http://photo.initedit.com/uploads/original/',img) as originalUrl
,(select username from usersignup where usersignup.userid=images.userid) as user
,concat('http://photo.initedit.com/uploads/profile/thumb/',(select img from usersignup where usersignup.userid=images.userid)) as user_img
,concat('http://photo.initedit.com/uploads/original/',(select cover from usersignup where usersignup.userid=images.userid)) as user_cover
,(select album from album where album.albumid=images.albumid) as album
,UNIX_TIMESTAMP(time) as time
,img as img
,view as view_count
,(select count(*) from imagelike where imagelike.imgid=images.imgid) as like_count
,(select count(*) from imagefav where imagefav.imgid=images.imgid) as fav_count
from images where privacy=0 and title like '%{$SEARCH}%' order by time desc limit $START_FROM,$POST_PER_PAGE";
        */
         if (mysqli_num_rows($result) == 0) {
         
            $myArray = array();
            $myNewArray = array();
            $myNewArray['code'] = 404;
            $myNewArray['message'] = "Page Not Found";
            $myArray[] = $myNewArray;
            echo json_encode(array("error" => $myArray));
            exit;
        }
        while ($row = $result->fetch_array(MYSQL_ASSOC)) {
            $photo = $row['img'];
            $image_properties = exif_read_data("./uploads/original/$photo", 0, true);

            $WIDTH = "0";
            $HEIGHT = "0";
            $TYPE = "";
            $SIZE = "";
            $TITLE = "";
            $IMG_NAME = "";
            $CAMERA = "";
            $SOFTWARE = "";
            $TIME = "";
            $TITLE = QUERY::c("select title from images where img='{$photo}'");
            $IMG_NAME = QUERY::c("select name from images where img='{$photo}'");

            foreach ($image_properties as $key => $section) {
                foreach ($section as $name => $val) {
                    if (strtolower($name) == "filename") {
                        //  echo "<tr><td> Name </td><td>$val</td></tr>";
                    } else if (strtolower($name) == "filesize") {
                        $SIZE = ceil($val / 1024) . "KB";
                    } else if (strtolower($name) == "filedatetime") {
                        $TIME = date("Y-m-d", $val);
                    } else if (strtolower($name) == "software") {
                        $SOFTWARE = $val;
                    } else if (strtolower($name) == "mimetype") {
                        $TYPE = $val;
                    } else if (strtolower($name) == "width") {
                        $WIDTH = $val;
                    } else if (strtolower($name) == "height") {
                        $HEIGHT = $val;
                    } else if (strtolower($name) == "model") {
                        $CAMERA = $val;
                    }
                }
            }
            $detailArray = array("width" => $WIDTH, "height" => $HEIGHT, "type" => $TYPE,
                "size" => $SIZE, "title" => $TITLE, "name" => $IMG_NAME, "camera" => $CAMERA, "software" => $SOFTWARE, "time" => $TIME);
            $row['detail'] = $detailArray;

            $myArray[] = $row;
        }
        echo json_encode(array("images" => $myArray));
    } else if ($PAGE == "ALBUM") {
        $PAGE_NO = isset($pArray[6]) ? (is_numeric($pArray[6])) ? $pArray[6] : 1 : 1;
        $START_FROM = $POST_PER_PAGE * ($PAGE_NO - 1);
        $USER_NAME = isset($pArray[4]) ? $pArray[4] : "";
        $ALBUM_NAME = isset($pArray[5]) ? $pArray[5] : "";
        $USER_ID = QUERY::c("select userid from usersignup where username='{$USER_NAME}'");
        $ALBUM_ID = QUERY::c("select albumid from album where album='{$ALBUM_NAME}'");
        if ($ALBUM_ID == "" || $USER_ID == "") {
            $myArray = array();
            $myNewArray = array();
            $myNewArray['code'] = 404;
            $myNewArray['message'] = "Album Not Found";
            $myArray[] = $myNewArray;
            echo json_encode(array("error" => $myArray));
            exit;
        }
        $myArray = array();
        $result = QUERY::query("select  concat('http://photo.initedit.com/uploads/thumb/',img) as thumbUrl
,concat('http://photo.initedit.com/uploads/original/',img) as originalUrl
,(select username from usersignup where usersignup.userid=images.userid) as user
,concat('http://photo.initedit.com/uploads/profile/thumb/',(select img from usersignup where usersignup.userid=images.userid)) as user_img
,concat('http://photo.initedit.com/uploads/original/',(select cover from usersignup where usersignup.userid=images.userid)) as user_cover
,(select album from album where album.albumid=images.albumid) as album
,UNIX_TIMESTAMP(time) as time
,img as img
,view as view_count
,(select count(*) from imagelike where imagelike.imgid=images.imgid) as like_count
,(select count(*) from imagefav where imagefav.imgid=images.imgid) as fav_count
from images where images.privacy=0 and images.userid=$USER_ID and images.albumid=$ALBUM_ID order by time desc limit $START_FROM,$POST_PER_PAGE");
        while ($row = $result->fetch_array(MYSQL_ASSOC)) {
            $photo = $row['img'];
            $image_properties = exif_read_data("./uploads/original/$photo", 0, true);

            $WIDTH = "0";
            $HEIGHT = "0";
            $TYPE = "";
            $SIZE = "";
            $TITLE = "";
            $IMG_NAME = "";
            $CAMERA = "";
            $SOFTWARE = "";
            $TIME = "";
            $TITLE = QUERY::c("select title from images where img='{$photo}'");
            $IMG_NAME = QUERY::c("select name from images where img='{$photo}'");

            foreach ($image_properties as $key => $section) {
                foreach ($section as $name => $val) {
                    if (strtolower($name) == "filename") {
                        //  echo "<tr><td> Name </td><td>$val</td></tr>";
                    } else if (strtolower($name) == "filesize") {
                        $SIZE = ceil($val / 1024) . "KB";
                    } else if (strtolower($name) == "filedatetime") {
                        $TIME = date("Y-m-d", $val);
                    } else if (strtolower($name) == "software") {
                        $SOFTWARE = $val;
                    } else if (strtolower($name) == "mimetype") {
                        $TYPE = $val;
                    } else if (strtolower($name) == "width") {
                        $WIDTH = $val;
                    } else if (strtolower($name) == "height") {
                        $HEIGHT = $val;
                    } else if (strtolower($name) == "model") {
                        $CAMERA = $val;
                    }
                }
            }
            $detailArray = array("width" => $WIDTH, "height" => $HEIGHT, "type" => $TYPE,
                "size" => $SIZE, "title" => $TITLE, "name" => $IMG_NAME, "camera" => $CAMERA, "software" => $SOFTWARE, "time" => $TIME);
            $row['detail'] = $detailArray;

            $myArray[] = $row;
        }
        echo json_encode(array("images" => $myArray));
    } else if ($PAGE == "USER") {
        $PAGE_NO = isset($pArray[5]) ? (is_numeric($pArray[5])) ? $pArray[5] : 1 : 1;
        $START_FROM = $POST_PER_PAGE * ($PAGE_NO - 1);
        $USER_NAME = isset($pArray[4]) ? $pArray[4] : "";
        $USER_ID = QUERY::c("select userid from usersignup where username='{$USER_NAME}'");
        if ($USER_ID == "") {
            $myArray = array();
            $myNewArray = array();
            $myNewArray['code'] = 404;
            $myNewArray['message'] = "User Not Found";
            $myArray[] = $myNewArray;
            echo json_encode(array("error" => $myArray));
            exit;
        }
        $myArray = array();
        $result = QUERY::query("select  concat('http://photo.initedit.com/uploads/thumb/',img) as thumbUrl
,concat('http://photo.initedit.com/uploads/original/',img) as originalUrl
,(select username from usersignup where usersignup.userid=images.userid) as user
,concat('http://photo.initedit.com/uploads/profile/thumb/',(select img from usersignup where usersignup.userid=images.userid)) as user_img
,concat('http://photo.initedit.com/uploads/original/',(select cover from usersignup where usersignup.userid=images.userid)) as user_cover
,(select album from album where album.albumid=images.albumid) as album
,UNIX_TIMESTAMP(time) as time
,img as img
,view as view_count
,(select count(*) from imagelike where imagelike.imgid=images.imgid) as like_count
,(select count(*) from imagefav where imagefav.imgid=images.imgid) as fav_count
from images where images.privacy=0 and images.userid=$USER_ID order by time desc limit $START_FROM,$POST_PER_PAGE");
        while ($row = $result->fetch_array(MYSQL_ASSOC)) {
            $photo = $row['img'];
            $image_properties = exif_read_data("./uploads/original/$photo", 0, true);

            $WIDTH = "0";
            $HEIGHT = "0";
            $TYPE = "";
            $SIZE = "";
            $TITLE = "";
            $IMG_NAME = "";
            $CAMERA = "";
            $SOFTWARE = "";
            $TIME = "";
            $TITLE = QUERY::c("select title from images where img='{$photo}'");
            $IMG_NAME = QUERY::c("select name from images where img='{$photo}'");

            foreach ($image_properties as $key => $section) {
                foreach ($section as $name => $val) {
                    if (strtolower($name) == "filename") {
                        //  echo "<tr><td> Name </td><td>$val</td></tr>";
                    } else if (strtolower($name) == "filesize") {
                        $SIZE = ceil($val / 1024) . "KB";
                    } else if (strtolower($name) == "filedatetime") {
                        $TIME = date("Y-m-d", $val);
                    } else if (strtolower($name) == "software") {
                        $SOFTWARE = $val;
                    } else if (strtolower($name) == "mimetype") {
                        $TYPE = $val;
                    } else if (strtolower($name) == "width") {
                        $WIDTH = $val;
                    } else if (strtolower($name) == "height") {
                        $HEIGHT = $val;
                    } else if (strtolower($name) == "model") {
                        $CAMERA = $val;
                    }
                }
            }
            $detailArray = array("width" => $WIDTH, "height" => $HEIGHT, "type" => $TYPE,
                "size" => $SIZE, "title" => $TITLE, "name" => $IMG_NAME, "camera" => $CAMERA, "software" => $SOFTWARE, "time" => $TIME);
            $row['detail'] = $detailArray;

            $myArray[] = $row;
        }
        echo json_encode(array("images" => $myArray));
    }
}
?>