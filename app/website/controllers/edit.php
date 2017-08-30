<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of edit
 *
 * @author home
 */
class edit extends Controller {

    public function index($photoURL = "") {
        $this->view("common/head", ["title" => "Initedit Photo", "description" => "Main Page"]);
        $this->view("common/headernew", ["isloggedin" => SessionManagement::sessionExists("userid")]);
        $photoURL = (isset($_GET["img"])) ? $_GET["img"] : "";
        if (empty($photoURL)) {
            header("Location: /recent");
        } else {

            $query = "select img from images where url=:url";
            $this->database->query($query);
           
            $this->database->bind("url", $photoURL);
            $photoImg = $this->database->firstColumn();
            if (empty($photoImg)) {
                header("Location: /recent");
            } else {
                $this->view("edit/edit", ["img" => $photoImg]);
            }
        }
    }

    public function filter() {
        $this->loadTools("Instagraph");
        $type = (isset($_GET["filter"])) ? $_GET["filter"] : "normal";
        $img = (isset($_GET["img"])) ? $_GET["img"] : "";
        $res = (isset($_GET["res"])) ? "compressed" : "thumb";
        $imgPath = "images/$res/" . $img;
        if (file_exists($imgPath)) {
            try {

                $instagraph = Instagraph::factory($imgPath, 'output.jpg');
                // $instagraph->toaster(); // name of the filter
                //$imgSrc = $instagraph->getImageResource();
                $imgSrc = $this->applyFilter($instagraph, $type);
                if ($imgSrc) {
                    header("Content-Type: image/jpeg");
//                    imagescale($imgSrc, 100,100);
                    imagejpeg($imgSrc);
                } else {
                    echo "Src not";
                }
                $instagraph->deleteImage();
            } catch (Exception $e) {
                echo $e->getMessage();
                die;
            }
        } else {
            echo "file not found | " . $imgPath;
        }
    }

    private function applyFilter($instagraph, $filterType) {
        $imgSrc = null;

//        if ($filterType == "toaster") {
//            $instagraph->toaster();
//        }else if ($filterType == "gotham") {
//            $instagraph->gotham();
//        }else if ($filterType == "nashville") {
//            $instagraph->nashville();
//        }else if ($filterType == "lomo") {
//            $instagraph->lomo();
//        }else if ($filterType == "kelvin") {
//            $instagraph->kelvin();
//        }else if ($filterType == "tiltShift") {
//            $instagraph->tiltShift();
//        }else if ($filterType == "myShift") {
//            $instagraph->myShift();
//        }
        $instagraph->$filterType();

        $imgSrc = $instagraph->getImageResource();
        return $imgSrc;
    }

}
