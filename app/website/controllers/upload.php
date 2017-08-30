<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of upload
 *
 * @author home
 */
class upload extends Controller {

    public $allowedExts = array("gif", "jpeg", "jpg", "png", "svg");
    public $allowedCompression = array(2, 3, 6);

    public function index() {
        
        if(!SessionManagement::sessionExists("userid")){
            header("Location: /account/login?r=/upload");
        }
        
        
        
        $this->view("common/head", ["title" => "Upload - Initedit Photo", "description" => "Main Page"]);
        $this->view("common/header", ["isloggedin" => SessionManagement::sessionExists("userid")]);
        $this->view("common/container_start");
        $this->view("upload/upload", ['albums' => $this->getAlbums()]);
        $this->view("common/container_end");
        $this->view("common/footer");
    }

    private function getAlbums() {
        $query = "select album from album where userid=:userid";
        $this->database->query($query);
        $this->database->bind("userid", SessionManagement::getSession("userid"));

        return $this->database->resultset();
    }

    public function cover() {
        if (!SessionManagement::sessionExists("userid")) {
            $result = ["code" => 101, "message" => "Login First"];
        } else {
            if (!file_exists("images/profile_cover")) {
                mkdir("images/profile_cover", 0744, true);
            }
            if (isset($_FILES["cover"]["name"])) {
                $file_name_exploded = explode(".", $_FILES["cover"]["name"]);
                $extension = end($file_name_exploded);
                $extension_check = strtolower($extension);
                if (in_array($extension_check, $this->allowedExts)) {
                    $random_name = md5((time() + rand(0, 200)));
                    $target_file_name = "tmp_" . $random_name . "." . $extension;
                    move_uploaded_file($_FILES["cover"]["tmp_name"], "images/profile_cover/" . $target_file_name);
                    $this->compressImage("images/profile_cover/" . $target_file_name, "images/profile_cover/" . $target_file_name, 1200, 1200);
                    $query = "update usersignup set cover=:cover where userid=:userid";
                    $this->database->query($query);
                    $this->database->bind("userid", SessionManagement::getSession("userid"));
                    $this->database->bind("cover", $target_file_name);
                    $this->database->execute();
                    $result = ["code" => 1, "message" => "Changed."];
                } else {
                    $result = ["code" => 103, "message" => "Image Type is not valid."];
                }
            } else {
                $result = ["code" => 103, "message" => "Select Image First"];
            }
        }
        echo json_encode($result);
    }

    public function icon() {
        if (!SessionManagement::sessionExists("userid")) {
            $result = ["code" => 101, "message" => "Login First"];
        } else {
            if (!file_exists("images/profile_img")) {
                mkdir("images/profile_cover", 0744, true);
            }
            if (isset($_FILES["cover"]["name"])) {
                $file_name_exploded = explode(".", $_FILES["cover"]["name"]);
                $extension = end($file_name_exploded);
                $extension_check = strtolower($extension);
                if (in_array($extension_check, $this->allowedExts)) {
                    $random_name = md5((time() + rand(0, 200)));
                    $target_file_name = "tmp_" . $random_name . "." . $extension;
                    move_uploaded_file($_FILES["cover"]["tmp_name"], "images/profile_img/" . $target_file_name);

                    $this->compressImage("images/profile_img/" . $target_file_name, "images/profile_img/" . $target_file_name, 200, 200);

                    $query = "update usersignup set img=:img where userid=:userid";
                    $this->database->query($query);
                    $this->database->bind("userid", SessionManagement::getSession("userid"));
                    $this->database->bind("img", $target_file_name);
                    $this->database->execute();
                    $result = ["code" => 1, "message" => "Changed."];
                } else {
                    $result = ["code" => 103, "message" => "Image Type is not valid."];
                }
            } else {
                $result = ["code" => 103, "message" => "Select Image First"];
            }
        }
        echo json_encode($result);
    }

