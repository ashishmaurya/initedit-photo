<?php
$userInfo = $data['userInfo'];
?>
<div class="uploadProgressBar" id="uploadProgressBar"></div>
<div class="userCover" id="userProfileCoverPhoto" style="background-image: url(/public/images/profile_cover/<?php echo $userInfo['cover']; ?>)">
    <div class="userIconContainer">
        <!--<img src="/public/images/profile_img/<?php echo $userInfo['img']; ?>" alt="" class="userProfileIconPhoto" id="userProfileIconPhoto" />-->
        <div class="userProfileIconPhoto" id="userProfileIconPhoto" style="background-image: url(/public/images/profile_img/<?php echo $userInfo['img']; ?>)"></div>

        <a href="/user/<?php echo $data['username']; ?>"><?php echo $data['username']; ?></a>
    </div>
    <?php if(SessionManagement::sessionExists("userid") && SessionManagement::getSession("userid")==$userInfo['userid']){ ?>
    <button class="changeUserProfileCover" id="changeUserProfileCover">
        Change Cover
        <input type="file" id="userCoverUploadFile" onchange="showPreviewProfileCover()"/>
    </button>
    <button class="changeUserProfileCoverSave" id="changeUserProfileCoverSave" onclick="saveUserProfileCover()">Save</button>
    <div class="profileIconChangeContainer">
        <button class="changeUserProfileIcon" id="changeUserProfileIcon">
            Change Profile
            <input type="file" id="userIconUploadFile" onchange="showPreviewProfileIcon()"/>
        </button>
        <button class="changeUserProfileIconSave" id="changeUserProfileIconSave" onclick="saveUserProfileIcon()">Save</button>
    </div>
    <?php }?>
</div>
<div class="userMenu">
    <ul class="hl">

        <li><a href="/user/<?php echo $data['username']; ?>/photo" class="<?php echo ($data['menu'] == "photo") ? "highlight" : ""; ?>">Photo</a></li>
        <li><a href="/user/<?php echo $data['username']; ?>/album" class="<?php echo ($data['menu'] == "album") ? "highlight" : ""; ?>">Album</a></li>
        <li><a href="/user/<?php echo $data['username']; ?>/like" class="<?php echo ($data['menu'] == "like") ? "highlight" : ""; ?>">Like</a></li>
        <li><a href="/user/<?php echo $data['username']; ?>/favorite" class="<?php echo ($data['menu'] == "favorite") ? "highlight" : ""; ?>">Favorite</a></li>

    </ul>
</div>