<?php

class search extends Controller {

    public function index() {
        $this->view("common/head", ["title" => "Search - Initedit Photo", "description" => "Main Page"]);
        $this->view("common/header", ["isloggedin" => SessionManagement::sessionExists("userid")]);
        $this->view("common/container_start");
        $this->view("search/search", []);
        $this->view("common/container_end");
        $this->view("common/footer");
    }
//
//    public function index() {
//        $this->view("common/head", ["title" => "Initedit Photo", "description" => "Main Page"]);
//        $this->view("common/header");
//        $hashes = $this->gethash();
//        $this->view("search/newsearch", ["hash"=>$hashes]);
//    }
    
    private function gethash() {
        $userid = -1;
        if (SessionManagement::sessionExists("userid")) {
            $userid = SessionManagement::sessionExists("userid");
        }
        $query = "select count(*) as total,hash,(select images.img from images where images.imgid=hash.imgid) as hashimg from hash group by hash order by total desc limit 4";
        $this->database->query($query);
        $result = $this->database->resultset();
        return $result;
        
    }

    public function get() {
        $userid = -1;
        if (SessionManagement::sessionExists("userid")) {
            $userid = SessionManagement::sessionExists("userid");
        }
        $searchTerm = isset($_POST['searchTerm']) ? $_POST['searchTerm'] : "";
        $searchType = isset($_POST['searchType']) ? $_POST['searchType'] : "photo";
        $searchType = strtolower($searchType);
        $searchTerm = strtolower($searchTerm);
        $searchTerm = preg_replace("/[^A-Za-z0-9 _]/", '', $searchTerm);
        $searchTerm = preg_replace('!\s+!', ' ', $searchTerm);
        $searchTerm = (strlen($searchTerm) > 50) ? substr($searchTerm, 0, 50) : $searchTerm;
        $searchTerm = str_replace(" ", "%", $searchTerm);
        $result["searchType"] = $searchType;
        $query = "set profiling=1";
        $this->database->query($query);
        $this->database->execute();
        if ($searchType == "photo") {
            $query = "select :searchType as page, imgid,img,title,(select count(*) from imagelike where imagelike.imgid=images.imgid and userid=:userid) as likecount,(select count(*) from imagefav where imagefav.imgid=images.imgid and userid=:userid) as favcount
                  from images where  LOWER(title) like :title  and enable=0 order by imgid desc limit " . App::$NUMBER_PHOTO_PER_PAGE;
            $this->database->query($query);
            $this->database->bind("userid", $userid);
            $this->database->bind("searchType", "search_" . $searchType);
            $this->database->bind("title", "%" . $searchTerm . "%");
            $result["code"] = 1;
            $result["type"] = "photo";
            $result["photos"] = $this->database->resultset();
        } else if ($searchType == "people") {
            $query = "select :searchType as page, username,userid,img,cover
                  from usersignup where  LOWER(username) like :username  order by userid desc limit " . App::$NUMBER_PHOTO_PER_PAGE;
            $this->database->query($query);
            $this->database->bind("searchType", "search_" . $searchType);
            $this->database->bind("username", "%" . $searchTerm . "%");
            $result["code"] = 1;
            $result["type"] = "user";
            $result["users"] = $this->database->resultset();
        }

        $query = "show profiles";
        $this->database->query($query);
        $profile = $this->database->single();
        $result["query_time"] = $profile["Duration"];
        echo json_encode($result);
    }

    public function more() {
        $userid = -1;
        if (SessionManagement::sessionExists("userid")) {
            $userid = SessionManagement::sessionExists("userid");
        }
        $photo = isset($_POST['photo']) ? json_decode($_POST['photo']) : "";
        $photo = (array) $photo;
        $searchTerm = isset($photo['search_query']) ? $photo['search_query'] : "";
        $searchType = isset($photo['page']) ? $photo['page'] : "search_photo";
        $searchType = strtolower($searchType);
        $searchTerm = strtolower($searchTerm);
        $searchTerm = preg_replace("/[^A-Za-z0-9 _]/", '', $searchTerm);
        $searchTerm = preg_replace('!\s+!', ' ', $searchTerm);
        $searchTerm = (strlen($searchTerm) > 50) ? substr($searchTerm, 0, 50) : $searchTerm;
        $searchTerm = str_replace(" ", "%", $searchTerm);
        $result["searchType"] = $searchType;
        if ($searchType == "search_photo") {
            $query = "select :searchType as page,:searchTerm as search_query, imgid,img,title,(select count(*) from imagelike where imagelike.imgid=images.imgid and userid=:userid) as likecount,(select count(*) from imagefav where imagefav.imgid=images.imgid and userid=:userid) as favcount
                  from images where LOWER(title) like :title  and enable=0 and imgid<:imgid order by imgid desc limit " . App::$NUMBER_PHOTO_PER_PAGE;
            $this->database->query($query);
            $this->database->bind("userid", $userid);
            $this->database->bind("searchType", $searchType);
            $this->database->bind("searchTerm", $photo['search_query']);
            $this->database->bind("title", "%" . $photo['search_query'] . "%");
            $this->database->bind("imgid", $photo['imgid']);
            $result["code"] = 1;
            $result["photo"] = $photo;
            $result["type"] = "photo";
            $result["photos"] = $this->database->resultset();
        } else if ($searchType == "search_people") {
            if (!empty($photo['search_query'])) {
                $query = "select :searchType as page,:searchTerm as search_query, username,userid,img,cover
                  from usersignup where  LOWER(username) like :username and userid<:userid  order by userid desc limit " . App::$NUMBER_PHOTO_PER_PAGE;
                $this->database->query($query);
                $this->database->bind("userid", $photo['userid']);
                $this->database->bind("searchType", $searchType);
                $this->database->bind("searchTerm", $photo['search_query']);
                $this->database->bind("username", "%" . $photo['search_query'] . "%");
                $result["code"] = 1;
                $result["user"] = $photo;
                $result["type"] = "people";
                $result["users"] = $this->database->resultset();
            } else {
                $result["code"] = 1;
                $result["user"] = $photo;
                $result["type"] = "people";
                $result["users"] = [];
            }
        }
        echo json_encode($result);
    }

}
