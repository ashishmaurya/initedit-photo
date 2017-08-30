function e(msg) {
    console.log(msg)
}
function _(id) {
    return document.getElementById(id);
}
var config = {
    scrollOffsetForLoadMore: 300,
    scrollTimerForLoadMore: 50,
    photoPerPage: 12
}

var latestFullPhotoObject = null;
var photoTemplate, fullPhotoTemplate, albumTemplate;

photoTemplate = '<div class="photoContainer" itemscope itemtype ="http://schema.org/ImageObject">            <div class="photo" id="photo_{{id}}" style="background-image: url(\'/public/images/compressed/{{img}}\')">            </div>            <div class="title">                <div class="titleText" itemprop="caption">{{title}}</div>                <div class="likeAndFavContainer">                    <ul class="hl">                        <li class="likeIcon">                            <img src="/public/images/img/like_normal.png" id="like_{{id}}" data-id="{{id}}">                            <img src="/public/images/img/like_highlight.png" id="dislike_{{id}}" data-id="{{id}}">                        </li>                        <li class="favIcon">                            <img src="/public/images/img/fav_normal.png" id="fav_{{id}}" data-id="{{id}}">                            <img src="/public/images/img/fav_highlight.png" id="unfav_{{id}}" data-id="{{id}}">                        </li>                    </ul>                </div>            </div>        </div>';
fullPhotoTemplate = '<div class="fullScreenPhoto">            <div class="close" onclick="closeFullScreenPhoto()"><img src="/public/images/img/close-icon.png"/></div>            <div class="photoHolder">                <div class="previousFullPhoto" id="previousFullPhoto">                    <span>&lt;</span>                </div>                <div class="nextFullPhoto" id="nextFullPhoto">                    <span>&gt;</span>                </div>                <img src="/public/images/compressed/{{img}}">            </div>            <div class="photoDetailHolder">                <div class="userInfo">                    <img src="/public/images/profile_img/{{user_img}}">                    <a href="/user/{{username}}">{{username}}</a>                </div>                <div class="title">{{title}}</div>                <div>                    <ul class="hl likeFavViewContainer">                        <li>                            <ul class="hl ">                                <li class="word" id="like_{{id}}">Like</li>                                <li class="word likehighlight" id="dislike_{{id}}">Like</li>                                <li class="count likehighlight" id="likeCount_{{id}}">12</li>                            </ul>                        </li>                        <li>                            <ul class="hl">                                <li class="word" id="fav_{{id}}">Favorite</li>                                <li class="word favhighlight" id="unfav_{{id}}">Favorite</li>                                <li class="count favhighlight" id="favCount_{{id}}">12</li>                            </ul>                        </li>                        <li>                            <ul class="hl">                                <li class="word">View</li><li class="count viewhighlight" id="viewCount_{{id}}">12</li>                            </ul>                        </li>                    </ul>                </div>                <div class="viewFullPhoto">                    <a href="/photo/{{url}}"><button>View Full Photo</button></a>                </div><div class="viewFullPhoto">                    <a href="/edit/?img={{url}}"><button>Image Filter</button></a>                </div>                <div>                    <table class="photoDetailTable">                        <tbody><tr><th>Name</th><th>Description</th></tr>                        <tr><td>Album</td><td><a href="/album/{{albumurl}}" title="{{album}}">{{album}}</a></td></tr>                        <tr><td>Width</td><td>{{width}}</td></tr>                        <tr><td>Height</td><td>{{height}}</td></tr>                        <tr><td>Size</td><td>{{size}}</td></tr>                    </tbody></table>                </div><div class="fullPhotoColorPalatteTitle">Color Palette</div> <div id="fullPhotoColorPalatte" class="fullPhotoColorPalatte"></div>           </div>        </div>';
albumTemplate = '<div class="album">            <div class="albumTitle">                {{album}}                <a href="/album/{{albumurl}}" class="seeAll">See All Photo</a>            </div>            <a href="/album/{{albumurl}}">                <div class="imgage" style="background-image: url(/public/images/compressed/{{img}});"></div>            </a>        </div>';

$(window).on("load", function () {
    $(window).on("click", function () {
        if ($("#hiddenTopMoreOptionContainer").css("display") == "block") {
            toggleMoreOption();
        }
    })
});


function photoComesUnderFocus()
{
    $("#photo_" + this.imgid).css("background-image:url(/public/images/thumb/" + this.img + ")");
}
function photoLossesUnderFocus()
{

}



