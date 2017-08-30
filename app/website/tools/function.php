<?php

function get_view($view, $data = []) {
    $file = "../app/website/views/" . $view . ".php";
    if (file_exists($file)) {
        include $file;
    } else {
        echo "<h2>No Such Page</h2>";
    }
}

$db = new Database();

function get_image_post($id) {
    global $db;
    $query = " select 'album' as page,imgid,img,title,
        (select count(*) from imagelike where imagelike.imgid=images.imgid and userid=:userid) as likecount,
        (select count(*) from imagefav where imagefav.imgid=images.imgid and userid=:userid) as favcount
                  from images where imgid=:imgid ";
    $db->query($query);
    $db->bind("imgid", $id);
    $db->bind("userid",  get_current_userid());
    $result = $db->single();
    
    $img_path = "images/compressed/".$result["img"];
    list($width, $height, $type, $attr) = getimagesize($img_path);
    $result["width"] = $width;
    $result["height"] = $height;
    return $result;
}