    public function savedraft() {
        if (!SessionManagement::sessionExists("userid")) {
            $result = ["code" => 101, "message" => "Login First"];
        } else {



            $filenames = [];
            $originalname = [];
            if (!file_exists("images/tmp")) {
                mkdir("images/tmp", 0744, true);
            }
            $names = isset($_POST['names']) ? $_POST['names'] : "";

            if (empty($names)) {
                $result = ["code" => 102, "message" => "Empty Names."];
            } else {

                $names = explode(",", $names);
                $resultInfos = [];
                $index = 0;
                foreach ($names as $name) {
                    if (!empty($name)) {
                        if (isset($_FILES[$name]["name"])) {
                            $file_name_exploded = explode(".", $_FILES[$name]["name"]);
                            $extension = end($file_name_exploded);
                            $extension_check = strtolower($extension);
                            $resultInfo = ["code" => 1, "index" => $index, "name" => $name, "saved" => 0,
                                "filename" => "", "originalnames" => "", "title" => ""];
                            if (in_array($extension_check, $this->allowedExts)) {
                                $random_name = md5((time() + rand(0, 200)));
                                $target_file_name = "tmp_" . $random_name . "." . $extension;
                                $originalname[] = $_FILES[$name]["name"];
                                $filenames[] = $target_file_name;
                                move_uploaded_file($_FILES[$name]["tmp_name"], "images/tmp/" . $target_file_name);
                                $_SESSION[$target_file_name] = true;
                                $resultInfo['saved'] = 1;
                                $resultInfo['filename'] = $target_file_name;
                                $resultInfo['originalname'] = $_FILES[$name]["name"];
                            } else {
                                $resultInfo['code'] = 101;
                            }
                            $resultInfos[] = $resultInfo;
                        }
                    }
                    $index++;
                }
                $result = ["code" => 1, "photos" => $resultInfos];
            }
        }
        echo json_encode($result);
    }

    public function addalbum() {
        $albumname = isset($_POST['albumname']) ? $_POST['albumname'] : "";
        $albumname = strtolower($albumname);
        $result = ["code" => 101, "message" => "Unknown error."];
        if (!SessionManagement::sessionExists("userid")) {
            $result = ["code" => 100, "message" => "Login First"];
        } else
        if (strlen($albumname) == 0) {
            $result = ["code" => 105, "message" => "Album Name is required."];
        }else if (strlen($albumname) > 900) {
            $result = ["code" => 105, "message" => "Album Name should be less then 900 charecters."];
        } else {

            $query = "select count(*)  from album where userid=:userid and album=:album";
            $this->database->query($query);
            $this->database->bind("userid", SessionManagement::getSession("userid"));
            $this->database->bind("album", $albumname);
            $status = $this->database->firstColumn();
            if ($status == "0") {
                $url = $albumname . "-by-" . SessionManagement::getSession("username");
                $status = "1";
                while ($status == "1") {
                    $query = "select count(*)  from album where url=:url";
                    $this->database->query($query);
                    $this->database->bind("url", $url);
                    $status = $this->database->firstColumn();
                    if ($status == "1") {
                        $url = $albumname . "-" . rand(100000, 999999) . "-by-" . SessionManagement::getSession("username");
                    }
                }

                $query = "insert into album(userid,album,url) values(:userid,:album,:url)";
                $this->database->query($query);
                $this->database->bind("userid", SessionManagement::getSession("userid"));
                $this->database->bind("album", $albumname);
                $this->database->bind("url", $url);
                $status = $this->database->execute();
                if ($status == "1") {
                    $result = ["code" => 1, "message" => "Album Added.", "album" => strtolower($albumname), "displayname" => ucfirst($albumname)];
                } else {
                    $result = ["code" => 102, "message" => "Unknown error."];
                }
            } else {
                $result = ["code" => 103, "message" => "Album Alrady Exists."];
            }
        }
        echo json_encode($result);
    }