function displayPhotos(config)
{
    var data = config.data;
    for (var i = 0; i < data.length; i++) {
        var photo = config.data[i];
        var title = photo.title;
        title = title.replace(/#(\S*)/g, '<a href="/hash/$1">\#$1</a>');

        var txtTemplate = $("#templates").find(".photoTemplate").html();
        txtTemplate = txtTemplate.replace(/\{\{id\}\}/g, photo.imgid);

        txtTemplate = txtTemplate.replace(/\{\{title\}\}/g, title);
        txtTemplate = txtTemplate.replace(/\{\{img\}\}/g, photo.img);
        $("#" + config.id).append(txtTemplate);

        if (photo.likecount == "0")
        {
            $("#like_" + photo.imgid).show();
            $("#dislike_" + photo.imgid).hide();
        } else {
            $("#like_" + photo.imgid).hide();
            $("#dislike_" + photo.imgid).show();
        }
        $("#like_" + photo.imgid).on("click", likePhoto.bind(photo));
        $("#dislike_" + photo.imgid).on("click", dislikePhoto.bind(photo));

        if (photo.favcount == "0")
        {
            $("#fav_" + photo.imgid).show();
            $("#unfav_" + photo.imgid).hide();
        } else {
            $("#fav_" + photo.imgid).hide();
            $("#unfav_" + photo.imgid).show();
        }
        $("#fav_" + photo.imgid).on("click", favPhoto.bind(photo));
        $("#unfav_" + photo.imgid).on("click", unfavPhoto.bind(photo));
        $("#photo_" + photo.imgid).on("click", showFullScreenPhoto.bind(photo));

    }
    setTimeout(checkPhotoCountForEmptyAndThreshold, 100);

}
function checkPhotoCountForEmptyAndThreshold()
{
    if ($(".photoContainer").length < config.photoPerPage)
    {
        $("#messageFooter").show();
    }
}

function closeFullScreenImage() {
    $(".full-screen-photo").removeClass("visible");
    $(document).off("keyup", hideFullScreenImage);
}
function hideFullScreenImage(e) {
    if (e.keyCode == 27) {
        closeFullScreenImage();
    }
}

$(document).ready(function () {
    var $template = $(".full-screen-photo");
    $template.find(".img-like").click(function () {
        var photo = $(this).data("photo");
        $.post("/photo/like", {photo: JSON.stringify(photo)}, function (response) {
            response = JSON.parse(response);
            console.log(response);
            if (response.code == 1) {
                $template.find(".img-like").hide();
                $template.find(".img-dislike").show();
                var count = parseInt($template.find(".like-count").text());
                count++;
                $template.find(".like-count").text(count);
            } else {
                $template.find(".img-like").show();
                $template.find(".img-dislike").hide();

            }
        });
    });

    $template.find(".img-dislike").click(function () {
        var photo = $(this).data("photo");
        $.post("/photo/dislike", {photo: JSON.stringify(photo)}, function (response) {
            response = JSON.parse(response);
            if (response.code == 1) {
                $template.find(".img-dislike").hide();
                $template.find(".img-like").show();
                var count = parseInt($template.find(".like-count").text());
                count--;
                $template.find(".like-count").text(count);
            } else {
                $template.find(".img-dislike").show();
                $template.find(".img-like").hide();

            }
        });
    });

    $template.find(".img-fav").click(function () {
        var photo = $(this).data("photo");
        $.post("/photo/fav", {photo: JSON.stringify(photo)}, function (response) {
            response = JSON.parse(response);
            if (response.code == 1) {
                $template.find(".img-fav").hide();
                $template.find(".img-unfav").show();
                var count = parseInt($template.find(".fav-count").text());
                count++;
                $template.find(".fav-count").text(count);
            } else {
                $template.find(".img-fav").show();
                $template.find(".img-unfav").hide();

            }
        });
    });
    $template.find(".img-unfav").click(function () {
        var photo = $(this).data("photo");
        $.post("/photo/unfav", {photo: JSON.stringify(photo)}, function (response) {
            response = JSON.parse(response);
            if (response.code == 1) {
                $template.find(".img-unfav").hide();
                $template.find(".img-fav").show();
                var count = parseInt($template.find(".fav-count").text());
                count++;
                $template.find(".fav-count").text(count);
            } else {
                $template.find(".img-unfav").show();
                $template.find(".img-fav").hide();

            }
        });
    });
    $template.find(".nav-next").click(fullScreenNextNav);
    $template.find(".nav-previous").click(fullScreenPriviousNav);




});
function fullScreenNextNav() {
    var $template = $(".full-screen-photo");
    var photo = $template.data("photo");
    var nextPhoto = null;
    for (var i = 0; i < photosJSON.length; i++) {
        var p = photosJSON[i];
        if (p.imgid == photo.imgid) {
            if (i < photosJSON.length - 1) {
                nextPhoto = photosJSON[i + 1];
                var $photoContainer = $("#photo_" + photo.imgid);
                window.scrollTo(0, ($photoContainer.offset().top));
                break;
            } else {
                nextPhoto = photosJSON[0];
                var $photoContainer = $("#photo_" + photo.imgid);
                window.scrollTo(0, ($photoContainer.offset().top));
                break;
            }
        }
    }
    if (nextPhoto != null) {
        (showFullScreenPhoto.bind(nextPhoto))();
    }
}
function fullScreenPriviousNav() {
    var $template = $(".full-screen-photo");
    var photo = $template.data("photo");
    var previousPhoto = null;
    for (var i = 0; i < photosJSON.length; i++) {
        var p = photosJSON[i];
        if (p.imgid == photo.imgid) {
            if (i > 0 && photosJSON.length > 1) {
                previousPhoto = photosJSON[i - 1];

            } else {
                previousPhoto = photosJSON[photosJSON.length - 1];
            }
            break;
        }
    }
    if (previousPhoto != null) {
        (showFullScreenPhoto.bind(previousPhoto))();
    }
}

function showFullScreenPhoto()
{
    var photo = this;

    var $template = $(".full-screen-photo");
    $template.find(".img-content").css("background-image", "url(/public/images/compressed/" + photo.img + ")");
    $template.find(".loading").show();
    $template.find(".content").hide();
    $template.data("photo", photo);
    $template.addClass("visible");
    $template.scrollTop(0);
    $(document).on("keyup", hideFullScreenImage);
    $.ajax({
        type: "post",
        url: "/photo/info",
        async: true,
        dataType: "json",
        data: {photo: JSON.stringify(photo)},
        success: function (response) {
            console.log(response);
            if (response.code == 1) {
                var $userInfo = $template.find(".userInfo");
                $userInfo.find("img").attr("src", "/public/images/profile_img/" + response.usericon);
                $userInfo.find("a").attr("href", "/user/"+response.username).html(response.username);
                $template.find(".title").html(response.title);
                $template.find(".viewFullPhoto a").attr("href", "/photo/" + response.url);
                $template.find(".viewFullPhotoFilter a").attr("href", "/edit/" + response.url);
                $template.find(".album-url").attr("href", "/album/" + response.albumurl);
                $template.find(".album-url").html(response.albumname);
                $template.find(".width").html(response.width);
                $template.find(".height").html(response.height);
                $template.find(".size").html(response.size);


                $template.find(".like-count").text(response.likecount);
                $template.find(".fav-count").text(response.favcount);
                $template.find(".view-count").text(response.viewcount);

                if (response.userlike == "0")
                {
                    $template.find(".img-like").show();
                    $template.find(".img-dislike").hide();
                } else {
                    $template.find(".img-like").hide();
                    $template.find(".img-dislike").show();
                }

                if (response.userfav == "0")
                {
                    $template.find(".img-fav").show();
                    $template.find(".img-unfav").hide();
                } else {
                    $template.find(".img-fav").show();
                    $template.find(".img-unfav").hide();
                }

                $template.find(".img-like").data("photo", photo);
                $template.find(".img-dislike").data("photo", photo);
                $template.find(".img-fav").data("photo", photo);
                $template.find(".img-unfav").data("photo", photo);


                var colorTxt = "";
                for (i = 0; i < response.extra.length; i++)
                {

                    var extra = response.extra[i];
                    if (extra.meta_key.startsWith("Palette_"))
                    {
                        var color = (extra.meta_value);
                        colorTxt += "<div style='background-color:#" + color + "' class='paletteColorBox'></div>";
                    }
                }
                $template.find(".fullPhotoColorPalatte").html(colorTxt);
                $template.find(".content").show();
            } else {
                addMessageBox(1, response.message);
            }
        }
    }).always(function () {
        $template.find(".loading").hide();
    });
    return;
}
function displayAlbums(config)
{
    for (albumJSONIndex in config.data)
    {
        var album = config.data[albumJSONIndex];
//        var txtTemplate = $("#albumTemplate").text();
        var txtTemplate = albumTemplate;
        //Present in views/common/header.php
        txtTemplate = txtTemplate.replace(/\{\{album\}\}/g, album.album);
        txtTemplate = txtTemplate.replace(/\{\{albumurl\}\}/g, album.albumurl);
        txtTemplate = txtTemplate.replace(/\{\{img\}\}/g, album.img);

        $("#" + config.id).append(txtTemplate);
    }
}
function likePhoto(e)
{
    var photo = this;
    $.ajax({
        type: "post",
        url: "/photo/like",
        async: true,
        dataType: "json",
        data: {photo: JSON.stringify(photo)},
        start: function () {
            $("#like_" + photo.imgid).hide();
            $("#dislike_" + photo.imgid).show();
        },
        success: function (response) {
            if (response.code == 1)
            {
                $("#like_" + photo.imgid).hide();
                $("#dislike_" + photo.imgid).show();

            } else {
                $("#like_" + photo.imgid).show();
                $("#dislike_" + photo.imgid).hide();
                addMessageBox(1, response.message);
            }
        },
        end: function () {

        },
        failure: function () {
            addMessageBox(1, "No Internet Connection");
            $("#like_" + photo.imgid).show();
            $("#dislike_" + photo.imgid).hide();
        }
    });
}
function dislikePhoto()
{

    var photo = this;
    $.ajax({
        type: "post",
        url: "/photo/dislike",
        async: true,
        dataType: "json",
        data: {photo: JSON.stringify(photo)},
        start: function () {
            $("#like_" + photo.imgid).show();
            $("#dislike_" + photo.imgid).hide();
        },
        success: function (response) {
            if (response.code == 1)
            {
                $("#like_" + photo.imgid).show();
                $("#dislike_" + photo.imgid).hide();
            } else {
                $("#like_" + photo.imgid).hide();
                $("#dislike_" + photo.imgid).show();
                addMessageBox(1, response.message);
            }
        },
        end: function () {

        },
        failure: function () {
            addMessageBox(1, "No Internet Connection");
            $("#like_" + photo.imgid).hide();
            $("#dislike_" + photo.imgid).show();
        }
    });
}
function favPhoto()
{
    var photo = this;
    $.ajax({
        type: "post",
        url: "/photo/fav",
        async: true,
        dataType: "json",
        data: {photo: JSON.stringify(photo)},
        start: function () {
            $("#fav_" + photo.imgid).hide();
            $("#unfav_" + photo.imgid).show();
        },
        success: function (response) {
            if (response.code == 1)
            {
                $("#fav_" + photo.imgid).hide();
                $("#unfav_" + photo.imgid).show();
            } else {
                $("#fav_" + photo.imgid).show();
                $("#unfav_" + photo.imgid).hide();
                addMessageBox(1, response.message);
            }
        },
        end: function () {

        },
        failure: function () {
            addMessageBox(1, "No Internet Connection");
            $("#fav_" + photo.imgid).show();
            $("#unfav_" + photo.imgid).hide();
        }
    });
}
function unfavPhoto()
{
    var photo = this;
    $.ajax({
        type: "post",
        url: "/photo/unfav",
        async: true,
        dataType: "json",
        data: {photo: JSON.stringify(photo)},
        start: function () {
            $("#fav_" + photo.imgid).show();
            $("#unfav_" + photo.imgid).hide();
        },
        success: function (response) {
            if (response.code == 1)
            {
                $("#fav_" + photo.imgid).show();
                $("#unfav_" + photo.imgid).hide();
            } else {
                $("#fav_" + photo.imgid).hide();
                $("#unfav_" + photo.imgid).show();
                addMessageBox(1, response.message);
            }
        },
        end: function () {

        },
        failure: function () {
            addMessageBox(1, "No Internet Connection");
            $("#fav_" + photo.imgid).hide();
            $("#unfav_" + photo.imgid).show();
        }
    });
}
function fullScreenLikePhoto()
{
    var photo = this;
    $.ajax({
        type: "post",
        url: "/photo/like",
        async: true,
        dataType: "json",
        data: {photo: JSON.stringify(photo)},
        beforeSend: function (xhr) {
            $("#like_" + photo.imgid).hide();
            $("#dislike_" + photo.imgid).show();
            var count = parseInt($("#likeCount_" + photo.imgid).text());
            count++;
            $("#likeCount_" + photo.imgid).text(count);
        },
        success: function (response) {
            if (response.code == 1)
            {
                $("#like_" + photo.imgid).hide();
                $("#dislike_" + photo.imgid).show();

            } else {
                $("#like_" + photo.imgid).show();
                $("#dislike_" + photo.imgid).hide();
                addMessageBox(1, response.message);

                var count = parseInt($("#likeCount_" + photo.imgid).text());
                count--;
                $("#likeCount_" + photo.imgid).text(count);
            }
        }
    }).error(function () {
        addMessageBox(1, "No Internet Connection");
        $("#like_" + photo.imgid).show();
        $("#dislike_" + photo.imgid).hide();
        var count = parseInt($("#likeCount_" + photo.imgid).text());
        count--;
        $("#likeCount_" + photo.imgid).text(count);
    });

}


function fullScreenDislikePhoto()
{
    var photo = this;
    $.ajax({
        type: "post",
        url: "/photo/dislike",
        async: true,
        dataType: "json",
        data: {photo: JSON.stringify(photo)},
        beforeSend: function () {
            $("#like_" + photo.imgid).show();
            $("#dislike_" + photo.imgid).hide();
            var count = parseInt($("#likeCount_" + photo.imgid).text());
            count--;
            $("#likeCount_" + photo.imgid).text(count);
        },
        success: function (response) {
            if (response.code == 1)
            {
                $("#like_" + photo.imgid).show();
                $("#dislike_" + photo.imgid).hide();
            } else {
                $("#like_" + photo.imgid).hide();
                $("#dislike_" + photo.imgid).show();
                addMessageBox(1, response.message);
                var count = parseInt($("#likeCount_" + photo.imgid).text());
                count++;
                $("#likeCount_" + photo.imgid).text(count);
            }
        }
    }).error(function(){
        addMessageBox(1, "No Internet Connection");
            $("#like_" + photo.imgid).hide();
            $("#dislike_" + photo.imgid).show();
            var count = parseInt($("#likeCount_" + photo.imgid).text());
            count++;
            $("#likeCount_" + photo.imgid).text(count);
    });
}


function fullScreenFavPhoto()
{
    var photo = this;
    $.ajax({
        type: "post",
        url: "/photo/fav",
        async: true,
        dataType: "json",
        data: {photo: JSON.stringify(photo)},
        beforeSend: function () {
            $("#fav_" + photo.imgid).hide();
            $("#unfav_" + photo.imgid).show();
            var count = parseInt($("#favCount_" + photo.imgid).text());
            count++;
            $("#favCount_" + photo.imgid).text(count);
        },
        success: function (response) {
            if (response.code == 1)
            {
                $("#fav_" + photo.imgid).hide();
                $("#unfav_" + photo.imgid).show();
            } else {
                $("#fav_" + photo.imgid).show();
                $("#unfav_" + photo.imgid).hide();
                addMessageBox(1, response.message);
                var count = parseInt($("#favCount_" + photo.imgid).text());
                count--;
                $("#favCount_" + photo.imgid).text(count);
            }
        }
    }).error(function(){
        addMessageBox(1, "No Internet Connection");
            $("#fav_" + photo.imgid).show();
            $("#unfav_" + photo.imgid).hide();
            var count = parseInt($("#favCount_" + photo.imgid).text());
            count--;
            $("#favCount_" + photo.imgid).text(count);
    });

}

function fullScreenUnfavPhoto()
{
    var photo = this;
    $.ajax({
        type: "post",
        url: "/photo/unfav",
        async: true,
        dataType: "json",
        data: {photo: JSON.stringify(photo)},
        beforeSend: function () {
            $("#fav_" + photo.imgid).show();
            $("#unfav_" + photo.imgid).hide();
            var count = parseInt($("#favCount_" + photo.imgid).text());
            count--;
            $("#favCount_" + photo.imgid).text(count);
        },
        success: function (response) {
            if (response.code == 1)
            {
                $("#fav_" + photo.imgid).show();
                $("#unfav_" + photo.imgid).hide();
            } else {
                $("#fav_" + photo.imgid).hide();
                $("#unfav_" + photo.imgid).show();
                addMessageBox(1, response.message);
                var count = parseInt($("#favCount_" + photo.imgid).text());
                count++;
                $("#favCount_" + photo.imgid).text(count);
            }
        }
    }).error(function(){
        addMessageBox(1, "No Internet Connection");
            $("#fav_" + photo.imgid).hide();
            $("#unfav_" + photo.imgid).show();
            var count = parseInt($("#favCount_" + photo.imgid).text());
            count++;
            $("#favCount_" + photo.imgid).text(count);
    });
}



function closeFullScreenPhoto()
{

    $("#fullScreenPhoto").hide();
    $(window).off("keydown", instantCloseFullScreenPhoto);
}
function instantCloseFullScreenPhoto(event)
{
    if (event.keyCode == 27) {
        closeFullScreenPhoto();
    }
//            else if (event.keyCode == 37) {
//                if (latestFullPhotoObject != null && latestFullPhotoObject.previous!=false) {
//                    $(window).off("keydown", instantCloseFullScreenPhoto);
//                    var fun = showFullScreenPhoto.bind(latestFullPhotoObject.previous)
//                    fun();
//                }
//            }else if (event.keyCode == 39 && latestFullPhotoObject.next!=false) {
//                if (latestFullPhotoObject != null) {
//                    $(window).off("keydown", instantCloseFullScreenPhoto);
//                    var fun = showFullScreenPhoto.bind(latestFullPhotoObject.next)
//                    fun();
//                }
//            }
}
function toggleMoreOption()
{
    $("#hiddenTopMoreOptionContainer").toggle();
}
function addMessageBox(type, msg) {

    var boxMsg = '<div class="messageBox ' + ((type === 0) ? "messageBoxGreen" : "messageBoxRed") + '">\
                <ul class="hl">\
                    <li>' + msg + '</li>\
                    <li class="close">&times;</li>\
                </ul>\
            </div>';


    var $boxMsg = $(boxMsg);
    $("#rightMessageBox").prepend($boxMsg);
    $boxMsg.click(function () {
        $(this).hide(500, function () {
            $(this).remove();
        })
    });
    $boxMsg.show(500).delay(3000).hide(500, function () {
        $(this).remove();
    });
}




function userAlbumScrollHandlerWithTime()
{
    clearInterval(userAlbumScrollTimer);
    userAlbumScrollTimer = setTimeout(userAlbumScrollHandler, config.scrollTimerForLoadMore);
}
function userAlbumScrollHandler() {
    var scrollTop = (document.documentElement && document.documentElement.scrollTop) || document.body.scrollTop;
    var scrollHeight = (document.documentElement && document.documentElement.scrollHeight) || document.body.scrollHeight;
    var scrolledToBottom = (scrollTop + window.innerHeight + config.scrollOffsetForLoadMore) >= scrollHeight;
    var isEnded = $("#messageFooter").css("display") == "block" ? true : false;
    if (scrolledToBottom && !isEnded)
    {
        userAlbumLoadMore();
    }
}
function userAlbumLoadMore()
{
    var photo = albumsJSON[albumsJSON.length - 1];
    if (latestPhotoSent == photo)
    {
        return;
    } else {
        latestPhotoSent = photo;
        $.ajax({
            type: "post",
            url: "/user/more",
            async: true,
            dataType: "json",
            data: {photo: JSON.stringify(photo)},
            start: function () {
                $("#loadingFooter").show();
                $("#retryFooter").hide();
            },
            success: function (response) {
                e(response)
                if (response.code == 1)
                {

                    albumsJSON.push.apply(albumsJSON, response.photos);
                    displayAlbums({data: response.photos, id: "albumContainer"});
                    if (response.photos.length == 0)
                    {
                        $(window).off("scroll", userAlbumScrollHandlerWithTime);
                        $("#messageFooter").show();
                    } else {
                        $("#messageFooter").hide();
                    }
                } else {
                    addMessageBox(1, response.message);
                }
            },
            end: function () {
                $("#loadingFooter").hide();
                latestPhotoSent = null;
            },
            failure: function () {
                $("#retryFooter").show();
            }
        });
    }
}
function userPhotoScrollHandlerWithTime()
{
    clearInterval(userPhotoScrollTimer);
    userPhotoScrollTimer = setTimeout(userPhotoScrollHandler, config.scrollTimerForLoadMore);
}
function userPhotoScrollHandler() {
    var scrollTop = (document.documentElement && document.documentElement.scrollTop) || document.body.scrollTop;
    var scrollHeight = (document.documentElement && document.documentElement.scrollHeight) || document.body.scrollHeight;
    var scrolledToBottom = (scrollTop + window.innerHeight + config.scrollOffsetForLoadMore) >= scrollHeight;
    var isEnded = $("#messageFooter").css("display") == "block" ? true : false;
    if (scrolledToBottom && !isEnded)

    {
        userPhotoLoadMore();
    }
}
function userPhotoLoadMore()
{
    var photo = photosJSON[photosJSON.length - 1];
    $.ajax({
        type: "post",
        url: "/user/more",
        async: true,
        dataType: "json",
        data: {photo: JSON.stringify(photo)},
        beforeSend: function (xhr) {
            $("#loadingFooter").show();
            $("#retryFooter").hide();
        },
        success: function (response) {

            if (response.code == 1)
            {
                photosJSON.push.apply(photosJSON, response.photos);
                displayPhotos({data: response.photos, id: "photoContainer"});
                if (response.photos.length == 0)
                {
                    $(window).off("scroll", userPhotoScrollHandlerWithTime);
                    $("#messageFooter").show();
                } else {
                    $("#messageFooter").hide();
                }
            } else {
                addMessageBox(1, response.message);
            }
        }
    }).always(function(){
        $("#loadingFooter").hide();
    }).error(function(){
        $("#retryFooter").show();
    });
}

function homeScrollHandlerWithTime()
{
    clearInterval(homeScrollTimer);
    homeScrollTimer = setTimeout(homeScrollHandler, config.scrollTimerForLoadMore);
}
function homeScrollHandler() {
    var scrollTop = (document.documentElement && document.documentElement.scrollTop) || document.body.scrollTop;
    var scrollHeight = (document.documentElement && document.documentElement.scrollHeight) || document.body.scrollHeight;
    var scrolledToBottom = (scrollTop + window.innerHeight + config.scrollOffsetForLoadMore) >= scrollHeight;
//    var isEnded = $("#messageFooter").css("display") == "block" ? true : false;
    var isEnded = $("#messageFooter").is(":visible");
    if (scrolledToBottom && !isEnded)

    {
        homeLoadMore();
    }
}
var latestPhotoSent;
function homeLoadMore()
{
    var photo = photosJSON[photosJSON.length - 1];
    if (latestPhotoSent == photo)
    {
        return false;
    } else {
        latestPhotoSent = photo;
        $.ajax({
            type: "post",
            url: "/home/more",
            async: true,
            dataType: "json",
            data: {photo: JSON.stringify(photo)},
            beforeSend: function (xhr) {
                $("#loadingFooter").show();
                $("#retryFooter").hide();
            },
            success: function (response) {
                if (response.code == 1)
                {
                    photosJSON.push.apply(photosJSON, response.photos);
                    displayPhotos({data: response.photos, id: "photoContainer"});
                    if (response.photos.length == 0)
                    {
                        $("#messageFooter").show();
                    } else {
                        $("#messageFooter").hide();
                    }
                } else {
                    addMessageBox(1, response.message);
                }

            }
        }).always(function () {
            $("#loadingFooter").hide();
            latestPhotoSent = null;
        }).error(function () {
            $("#retryFooter").show();

        });
    }
}

function hashScrollHandlerWithTime()
{
    clearInterval(hashScrollTimer);
    hashScrollTimer = setTimeout(hashScrollHandler, config.scrollTimerForLoadMore);
}
function hashScrollHandler() {
    var scrollTop = (document.documentElement && document.documentElement.scrollTop) || document.body.scrollTop;
    var scrollHeight = (document.documentElement && document.documentElement.scrollHeight) || document.body.scrollHeight;
    var scrolledToBottom = (scrollTop + window.innerHeight + config.scrollOffsetForLoadMore) >= scrollHeight;
    var isEnded = $("#messageFooter").css("display") == "block" ? true : false;
    if (scrolledToBottom && !isEnded)

    {
        hashLoadMore();
    }
}
function hashLoadMore()
{
    var photo = photosJSON[photosJSON.length - 1];
    if (latestPhotoSent == photo)
    {
        return;
    } else {
        latestPhotoSent = photo;
        $.ajax({
            type: "post",
            url: "/hash/more",
            async: true,
            dataType: "json",
            data: {photo: JSON.stringify(photo)},
            start: function () {
                $("#loadingFooter").show();
                $("#retryFooter").hide();
            },
            success: function (response) {
                if (response.code == 1)
                {
                    photosJSON.push.apply(photosJSON, response.photos);
                    displayPhotos({data: response.photos, id: "photoContainer"});
                    if (response.photos.length == 0)
                    {
                        $("#messageFooter").show();
                    } else {
                        $("#messageFooter").hide();
                    }
                } else {
                    addMessageBox(1, response.message);
                }
            },
            end: function () {
                $("#loadingFooter").hide();
                latestPhotoSent = null;
            },
            failure: function () {
                $("#retryFooter").show();
            }
        });
    }
}
function albumScrollHandlerWithTime()
{
    clearInterval(albumScrollTimer);
    albumScrollTimer = setTimeout(albumScrollHandler, config.scrollTimerForLoadMore);
}
function albumScrollHandler() {
    var scrollTop = (document.documentElement && document.documentElement.scrollTop) || document.body.scrollTop;
    var scrollHeight = (document.documentElement && document.documentElement.scrollHeight) || document.body.scrollHeight;
    var scrolledToBottom = (scrollTop + window.innerHeight + config.scrollOffsetForLoadMore) >= scrollHeight;
    var isEnded = $("#messageFooter").css("display") == "block" ? true : false;
    if (scrolledToBottom && !isEnded)

    {
        albumLoadMore();
    }
}
function albumLoadMore()
{
    var photo = photosJSON[photosJSON.length - 1];
    if (latestPhotoSent == photo)
    {
        return;
    } else {
        latestPhotoSent = photo;
        $.ajax({
            type: "post",
            url: "/album/more",
            async: true,
            dataType: "json",
            data: {photo: JSON.stringify(photo)},
            start: function () {
                $("#loadingFooter").show();
                $("#retryFooter").hide();
            },
            success: function (response) {
                if (response.code == 1)
                {
                    photosJSON.push.apply(photosJSON, response.photos);
                    displayPhotos({data: response.photos, id: "photoContainer"});
                    if (response.photos.length == 0)
                    {
                        $("#messageFooter").show();
                    } else {
                        $("#messageFooter").hide();
                    }
                } else {
                    addMessageBox(1, response.message);
                }
            },
            end: function () {
                $("#loadingFooter").hide();
                latestPhotoSent = null;
            },
            failure: function () {
                $("#retryFooter").show();
            }
        });
    }
}

function editorScrollHandlerWithTime()
{
    clearInterval(editorScrollTimer);
    editorScrollTimer = setTimeout(editorScrollHandler, config.scrollTimerForLoadMore);
}
function editorScrollHandler() {
    var scrollTop = (document.documentElement && document.documentElement.scrollTop) || document.body.scrollTop;
    var scrollHeight = (document.documentElement && document.documentElement.scrollHeight) || document.body.scrollHeight;
    var scrolledToBottom = (scrollTop + window.innerHeight + config.scrollOffsetForLoadMore) >= scrollHeight;
    var isEnded = $("#messageFooter").css("display") == "block" ? true : false;
    if (scrolledToBottom && !isEnded)
    {
        editorLoadMore();
    }
}
function editorLoadMore()
{
    var photo = photosJSON[photosJSON.length - 1];
    if (latestPhotoSent == photo)
    {
        return;
    } else {
        latestPhotoSent = photo;
        $.ajax({
            type: "post",
            url: "/editor/more",
            async: true,
            dataType: "json",
            data: {photo: JSON.stringify(photo)},
            start: function () {
                $("#loadingFooter").show();
                $("#retryFooter").hide();
            },
            success: function (response) {
                if (response.code == 1)
                {
                    photosJSON.push.apply(photosJSON, response.photos);
                    displayPhotos({data: response.photos, id: "photoContainer"});
                    if (response.photos.length == 0)
                    {
                        $("#messageFooter").show();
                    } else {
                        $("#messageFooter").hide();
                    }
                } else {
                    addMessageBox(1, response.message);
                }
            },
            end: function () {
                $("#loadingFooter").hide();
                latestPhotoSent = null;
            },
            failure: function () {
                $("#retryFooter").show();
            }
        });
    }
}
function saveUserProfileCover()
{
    var e = document.getElementById("userCoverUploadFile");
    var file = e.files[0];
    if (file.type == undefined || file.size == 0) {
        addMessageBox(1, "Unknown File");
        return;
    }
    var formdata = new FormData();
    formdata.append("cover", file);
    ajax = new XMLHttpRequest();
    ajax.upload.addEventListener("progress", profileProgressHandler, false);
    ajax.addEventListener("load", profileCompleteHandler, false);
    ajax.open("POST", "/upload/cover");
    ajax.send(formdata);
}

function profileProgressHandler(event) {
    var percent = (event.loaded / event.total) * 100;
    $("#uploadProgressBar").css("width:" + percent + "%");
}
function profileCompleteHandler(event) {
    $("#uploadProgressBar").css("width:0%");
    rawInfo = ajax.responseText;
    info = JSON.parse(ajax.responseText);
    if (info.code == 101) {
        window.location.href = "/account/login";
    } else if (info.code == 1) {
        addMessageBox(0, info.message);
    } else {
        addMessageBox(1, info.message);
    }
    $("#changeUserProfileCoverSave").hide();
}

function saveUserProfileIcon()
{
    var e = document.getElementById("userIconUploadFile");
    var file = e.files[0];
    if (file.type == undefined || file.size == 0) {
        addMessageBox(1, "Unknown File");
        return;
    }
    var formdata = new FormData();
    formdata.append("cover", file);
    ajax = new XMLHttpRequest();
    ajax.upload.addEventListener("progress", profileIconProgressHandler, false);
    ajax.addEventListener("load", profileIconCompleteHandler, false);
    ajax.open("POST", "/upload/icon");
    ajax.send(formdata);
}

function profileIconProgressHandler(event) {
    var percent = (event.loaded / event.total) * 100;
    $("#uploadProgressBar").css("width:" + percent + "%");
}
function profileIconCompleteHandler(event) {
    $("#uploadProgressBar").css("width:0%");
    rawInfo = ajax.responseText;
    info = JSON.parse(ajax.responseText);
    if (info.code == 101) {
        window.location.href = "/account/login";
    } else if (info.code == 1) {
        addMessageBox(0, info.message);
    } else {
        addMessageBox(1, info.message);
    }
    $("#changeUserProfileIconSave").hide();
}


function showPreviewProfileCover() {
    var e = document.getElementById("userCoverUploadFile");


    if (e.files && e.files[0]) {
        for (j = 0; j < e.files.length; j++) {
            var r, o, t, a = new FileReader;

            a.onload = function (a) {
                var i = a.target.result;
                d = document.createElement("img");
                d.src = i;
                r = d.width;
                o = d.height;
                t = a.size;
                $("#userProfileCoverPhoto").css("background-image:url(" + d.src + ")");
                $("#changeUserProfileCoverSave").show();
            }, a.onerror = function (e) {
                console.error("File could not be read! Code " + e.target.error.code)
            }, a.readAsDataURL(e.files[j])
        }
    }
}
function showPreviewProfileIcon() {
    var e = document.getElementById("userIconUploadFile");


    if (e.files && e.files[0]) {
        for (j = 0; j < e.files.length; j++) {
            var r, o, t, a = new FileReader;

            a.onload = function (a) {
                var i = a.target.result;
                d = document.createElement("img");
                d.src = i;
                r = d.width;
                o = d.height;
                t = a.size;
                $("#userProfileIconPhoto").css("background-image:url(" + d.src + ")");
                $("#changeUserProfileIconSave").show();
            }, a.onerror = function (e) {
                console.error("File could not be read! Code " + e.target.error.code)
            }, a.readAsDataURL(e.files[j])
        }
    }
}
function instantSearch() {
    clearInterval(searchPhotoTimer);
    searchPhotoTimer = setTimeout(searchPhoto, 20);
}
function searchPhoto()
{
    var searchTerm = $("#searchBoxInput").val();
    var searchType = $("#searchType").val();

    var str = window.location.search
    str = replaceQueryParam('search', searchTerm, str)
    str = replaceQueryParam('type', searchType.toLowerCase(), str)

//    if (history.pushState) {
//        var newurl = window.location.protocol + "//" + window.location.host + window.location.pathname + str;
//        window.history.pushState({path: newurl}, '', newurl);
//    }
//    

    $.ajax({
        type: "post",
        url: "/search/get",
        async: true,
        dataType: "json",
        data: {searchTerm: searchTerm, searchType: searchType},
        beforeSend: function (xhr) {
            $("#messageFooter").hide();
        },
        success: function (response) {
            e(response);
            if (response.code == 1)
            {
                if (response.type == "photo") {
                    photosJSON = [];
                    $("#photoContainer").html("");
                    photosJSON.push.apply(photosJSON, response.photos);
                    displayPhotos({data: response.photos, id: "photoContainer"});
                } else if (response.type == "user") {
                    $("#photoContainer").html("");
                    userJSON.push.apply(userJSON, response.users);
                    displayUser({data: response.users, id: "photoContainer"});
                }
                $("#searchQueryExecutionTime").text("Search took " + response.query_time + " seconds");
            } else {
                addMessageBox(1, response.message);
            }
        }
    });
}

function displayUser(config) {
    for (photoJSONIndex in config.data)
    {
        var info = config.data[photoJSONIndex];
        var txtTemplate = $("#userSearchTemplate").html();
        txtTemplate = txtTemplate.replace(/\{\{img\}\}/g, info.img);
        txtTemplate = txtTemplate.replace(/\{\{cover\}\}/g, info.cover);
        txtTemplate = txtTemplate.replace(/\{\{username\}\}/g, info.username);
        $("#" + config.id).append(txtTemplate);
    }
    setTimeout(checkUserCountForEmptyAndThreshold, 100);

}
function checkUserCountForEmptyAndThreshold()
{
    if ($(".userSearch").length < config.photoPerPage)
    {
        $("#messageFooter").show();
    }
}

function searchScrollHandlerWithTime()
{
    clearInterval(searchScrollTimer);
    searchScrollTimer = setTimeout(searchScrollHandler, 50);
}
function searchScrollHandler() {
    var scrollTop = (document.documentElement && document.documentElement.scrollTop) || document.body.scrollTop;
    var scrollHeight = (document.documentElement && document.documentElement.scrollHeight) || document.body.scrollHeight;
    var scrolledToBottom = (scrollTop + window.innerHeight + 200) >= scrollHeight;
    var isEnded = $("#messageFooter").css("display") == "block" ? true : false;
    if (scrolledToBottom && !isEnded)
    {
        searchLoadMore();
    }
}
function searchLoadMore()
{
    var searchType = $("#searchType").val();
    if (searchType == "photo") {
        var photo = photosJSON[photosJSON.length - 1];
    } else {
        var photo = userJSON[userJSON.length - 1];
    }
    if (latestPhotoSent == photo)
    {
        return;
    } else {
        latestPhotoSent = photo;
        $.ajax({
            type: "post",
            url: "/search/more",
            async: true,
            dataType: "json",
            data: {photo: JSON.stringify(photo)},
            start: function () {
                $("#loadingFooter").show();
                $("#retryFooter").hide();
                $("#messageFooter").hide();
            },
            success: function (response) {
                e(response);
                if (response.code == 1)
                {
                    if (response.type == "photo") {
                        photosJSON.push.apply(photosJSON, response.photos);
                        displayPhotos({data: response.photos, id: "photoContainer"});

                        if (response.photos.length == 0)
                        {
                            $("#messageFooter").show();
                        } else {
                            $("#messageFooter").hide();
                        }

                    } else if (response.type == "people") {

                        userJSON.push.apply(userJSON, response.users);
                        displayUser({data: response.users, id: "photoContainer"});


                        if (response.users.length == 0)
                        {
                            $("#messageFooter").show();
                        } else {
                            $("#messageFooter").hide();
                        }

                    }

                } else {
                    addMessageBox(1, response.message);
                }
            },
            end: function () {
                $("#loadingFooter").hide();
                latestPhotoSent = null;
            },
            failure: function () {
                $("#retryFooter").show();
            }
        });
    }
}


function replaceQueryParam(param, newval, search) {
    //http://stackoverflow.com/questions/1090948/change-url-parameters
    var regex = new RegExp("([?;&])" + param + "[^&;]*[;&]?");
    var query = search.replace(regex, "$1").replace(/&$/, '');

    return (query.length > 2 ? query + "&" : "?") + (newval ? param + "=" + newval : '');
}
