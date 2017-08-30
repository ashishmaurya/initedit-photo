<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of test
 *
 * @author home
 */
class test extends Controller {

    public function shell() {
        
    }

    public function geo() {
        $img = "images/original/91ff24311a7276b8e1a0ccc45a72d529.jpg";
        $data = exif_read_data($img, 0, true);
//        print_r($data);
        print_r($data["GPS"]);
    }

    public function pal() {



// sample usage: 
//        echo "<table>\n";

        $query_image = "select title,imgid,img from images limit 181,40";
        $this->database->query($query_image);
        $result = $this->database->resultSet();

        $query = "insert into image_meta(imgid,meta_key,meta_value) values(:imgid,:meta_key,:meta_value)";
        $this->database->query($query);
        foreach ($result as $image) {
            $image_name = $image['img'];
            $imgid = $image['imgid'];
            $palette = $this->colorPalette("images/original/" . $image_name, 5, 4);
            $i = 1;
            foreach ($palette as $color) {
//                echo "<tr><td style='background-color:#$color;width:2em;'>&nbsp;</td><td>#$color</td></tr>\n";
                $this->database->bind("imgid", $imgid);
                $this->database->bind("meta_key", "Palette_" . $i);
                $this->database->bind("meta_value", $color);
                $this->database->execute();
                $i++;
//                echo $imgid."<br/>";
            }
        }


//        echo "</table>\n";
        echo 'Done';
    }

    function colorPalette($imageFile, $numColors, $granularity = 5) {
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

    public function meta() {
        $query_image = "select distinct meta_key from image_meta";
        $this->database->query($query_image);
        $result = $this->database->resultSet();
        foreach ($result as $image) {
            echo $image['meta_key'] . "<br/>";
        }
    }

    public function index() {
//        $src = "images/original/tmp_2b30821f4d553a31e20ace7363943059.JPG";



        $query_image = "select title,imgid,img from images";
        $this->database->query($query_image);
        $result = $this->database->resultSet();


        $query = "insert into image_meta(imgid,meta_key,meta_value) values(:imgid,:meta_key,:meta_value)";
        $this->database->query($query);


        foreach ($result as $image) {
            $imgid = $image['imgid'];
            $title = $image['title'];
            $src = "images/original/" . $image['img'];
            $exif = exif_read_data($src, 0, true);
            foreach ($exif as $key => $section) {
                foreach ($section as $name => $val) {
                    if (!empty($val)) {
//                    echo "$name: $val<br />\n";
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
        }



        $this->view("test/home");
    }

    public function profile() {
        $query = "select cover from usersignup";
        $this->database->query($query);
        $result = $this->database->resultSet();
        foreach ($result as $image) {
            $src = "images/original/" . $image['cover'];
            $dest = "images/profile_cover/" . $image['cover'];
            if (file_exists($src)) {
                copy($src, $dest);
            }
        }
    }

    public function phpinfo() {
        echo phpinfo();
    }

    private function hash() {
        $query = "select title,imgid from images";
        $this->database->query($query);
        $result = $this->database->resultSet();
        foreach ($result as $image) {
            $hashes = explode(" ", $image['title']);
            foreach ($hashes as $hash) {
                if (substr($hash, 0, 1) == "#") {
                    $hash = substr($hash, 1);
                    $imgid = $image["imgid"];
                    $query = "insert into hash(hash,imgid) values(:hash,:imgid)";
                    $this->database->query($query);
                    $this->database->bind("hash", $hash);
                    $this->database->bind("imgid", $imgid);
                    $this->database->execute();
                }
            }
        }
    }

    private function album() {
        $query = "select album,albumid,userid,(select username from usersignup where usersignup.userid=album.userid) as username from album";
        $this->database->query($query);
        $result = $this->database->resultSet();
        foreach ($result as $album) {
            $url = $album["album"] . "-by-" . $album["username"];
            $status = "1";
            while ($status == "1") {
                $query = "select count(*)  from album where url=:url";
                $this->database->query($query);
                $this->database->bind("url", $url);
                $status = $this->database->firstColumn();
                if ($status == "1") {
                    $url = $album["album"] . "-" . rand(100000, 999999) . "-by-" . $album["username"];
                }
            }
            $query = "update album set url=:url where albumid=:albumid";
            $this->database->query($query);
            $this->database->bind("albumid", $album["albumid"]);
            $this->database->bind("url", $url);
            $status = $this->database->execute();
        }
    }

    private function image() {
        $query = "select title,imgid from images";
        $this->database->query($query);
        $result = $this->database->resultSet();
        foreach ($result as $album) {
            $urlTitle = preg_replace("/[^A-Za-z0-9]/", '-', $album['title']);
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


            $query = "update images set url=:url where imgid=:imgid";
            $this->database->query($query);
            $this->database->bind("imgid", $album["imgid"]);
            $this->database->bind("url", $urlTitle);
            $status = $this->database->execute();
        }
    }

    public function thumb() {
        $query = "select img from images order by imgid desc";
        $this->database->query($query);
        $result = $this->database->resultSet();
        foreach ($result as $album) {
            $src = "images/compressed/" . $album['img'];
            $dst = "images/thumb/" . $album['img'];
            unlink($dst);
            echo $dst . "<br/>";
            $originalImagick = new Imagick($src);
            $originalImagick->scaleImage(400, 400, true);
//            $originalImagick->setImageCompressionQuality(0);
            $originalImagick->writeImage($dst);
            $originalImagick->clear();
        }
    }

}