    public function add() {

        if (!file_exists("images/original")) {
            mkdir("images/original", 0744, true);
        }
        if (!file_exists("images/compressed")) {
            mkdir("images/compressed", 0744, true);
        }

        if (!file_exists("images/thumb")) {
            mkdir("images/thumb", 0744, true);
        }

        $albumname = isset($_POST['albumname']) ? $_POST['albumname'] : "";
        $privacy = isset($_POST['privacy']) ? $_POST['privacy'] : "";

        $uploadInfo = isset($_POST['info']) ? json_decode($_POST['info'], true) : "";
        if (!SessionManagement::sessionExists("userid")) {
            $result = ["code" => 100, "message" => "Login First"];
        } else
        if (strlen($albumname) == 0) {
            $result = ["code" => 105, "message" => "Album Name is required."];
        } else {

            $query = "select count(*)  from album where userid=:userid and album='self-by-" . SessionManagement::getSession("username") . "'";
            $this->database->query($query);
            $this->database->bind("userid", SessionManagement::getSession("userid"));
            $status = $this->database->firstColumn();
            if ($status == "0") {
                $query = "insert into album(userid,album,url) values(:userid,'self','self-by-" . SessionManagement::getSession("username") . "'" . ")";
                $this->database->query($query);
                $this->database->bind("userid", SessionManagement::getSession("userid"));
                $this->database->execute();
            }

            $query = "select count(*) from album where album=:album";
            $this->database->query($query);
            $this->database->bind("album", $albumname);
            $status = $this->database->firstColumn();
            if ($status != "1") {
                $albumname = "self";
            }
            $query = "select albumid from album where userid=:userid and album=:album";
            $this->database->query($query);
            $this->database->bind("userid", SessionManagement::getSession("userid"));
            $this->database->bind("album", $albumname);
            $albumid = $this->database->firstColumn();
            $privacy = ($privacy == "Public") ? 0 : 1;

            foreach ($uploadInfo as $info) {

                copy("images/tmp/" . $info['filename'], "images/original/" . $info['filename']);
                copy("images/tmp/" . $info['filename'], "images/compressed/" . $info['filename']);
                unlink("images/tmp/" . $info['filename']);

                $this->compressImage("images/compressed/" . $info['filename'], "images/thumb/" . $info['filename'], 300, 200);
                $this->compressImage("images/compressed/" . $info['filename'], "images/compressed/" . $info['filename'], 1000, 1000);

                $urlTitle = preg_replace("/[^A-Za-z0-9]/", '-', $info['title']);
                $status = "1";
                while ($status == "1") {
                    $query = "select count(*) from images where url=:url";
                    $this->database->query($query);
                    $this->database->bind("url", $urlTitle);
                    $status = $this->database->firstColumn();
                    if ($status == "1") {
                        $urlTitle = substr($urlTitle, 0, 100) . "-" . rand(100000, 999999);
                    }
                }

                $query = "insert into images(img,userid,albumid,title,name,privacy,url) values (:img,:userid,:albumid,:title,:name,:privacy,:url)";
                $this->database->query($query);
                $this->database->bind("userid", SessionManagement::getSession("userid"));
                $this->database->bind("albumid", $albumid);
                $this->database->bind("img", $info['filename']);
                $this->database->bind("name", $info['originalname']);
                $this->database->bind("title", $info['title']);
                $this->database->bind("privacy", $privacy);
                $this->database->bind("url", $urlTitle);
                $this->database->execute();
                $result["photos"][] = ["code" => 1];

//                $imgid = -1;
//                $query = "select imgid from images where img=:img";
//                $this->database->query($query);
//                $this->database->bind("img", $info['filename']);
//                $imgid = $this->database->firstColumn();
                $imgid = $this->database->lastInsertId();

                //insert hash tags
                $hashes = explode(" ", $info['title']);
                foreach ($hashes as $hash) {
                    if (substr($hash, 0, 1) == "#") {
                        $hash = substr($hash, 1);
                        $query = "insert into hash(hash,imgid) values(:hash,:imgid)";
                        $this->database->query($query);
                        $this->database->bind("hash", $hash);
                        $this->database->bind("imgid", $imgid);
                        $this->database->execute();
                    }
                }
                //insert meta data
                $query = "insert into image_meta(imgid,meta_key,meta_value) values(:imgid,:meta_key,:meta_value)";
                $this->database->query($query);

                $title = $info['title'];
                $src = "images/original/" . $info['filename'];
                $exif = exif_read_data($src, 0, true);
                foreach ($exif as $key => $section) {
                    foreach ($section as $name => $val) {
                        if (!empty($val)) {
                            $this->database->bind("imgid", $imgid);
                            $this->database->bind("meta_key", $name);
                            $this->database->bind("meta_value", $val);
                            $this->database->execute();
                        }
                    }
                }
                $this->database->bind("imgid", $imgid);
                $this->database->bind("meta_key", "title");
                $this->database->bind("meta_value", $title);
                $this->database->execute();

                //insert Palette
                $query = "insert into image_meta(imgid,meta_key,meta_value) values(:imgid,:meta_key,:meta_value)";
                $this->database->query($query);
                $palette = $this->colorPalette("images/original/" . $info['filename'], 5, 4);
                $i = 1;
                foreach ($palette as $color) {
                    $this->database->bind("imgid", $imgid);
                    $this->database->bind("meta_key", "Palette_" . $i);
                    $this->database->bind("meta_value", $color);
                    $this->database->execute();
                    $i++;
                }
            }
            $result = ["code" => 1, "message" => "Uploaded Successfully", "redirect" => "/"];
        }
        echo json_encode($result);
    }

