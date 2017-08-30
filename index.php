<?php
$START_TIME = microtime(true);
include_once "php/session/session_start.php";
$TITLE = " - photos.initedit.com";
$DESCRIPTION = "upload your awesome photos here and access it from anywhere and also see other's awesome photos";
$useragent = $_SERVER['HTTP_USER_AGENT'];
$IS_MOBILE = false;
if (preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i', $useragent) || preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i', substr($useragent, 0, 4))) {
    $IS_MOBILE = true;
    $_SESSION['ismobile'] = true;
} else {
    unset($_SESSION['ismobile']);
}
include_once "php/class/query.php";
include_once "php/class/captcha.php";
include_once "php/class/images.php";
$p = $_SERVER['REQUEST_URI'];
$p = str_replace("http://", "", $p);
$pArray = explode("/", $p);

header('HTTP/1.0 200 Found');
if (!isset($pArray[1]) || $pArray[1] == "" || is_numeric($pArray[1])) {
    $PAGE = "HOME";
    $TITLE = "Home " . $TITLE;
} else if ($pArray[1] == "login") {
    if (isset($_SESSION['userid'])) {
        header("Location: /");
    } else {
        $PAGE = "LOGIN";
        $TITLE = "Login " . $TITLE;
    }
} else if ($pArray[1] == "signup") {
    if (isset($_SESSION['userid'])) {
        header("Location: /");
    } else {
        $PAGE = "SIGNUP";
        $TITLE = "Signup " . $TITLE;
    }
} else if ($pArray[1] == "logout") {
    include_once "php/session/session_destroy.php";
    header("Location: /");
} else if ($pArray[1] == "hash") {
    $PAGE = "HASH";
    $TITLE = "Hash " . $TITLE;
} else if ($pArray[1] == "detail") {
    $PAGE = "DETAIL";
    $TITLE = "Detail photo " . $TITLE;
} else if ($pArray[1] == "editors") {
    $PAGE = "EDITORS";
    $TITLE = "Editor's pick " . $TITLE;
} else if ($pArray[1] == "blog") {
    $PAGE = "BLOG";
    $TITLE = "Blog " . $TITLE;
} else if ($pArray[1] == "search") {
    $PAGE = "SEARCH";
    $TITLE = "Search " . $TITLE;
} else if ($pArray[1] == "upload") {
    if (isset($_SESSION['userid'])) {
        $PAGE = "UPLOAD";
        $TITLE = "Upload " . $TITLE;
    } else {
        header("Location: /login");
    }
} else if ($pArray[1] == "api") {
    include "api.php";
    exit();
} else {
    $name = $pArray[1];
    if (QUERY::c("select count(*) from usersignup where username='{$name}'") == "1") {
        $PAGE = "PROFILE";
        $TITLE = ucfirst($name) . " " . $TITLE;
    } else {
        $PAGE = "ERROR";
        $TITLE = "Error : Page not Found " . $TITLE;
    }
}
$PAGE_URL = "http://photo.initedit.com" . $_SERVER['REQUEST_URI'];
$SOCIAL_IMG = "http://photo.initedit.com/img/icon.png";
?>
<!DOCTYPE html >
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
    <head>

        <script type="text/javascript"  src="/js/index.js"></script>
        <title><?php echo $TITLE; ?></title>
        <meta name="description" content="<?php echo $DESCRIPTION; ?>" />
        <meta name="language" content="english" />
        <meta name="keywords" content="photos, initedit" />
        <meta charset="UTF-8"/>
        <link rel="icon" href="/img/favicon.png" />
        <link rel="publisher" href="https://plus.google.com/107451468871433725729" />

        <!-- Schema.org markup for Google+ -->
        <meta itemprop="name" content="<?php echo $TITLE; ?>" />
        <meta itemprop="description" content="<?php echo $DESCRIPTION; ?>" />
        <meta itemprop="image" content="<?php echo $SOCIAL_IMG; ?>" />
        <!-- Twitter Card data -->
        <meta name="twitter:card" content="summary_large_image"/>
        <meta name="twitter:site" content="@initedit"/>
        <meta name="twitter:title" content="<?php echo $TITLE; ?>"/>
        <meta name="twitter:description" content="<?php echo $DESCRIPTION; ?>"/>
        <meta name="twitter:creator" content="@author_handle"/>
        <!-- Twitter summary card with large image must be at least 280x150px -->
        <meta name="twitter:image:src" content="<?php echo $SOCIAL_IMG; ?>"/>
        <!-- Open Graph data -->
        <meta property="og:type" content="article" />
        <meta property="og:title" content="<?php echo $TITLE ?>" />
        <meta property="og:url" content="<?php echo $PAGE_URL; ?>" />
        <meta property="og:image" content="<?php echo $SOCIAL_IMG; ?>" />
        <meta property="og:description" content="<?php echo $DESCRIPTION; ?>" />
        <meta property="og:site_name" content="Initedit" />

        <meta property="article:section" content="Article Section" />
        <meta property="article:tag" content="Article Tag" />
        <meta property="fb:app_id" content="1590199441245076" />
        <link rel="stylesheet" href="/css/index.css?<?php echo time(); ?>" type="text/css"/>
        <style type="text/css">
            .hl,.vl{
                list-style: none;
                margin: 0px;
                padding: 0px;
            }
            .hl > li{
                float: left;
            }
            .clearFix{
                clear: both;   
            }
            .leftImageBoxContainer  .photoOptions{
                opacity: 0;
                transition: opacity 0.5s;
                -webkit-transition: opacity 0.5s;
                -o-transition: opacity 0.5s;
                -moz-transition: opacity 0.5s;
                -ms-transition: opacity 0.5s;

            }
            .leftImageBoxContainer:hover .photoOptions{

                opacity: 1;
            }
            .time{
                cursor: pointer;   
            }
            .fulltime{
                display: none;   
            }
            .time:hover .fulltime{
                display: block;
                background: #FF6600;
                color:#000000;
                padding:5px;
                position:absolute; 
            }
            .logo img{
                padding-top:5px; 
            }
            .hash,.loginButton,.editors{
                color:#000000;
                border-top:#FFFFFF solid 5px;
                padding:0px 10px;
                min-height: 45px;
                line-height: 45px;
                font-weight: bold;
            }

            .hash:hover,.loginButton:hover,.editors:hover{
                background: #BBBBBB;
                border-top:#000000 solid 5px;
            }
            .editorsPicImg{
                transition: opacity 4s;
                -webkit-transition: opacity 4s;
                -moz-transition: opacity 4s;
                -o-transition: opacity 4s;
                -ms-transition: opacity 4s;
                position:  absolute;
                bottom: 0;
                width:100%;
            }
            .topShortHeader
            {
                position: fixed;
                bottom: 90%;
                width: 100%;
                z-index: 10;
                opacity:0;

                -webkit-transition: opacity 0.5s;
                -ms-transition: opacity 0.5s;
                -o-transition: opacity 0.5s;
                -moz-transition: opacity 0.5s;
                transition: opacity 0.5s;
            }
            .topShortHeaderContent
            {
                left:-50px;
                position: relative;
                -webkit-transition: left 1s;
                -ms-transition: left 1s;
                -o-transition: left 1s;
                -moz-transition: left 1s;
                transition: left 1s;
            }
            .hoverShowTitle{
                position: absolute;
                background: -webkit-linear-gradient(rgba(120 ,120,120,0.7), rgba(0 ,0,0,0.7)); 
                background: -o-linear-gradient(rgba(120 ,120,120,0.7), rgba(0 ,0,0,0.7)); 
                background: -moz-linear-gradient(rgba(120 ,120,120,0.7), rgba(0 ,0,0,0.7)); 
                background: linear-gradient(rgba(120 ,120,120,0.7), rgba(0 ,0,0,0.7));
                padding-top: 10px;
                padding-bottom: 10px;
                width: 100%;
                color: #FFF;
                font-weight: bold;
                display: none;
                transition: bottom 0.3s;
                -webkit-transition: bottom 0.3s;
                -moz-transition: bottom 0.3s;
                -o-transition: bottom 0.3s;
                -ms-transition: bottom 0.3s;

                bottom: -100px;
                padding-left:10px;
                display:block;
            }
            .img:hover .hoverShowTitle{

                bottom:0px;
            }
            .socialItem{
                width:100px;
                height: 100px;
                line-height: 100px;
                font-weight: bold;
                text-align: center;
                font-size: 40px;
                background: #AAAAAA;
                color:#FFFFFF;
                border-radius: 50px;
                margin:10px; 
            }
            .socialItem > a{
                text-decoration: none;
                color: #FFFFFF;
                width: 100px;
                height: 100px;
                display: block;
            }
            .socialFB:hover{
                background:#3b5998;
            }
            .socialT:hover{
                background:#55acee;
            }
            .socialG:hover{
                background:#dd4b39;
            }
            .searchImage{
                transition: opacity 2s;
                -webkit-transition: opacity 2s;
                -o-transition: opacity 2s;
                -ms-transition: opacity 2s;
            }
            .searchBox{
                height:400px;   
            }
            .settingList:hover #hiddenSetting{
                /*display:block;  
                z-index: 1000;*/
            }
            .settingList:hover{
                padding-top: 0px;
                background:#CCCCCC;

            }
            .settingList:hover img{
                background:#CCCCCC;
                border-top:5px solid #111111;

            }
            .styleLink{
                width:100%;
                padding:3px 0px;
                display: block;
                border-top:3px solid #FFFFFF;
            }
            .styleLink > span{
                padding-left: 20px;   
            }
            .styleLink:hover{
                border-top:3px solid #5586FF;
                background: #5586FF;
                color:#FFFFFF

            }
            .hashLink {
                border:#000 solid 1px;
                background:#CCCCCC;
                padding: 5px;
                margin: 5px;
                transition: background 0.5s;
                -wekbit-transition: background 0.5s;
                -ms-transition: background 0.5s;
                -moz-transition: background 0.5s;
                -o-transition: background 0.5s;
            }
            .hashLink:hover{
                border:#FF6600 solid 1px;
                background:#FFA500;
                opacity: 0.9;
                color:#FFF;
            }
            .middle{
                overflow: hidden;   
                min-height: 400px;
            }
            .bottom{
                border-top:#111 solid 3px;   
            }
            .blogUser{
                overflow: hidden;
                border-bottom:#111 solid 3px;     
            }
            .images > li{
                overflow: hidden; 
                background:#DDD;
            }
            .editorsLine{
                background:#FFF;
                overflow:hidden;
                border-top: 3px solid rgba(0,0,0,0);
                /* position: relative;*/
            }
            .isEditorPick:hover .editorsTitleLine{
                display: block;   
            }
            .isEditorPick{
                border-top: 3px solid rgba(0,220,0,0.8);
            }
            .isPrivatePhoto
            {
                border-top: 3px solid rgba(255,120,0,0.8);
            }
            .editorsTitleLine{
                display: none;
                /*position: absolute;*/
                left:40%;
                padding: 5px 15px;
                background: rgba(0,0,0,0.8);
                color: #FFF;
                text-align: center;
            }
            .editorsTitleLine::before{
                border-bottom: 10px solid rgba(0,0,0,0.8);
                bottom-left:10px solid trannsparent;
                bottom-right:10px solid trannsparent;
                left:50%;
                top:-10px;
            }
            .extraInfo > ul > li{
                overflow: hidden;   
            }
            .hoverProfilePic{
                overflow:hidden;   
                position: relative;
            }
            .hoverHiddenProfilePic{
                position: absolute;
                bottom:-100%;
                width:100%;
                height:100%;
                overflow: hidden;
                -webkit-transition: bottom 1s;
                -o-transition: bottom 1s;
                -ms-transition: bottom 1s;
                -moz-transition: bottom 1s;
                transition: bottom 1s;
            }
            .hoverProfilePic:hover .hoverHiddenProfilePic{
                bottom: 0%;  
            }
            .selectPrivacy{
                width:100%;
            }
            .input{

                -moz-box-sizing:border-box;
                -webkit-box-sizing:border-box;
                box-sizing:border-box;}

            @media print {.loginButton{}}@media only screen{.loginButton{}}
            @media only screen and (max-width:800px){.fullImageBox{position:fixed;top:0px;left:0px;}.leftImageBoxContainer{height:auto;}.rightImageBoxContainer ,.leftImageBoxContainer{float: none;clear: both;display:block;width:100%;}.mainContent>li>ul{width:48%}.profileMainContent>ul{width:48%}.singleAlbumImg img{width:98%}.headerContainer,.loginBox,.middle,.signupBox{width:100%}.header{height:50px}.previewImg{width:90%;max-height:300px}.fullImageBoxContiner{width:90%}.extraInfo{padding-top:10px;padding-bottom:10px;}}
            @media only screen and (max-width:500px){.fullImageBox{position:fixed;top:0px;left:0px;}.leftImageBoxContainer{height:auto;}.rightImageBoxContainer ,.leftImageBoxContainer{float: none;clear: both;display:block;width:100%;}.mainContent>li>ul,.profileMainContent>ul,.singleAlbumImg img{width:98%}.headerContainer,.loginBox,.middle,.signupBox{width:100%}.header{height:50px}.previewImg{width:90%;max-height:300px}.fullImageBoxContiner{width:90%}.extraInfo{padding-top:10px;padding-bottom:10px;}}
            .fullImageBox{position:fixed;top:0px;left:0px;height:100}
            <?php
            if ($PAGE == "PROFILE") {
                ?>
                .middle{
                    width:100%; 
                    margin:0 auto;
                }
                .profileMain,.profileMainContent{
                    width: 90%;
                    margin: 0 auto;
                }
            <?php } ?>
        </style>

        <?php
        if ($IS_MOBILE) {
            ?>
            <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
            <style type="text/css">
                .mainContent>li>ul,.profileMainContent>ul,.singleAlbumImg img{width:98%}.headerContainer,.loginBox,.middle,.signupBox{width:100%}.header{height:50px}.previewImg{width:90%;max-height:300px}.fullImageBox{position:relative}.fullImageBoxContiner{width:90%}.extraInfo{padding-top:10px;padding-bottom:10px;}
                @media only screen and (orientation : landscape){.middle{width:80%;}}
                .hash{display:none;}
                .fullImageBox{position:fixed;top:0px;left:0px;height:100%;width:100%;}
                .fullImageBoxContiner{height:100%;width:100%;}
                .rightImageBoxContainer{overflow-y:scroll;}
                .fullImageNavigateLeft,.fullImageNavigateRight{top:inherit;bottom:0px;width:49%;};
                .fullImageBoxContiner{overflow-y:scroll;}
                .fullImageBoxContiner img{
                    width: 100%;   
                }
                .leftImageBoxContainer >div{
                    width:100%;   
                }
            </style>
            <?php
        }
        ?>
        <script>
            (function(i, s, o, g, r, a, m) {
                i['GoogleAnalyticsObject'] = r;
                i[r] = i[r] || function() {
                    (i[r].q = i[r].q || []).push(arguments)
                }, i[r].l = 1 * new Date();
                a = s.createElement(o),
                        m = s.getElementsByTagName(o)[0];
                a.async = 1;
                a.src = g;
                m.parentNode.insertBefore(a, m)
            })(window, document, 'script', '//www.google-analytics.com/analytics.js', 'ga');

            ga('create', 'UA-65603663-1', 'auto');
            ga('send', 'pageview');

        </script>
        <script type="text/javascript">
            function setAsCover(img)
            {
                xml = post("/php/ajax/setascover.php", "img=" + img);
                response = xml.responseText;
                if (response == "NOTOK")
                {
                    alert("permission denied");
                } else if (response == "OK") {
                    alert("Cover Photo Updated");
                }
            }
            function setPrivacy(img)
            {
                xml = post("/php/ajax/setprivacy.php", "img=" + img + "&privacy=" + $("#imageprivacy").val());
                response = xml.responseText;
                if (response == "NOTOK")
                {
                    alert("permission denied");
                } else if (response == "OK") {
                    alert("Privacy Changed");
                }
            }
            function deleteImages(img)
            {
                isConfirmed = confirm("Are You Sure?");
                if (isConfirmed) {
                    xml = post("/php/ajax/deleteimage.php", "img=" + img);
                    response = xml.responseText;
                    if (response == "NOTOK")
                    {
                        alert("permission denied");
                    } else if (response == "OK") {
                        alert("Deleted.");
                    }
                }
            }
            function likeImagesCard(img)
            {
                event.stopPropagation();
                xml = post("/php/ajax/likeimage.php", "img=" + img + "&do=like");
                response = xml.responseText;
                if (response == "NOTOK")
                {
                    alert("Login First.");
                } else if (response == "OK") {
                    $("#like_" + img).hide();
                    $("#dislike_" + img).show();
                }
            }
            function dislikeImagesCard(img)
            {
                event.stopPropagation();
                xml = post("/php/ajax/likeimage.php", "img=" + img + "&do=unlike");
                response = xml.responseText;
                if (response == "NOTOK")
                {
                    alert("Login First.");
                } else if (response == "OK") {
                    $("#like_" + img).show();
                    $("#dislike_" + img).hide();
                }
            }
            function favImagesCard(img)
            {
                event.stopPropagation();
                xml = post("/php/ajax/favimage.php", "img=" + img + "&do=fav");
                response = xml.responseText;
                if (response == "NOTOK")
                {
                    alert("Login First.");
                } else if (response == "OK") {
                    $("#fav_" + img).hide();
                    $("#unfav_" + img).show();

                }
            }
            function unfavImagesCard(img)
            {
                event.stopPropagation();
                xml = post("/php/ajax/favimage.php", "img=" + img + "&do=unfav");
                response = xml.responseText;
                if (response == "NOTOK")
                {
                    alert("Login First.");
                } else if (response == "OK") {
                    $("#fav_" + img).show();
                    $("#unfav_" + img).hide();

                }
            }



            function likeImages(img)
            {
                xml = post("/php/ajax/likeimage.php", "img=" + img + "&do=like");
                response = xml.responseText;
                if (response == "NOTOK")
                {
                    alert("Login First.");
                } else if (response == "OK") {
                    $("#fullLikeText").hide();
                    $("#fullDislikeText").show();
                    count = parseInt($("#fullLikeCount").text());
                    count++;
                    $("#fullLikeCount").text(count)
                }
            }
            function dislikeImages(img)
            {
                xml = post("/php/ajax/likeimage.php", "img=" + img + "&do=unlike");
                response = xml.responseText;
                if (response == "NOTOK")
                {
                    alert("Login First.");
                } else if (response == "OK") {
                    $("#fullLikeText").show();
                    $("#fullDislikeText").hide();
                    count = parseInt($("#fullLikeCount").text());
                    count--;
                    $("#fullLikeCount").text(count)
                }
            }

            function favImages(img)
            {
                xml = post("/php/ajax/favimage.php", "img=" + img + "&do=fav");
                response = xml.responseText;
                if (response == "NOTOK")
                {
                    alert("Login First.");
                } else if (response == "OK") {
                    $("#fullFavText").hide();
                    $("#fullUnfavText").show();
                    count = parseInt($("#fullFavCount").text());
                    count++;
                    $("#fullFavCount").text(count)
                }
            }
            function unfavImages(img)
            {
                xml = post("/php/ajax/favimage.php", "img=" + img + "&do=unfav");
                response = xml.responseText;
                if (response == "NOTOK")
                {
                    alert("Login First.");
                } else if (response == "OK") {
                    $("#fullFavText").show();
                    $("#fullUnfavText").hide();
                    count = parseInt($("#fullFavCount").text());
                    count--;
                    $("#fullFavCount").text(count)
                }
            }


        </script>
    </head>
    <body onkeydown="handleKeys()">
        <h1 class="none"><?php echo $DESCRIPTION; ?></h1>
        <h2 class="none"><?php echo $DESCRIPTION; ?></h2>
        <div class='none'><ul><li class='socialShareButton socialGoogle'><a href="https://plus.google.com/share?url=<?php echo $PAGE_URL; ?>" target="_blank">g</a></li><li class='socialShareButton socialFacebook' ><a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode($PAGE_URL); ?>" target="_blank">f</a></li><li class='socialShareButton socialTwitter' ><a href="https://twitter.com/intent/tweet?text=<?php echo urlencode($TITLE) . urlencode("  #initedit"); ?>&url=<?php echo urlencode($PAGE_URL); ?>&via=initedit" target="_blank">t</a></li><li class='socialShareButton ' onclick='showSocialHidden()'>+</li></ul></div>
        <script type="text/javascript">function handleKeys() {
                ("q" == String.fromCharCode(event.keyCode) || "Q" == String.fromCharCode(event.keyCode) || event.keyCode == 27) && hideFullImage()
            }
        </script>
        <div class="body" style="background-color: #FFF;">
            <div class="fullImageBoxDully none" id="fullImageBoxDully" onclick="hideFullImage()"></div>
            <div class="fullImageBox none" id="fullImageBox" onclick="hideFullImage()" style="z-index: 100;">
                <div class="closeFullImage fr" style="z-index:10000;position: absolute;right: 1px;top:1px;color:#333;font-weight: bold;border: none;width: 20px;height: 20px;line-height: normal;font-family: monospace;" onclick="hideFullImage()">x</div>
                <div class="fullImageBoxContiner" id="fullImageBoxContiner" onclick="stopPropagation()" style="opacity:0.4;width:100%;background: #FFF;transition: opacity 0.5s;-webkit-transition: opacity 0.5s;-o-transition: opacity 0.5s;-moz-transition: opacity 0.5s;-ms-transition: opacity 0.5s;">

                </div>
            </div>
            <script type="text/javascript">
                function hideFullImage() {
                    $("#fullImageBoxContiner").css("opacity:0.4;");
                    setTimeout(function(){$("#fullImageBoxDully").hide(); $("#fullImageBox").hide();},200);
                }
                function stopPropagation() {
                    event.stopPropagation()
                }
                function loadFullImage(e) {
                   xml = post("/fullimage.php", "img=" + e + "&width=" + screen.width + "&height=" + screen.height + "&type=photo" + "&page=" + window.location.pathname + "&search=" + window.location.search), $("#fullImageBoxDully").show(), $("#fullImageBox").show(), $("#fullImageBoxContiner").text(xml.responseText)
                   setTimeout(function(){$("#fullImageBoxContiner").css("opacity:1;");},0);
                    
                }
            </script>
            <div class="header">
                <div style='position:absolute;top:0px;left:0px;height: 50px;background: rgba(255,128,0,0.2);width:0px;text-align: center;z-index: 1;' id="progressHeaderbar"></div>
                <div class="headerContainer" style="z-index: 2;">
                    <a href="/" class="link">
                        <div class="fl logo">
                            <img src="/img/icon.png" width="100" height="40" alt="home" />
                        </div>
                    </a>
                    <!--
                    <a href="/" class="link">
                        <div class="fl hash">
                            Home
                        </div>
                    </a>
                    -->
                    <a href="/hash" class="link">
                        <div class="fl hash" style="/*background-color: #5586FF;*/
    color: #000;
    margin-right: 10px;margin-left: 10px;">
                            Hash Tags
                        </div>
                    </a>
                    <a href="/editors" class="link">
                        <div class="fl hash" style="    /*background-color: #55AA86;*/
    color: #000;
    margin-right: 10px;">
                            Editor's Pick
                        </div>
                    </a>
                    <a href="/blog" class="link">
                        <div class="fl hash" style="   /* background-color: #FF5586;*/
    color: #000;">
                            Blog
                        </div>
                    </a>

                    <?php
                    if (isset($_SESSION['userid'])) {
                        $NAME = $_SESSION['username'];
                        ?>
                        <div class="fr">
                            <ul class="horizontalList">
                                <li class="hash" style="background-color: #5586FF;
    color: #FFF;
    margin-right: 10px;margin-left: 10px;"><a href='/search' class="link" style="display:block;color:#FFF;" style="">Search</a></li>
                                <li>
                                    <ul>
                                        <li class="settingList">
                                            <img src="/img/setting1.png" width="40" height="40" alt="setting" style="cursor: pointer;padding-left: 5px;padding-right: 5px;" onclick="toggleSetting()"/>
                                            <ul id="hiddenSetting" class="none setting" style="z-index: 1000;margin-top: 9px; border-top: 5px solid #5586FF;border-bottom: 5px solid #5586FF;">

                                                <li><a href='/upload' class='link styleLink'><span>Upload</span></a></li>
                                                <li><a href='/<?php echo $NAME; ?>' class='link styleLink'><span>Profile</span></a></li>
                                                <li><a href='/<?php echo $NAME; ?>' class='link styleLink'><span>Photos</span></a></li>
                                                <li><a href='/<?php echo $NAME; ?>/albums' class='link styleLink'><span>Albums</span></a></li>
                                                <li><a href='/<?php echo $NAME; ?>/likes' class='link styleLink'><span>Likes</span></a></li>
                                                <li><a href='/<?php echo $NAME; ?>/favourites' class='link styleLink'><span>Favourites</span></a></li>
                                                
                                                <li><a href='/logout' class='link styleLink'><span>Logout</span></a></li>
                                            </ul>
                                        </li>
                                    </ul>
                                </li>

                            </ul>
                        </div>
                        <script type="text/javascript">function toggleSetting() {
                                $("#hiddenSetting").toggle()
                            }</script>
                        <?php
                    } else {
                        ?>
                        <ul class="horizontalList" style="float:right;">
                            <li class="hash">
                                <a href='/search' class="link" style="display:block;">Search</a>
                            </li>
                            <li>
                                <a href="/login">
                                    <div class="fr loginButton">
                                        Login / Signup
                                       <!-- <a href="/login" class="link"><img src="/img/login.png" width="100" height="40" alt="login"/></a>-->
                                    </div>
                                </a>
                            </li>

                        </ul>
                        <?php
                    }
                    ?>

                </div>
            </div>
            <div class="middle">
                <ul class="mainContent" id="mainContent">
                    <script type="text/javascript">
                        function loadHomeMore() {
                            homepageno++;
                            homeProgressLeftPosition = 100;
                            xml = post("/php/ajax/loadhomemore.php", "pageno=" + homepageno, true, function() {
                                if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                                    clearInterval(homeProgressInterval);
                                    $("#loadMore").append(xmlhttp.responseText);
                                    $("#moreHomePageButton").show();
                                    $("#homeLoadProgress").hide();
                                    if ("" == xmlhttp.responseText) {
                                        $("#loadMore").append("<div class='loadNoMore'>No More Photos Available.</div>");
                                        $("#moreHomePageButton").hide();
                                    }

                                }
                            });
                            var homeProgressInterval = setInterval(function() {
                                $("#moreHomePageButton").hide();
                                $("#homeLoadProgress").show();
                                $("#homeLoadProgress").css("left", (homeProgressLeftPosition * -1) + "%");
                                homeProgressLeftPosition -= 5;
                                if (homeProgressLeftPosition < -100) {
                                    homeProgressLeftPosition = 100;
                                }
                            }
                            , 10);

                        }
                        homepageno = 1;
                    </script>
                    <?php
                    //echo QUERY::c('select current_time from dual');
                    $POST_PER_PAGE = 12;
                    if ($PAGE == "HOME") {
                        include "php/page/home.php";
                    } else if ($PAGE == "LOGIN") {
                        include "php/page/login.php";
                    } else if ($PAGE == "SIGNUP") {
                        include "php/page/signup.php";
                    } else if ($PAGE == "UPLOAD") {
                        include "php/page/upload.php";
                    } else if ($PAGE == "HASH") {
                        include "php/page/hash.php";
                    } else if ($PAGE == "PROFILE") {
                        include 'php/page/profile.php';
                    } else if ($PAGE == "EDITORS") {
                        include 'php/page/editor.php';
                    } else if ($PAGE == "BLOG") {
                        include 'php/page/blog.php';
                    } else if ($PAGE == "DETAIL") {
                        include 'php/page/photo.php';
                    } else if ($PAGE == "SEARCH") {
                        include 'php/page/search.php';
                    }
                    if ($PAGE == "ERROR") {
                        include 'php/page/error.php';
                    }
                    ?>
                </ul>
            </div>
            <div class="bottom">
                <div class="bottomContainer">Initedit &copy; All Right Reserved.</div>
            </div>
        </div>
        <?php
        $START_TIME = microtime(true) - $START_TIME;
        echo "<script>console.log('Execution Time : " . ($START_TIME * 1000) . " seconds');</script>";
        ?>
    </body>
</html>
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-72096245-1', 'auto');
  ga('send', 'pageview');

</script>
