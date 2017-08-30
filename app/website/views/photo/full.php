<?php
//print_r($data['info']);
$info = $data['info'];
?>
<div class="fullPhoto">
    <div class="fullPhotoImgContainer" style="background-image: url(/public/images/original/<?php echo $info['img']; ?>)">
<!--        <img src="/public/images/original/<?php echo $info['img']; ?>"/>-->
    </div>

    <div class="likeFavViewContainerHolder">
        <ul class="hl likeFavViewContainer">
            <li>
                <ul class="hl ">
                    <li class="word" id="like_<?php echo $info['imgid']; ?>" style="display:<?php echo ($info['userlike'] == 1) ? "none" : "block"; ?>">Like</li>
                    <li class="word likehighlight" id="dislike_<?php echo $info['imgid']; ?>" style="display:<?php echo ($info['userlike'] == 1) ? "block" : "none"; ?>">Like</li>
                    <li class="count likehighlight" id="likeCount_<?php echo $info['imgid']; ?>"><?php echo $info['likecount']; ?></li>
                </ul>
            </li>
            <li>
                <ul class="hl">
                    <li class="word" id="fav_<?php echo $info['imgid']; ?>" style="display:<?php echo ($info['userfav'] == 1) ? "none" : "block"; ?>">Favorite</li>
                    <li class="word favhighlight" id="unfav_<?php echo $info['imgid']; ?>" style="display:<?php echo ($info['userfav'] == 1) ? "block" : "none"; ?>">Favorite</li>
                    <li class="count favhighlight" id="favCount_<?php echo $info['imgid']; ?>"><?php echo $info['favcount']; ?></li>
                </ul>
            </li>
            <li>
                <ul class="hl">
                    <li class="word">View</li><li class="count viewhighlight" id="viewCount_<?php echo $info['imgid']; ?>"><?php echo $info['viewcount']; ?></li>
                </ul>
                <?php // var_dump($info);?>
            </li>
            <?php if ($info["username"]==SessionManagement::getSession("username")): ?>
            <li>
                <ul class="hl">
                    <li class="word photo-delete" data-id="<?php echo $info['imgid']; ?>">Delete</li>
                </ul>
            </li>
            <?php endif;?>
        </ul>
    </div>
    <div class="nextAndPreviousContainer">
        <a href="/photo/<?php echo $info['previous']["url"]; ?>" class="left button cta"> &lt;-- Previous</a>
        <a href="/photo/<?php echo $info['next']["url"]; ?>" class="right button cta">Next --&gt;</a>
    </div>
    <div class="centerText">
        <h2><?php echo $info['title']; ?></h2>
        BY<br/>
        <br/>
        <div style="background-image: url(/public/images/profile_img/<?php echo $info['usericon']; ?>)" class="fullPhotoUserImage"></div>
        <br/>
        <a href="/user/<?php echo $info['username']; ?>" class="fullPhotoUserName"><?php echo $info['username']; ?></a>
    </div>
    <div class="centerText">
        <?php
        $extraHTMLInfo = "";
        $extra = $info['extra'];
        foreach ($extra as $metainfo) {
            if (substr($metainfo["meta_key"], 0, 8) == "Palette_") {
                $colorPalette[] = $metainfo["meta_value"];
            } else {
                $extraHTMLInfo.= "<tr><td>" . $metainfo["meta_key"] . "</td><td>" . $metainfo["meta_value"] . "</td></tr>";
            }
        }
        $extraColorHTMLInfo="<div class='fullPagePhotoColorPalette'>"; 
        foreach ($colorPalette as $color) {
            $extraColorHTMLInfo.='<div style="background-color:#'.$color.'" class="paletteColorBox"></div>';
        }
        $extraColorHTMLInfo .= "</div>";
        echo $extraColorHTMLInfo;
        ?>
        
        <table class="photoDetailTable">
            <tr><th>Name</th><th>Description</th></tr>
            <tr><td>Album</td><td><a href="/album/<?php echo $info['albumurl']; ?>"><?php echo $info['albumname']; ?></a></td></tr>
            <tr><td>Width</td><td><?php echo $info['width']; ?></td></tr>
            <tr><td>Height</td><td><?php echo $info['height']; ?></td></tr>
            <tr><td>Size</td><td><?php echo $info['size']; ?></td></tr>
            <?php
            echo $extraHTMLInfo;
            ?>
        </table>

    </div>
</div>
<script>
    var fullPhotoJSONObject = <?php echo json_encode($info); ?>;
    $(window).on("load", function () {
        var photo = fullPhotoJSONObject;
        $("#like_" + photo.imgid).on("click", fullScreenLikePhoto.bind(photo));
        $("#dislike_" + photo.imgid).on("click", fullScreenDislikePhoto.bind(photo));
        $("#fav_" + photo.imgid).on("click", fullScreenFavPhoto.bind(photo));
        $("#unfav_" + photo.imgid).on("click", fullScreenUnfavPhoto.bind(photo));
        $(".photo-delete").on("click",deletePhoto);
    });
    function deletePhoto(){
        var imgid = $(this).attr("data-id");
        $.post("/photo/delete",{id:imgid},function(data){
            data = JSON.parse(data);
            if(data.code==1){
                window.location.href=data.url;
            }else{
                addMessageBox(1,data.message);
            }
        });
    }
    
</script>