    private function colorPalette($imageFile, $numColors, $granularity = 5) {
        $granularity = max(1, abs((int) $granularity));
        $colors = array();
        $size = @getimagesize($imageFile);
        if ($size === false) {
            user_error("Unable to get image size data");
            return false;
        }
//        $img = @imagecreatefromjpeg($imageFile);
        // Andres mentioned in the comments the above line only loads jpegs, 
        // and suggests that to load any file type you can use this:
        $img = @imagecreatefromstring(file_get_contents($imageFile));

        if (!$img) {
            user_error("Unable to open image file");
            return false;
        }
        for ($x = 0; $x < $size[0]; $x += $granularity) {
            for ($y = 0; $y < $size[1]; $y += $granularity) {
                $thisColor = imagecolorat($img, $x, $y);
                $rgb = imagecolorsforindex($img, $thisColor);
                $red = round(round(($rgb['red'] / 0x33)) * 0x33);
                $green = round(round(($rgb['green'] / 0x33)) * 0x33);
                $blue = round(round(($rgb['blue'] / 0x33)) * 0x33);
                $thisRGB = sprintf('%02X%02X%02X', $red, $green, $blue);
                if (array_key_exists($thisRGB, $colors)) {
                    $colors[$thisRGB] ++;
                } else {
                    $colors[$thisRGB] = 1;
                }
            }
        }
        imagedestroy($img);
        arsort($colors);
        return array_slice(array_keys($colors), 0, $numColors);
    }

    private function compressImage($src, $dst, $width = 300, $height = 200) {
        $code = exif_imagetype($src);
        /*
         * 1 - GIF
         * 2 - JPEG
         * 3 - PNG
         * 4 - SWF
         * 5 - PSD
         * 6 - BMP
         * 17 - IMAGETYPE_ICO
         * 
         * full list http://php.net/manual/en/function.exif-imagetype.php
         */

        if ($code == 2) {
            $image = imagecreatefromjpeg($src);
            $destination = $dst;
            $quality = 50;
            imagejpeg($image, $destination, $quality);
            imagedestroy($image);
//                $image = imagecreatefromjpeg($src);
//                imagescale ($image,200);
        } else if ($code == 3) {
            $image = imagecreatefrompng($src);
            $destination = $dst;
            $quality = 5;
            imagepng($image, $destination, $quality);
            imagedestroy($image);
        }
        if (in_array($code, $this->allowedCompression)) {
            $originalImagick = new Imagick($src);
            $originalImagick->thumbnailimage($width, $height, true);
            $originalImagick->setImageCompressionQuality(50);
            $originalImagick->writeImage($dst);
            $originalImagick->clear();
        }
    }

}
