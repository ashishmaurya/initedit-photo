function e(msg) {
    console.log(msg)
}
function _(id) {
    return document.getElementById(id);
}
function $(selector) {
    return new IniteditJSLib(selector);
}
if (!Array.from) {
    Array.from = (function () {
        var toStr = Object.prototype.toString;
        var isCallable = function (fn) {
            return typeof fn === 'function' || toStr.call(fn) === '[object Function]';
        };
        var toInteger = function (value) {
            var number = Number(value);
            if (isNaN(number)) {
                return 0;
            }
            if (number === 0 || !isFinite(number)) {
                return number;
            }
            return (number > 0 ? 1 : -1) * Math.floor(Math.abs(number));
        };
        var maxSafeInteger = Math.pow(2, 53) - 1;
        var toLength = function (value) {
            var len = toInteger(value);
            return Math.min(Math.max(len, 0), maxSafeInteger);
        };

        // The length property of the from method is 1.
        return function from(arrayLike/*, mapFn, thisArg */) {
            // 1. Let C be the this value.
            var C = this;

            // 2. Let items be ToObject(arrayLike).
            var items = Object(arrayLike);

            // 3. ReturnIfAbrupt(items).
            if (arrayLike == null) {
                throw new TypeError("Array.from requires an array-like object - not null or undefined");
            }

            // 4. If mapfn is undefined, then let mapping be false.
            var mapFn = arguments.length > 1 ? arguments[1] : void undefined;
            var T;
            if (typeof mapFn !== 'undefined') {
                // 5. else
                // 5. a If IsCallable(mapfn) is false, throw a TypeError exception.
                if (!isCallable(mapFn)) {
                    throw new TypeError('Array.from: when provided, the second argument must be a function');
                }

                // 5. b. If thisArg was supplied, let T be thisArg; else let T be undefined.
                if (arguments.length > 2) {
                    T = arguments[2];
                }
            }

            // 10. Let lenValue be Get(items, "length").
            // 11. Let len be ToLength(lenValue).
            var len = toLength(items.length);

            // 13. If IsConstructor(C) is true, then
            // 13. a. Let A be the result of calling the [[Construct]] internal method of C with an argument list containing the single item len.
            // 14. a. Else, Let A be ArrayCreate(len).
            var A = isCallable(C) ? Object(new C(len)) : new Array(len);

            // 16. Let k be 0.
            var k = 0;
            // 17. Repeat, while k < len… (also steps a - h)
            var kValue;
            while (k < len) {
                kValue = items[k];
                if (mapFn) {
                    A[k] = typeof T === 'undefined' ? mapFn(kValue, k) : mapFn.call(T, kValue, k);
                } else {
                    A[k] = kValue;
                }
                k += 1;
            }
            // 18. Let putStatus be Put(A, "length", len, true).
            A.length = len;
            // 20. Return A.
            return A;
        };
    }());
}
function IniteditJSLib(selector) {

    this.element = [];

    if (typeof selector == "string") {

        this.element = document.querySelectorAll(selector);
    } else if (typeof selector == "object") {

        if (selector.hasOwnProperty("_getStyle")) {
            COST = selector;
            e(COST);
            this.element = (Array.from(selector.element)).slice();
        } else {
            this.element[0] = selector;
        }
    }
    this.length = Array.from(this.element).length;
    this.CUSTOME = function () {
    };
//Start Private Function

    this._getStyle = function (el, styleProp) {
        var value, defaultView = (document).defaultView;
//        var value, defaultView = (el.ownerDocument || document).defaultView;
        if (defaultView && defaultView.getComputedStyle) {
            styleProp = styleProp.replace(/([A-Z])/g, "-$1").toLowerCase();
            return defaultView.getComputedStyle(el, null).getPropertyValue(styleProp);
        } else if (el.currentStyle) {
            styleProp = styleProp.replace(/\-(\w)/g, function (str, letter) {
                return letter.toUpperCase();
            });
            value = el.currentStyle[styleProp];
            if (/^\d+(em|pt|%|ex)?$/i.test(value)) {
                return (function (value) {
                    var oldLeft = el.style.left, oldRsLeft = el.runtimeStyle.left;
                    el.runtimeStyle.left = el.currentStyle.left;
                    el.style.left = value || 0;
                    value = el.style.pixelLeft + "px";
                    el.style.left = oldLeft;
                    el.runtimeStyle.left = oldRsLeft;
                    return value;
                })(value);
            }
            return value;
        }
    }

//End Private Function
//Public Function
    this.elements = function () {
        return Array.from(this.element);
    }
    this.count = function () {
        return Array.from(this.element).length;
    }
    this.height = function () {
        return this.element[0].clientHeight;
    }
    this.actualHeight = function () {
        return this.element[0].offsetHeight;
    }
    this.width = function () {
        return this.element[0].clientWidth;
    }
    this.actualWidth = function () {
        return this.element[0].offsetWidth;
    }
    this.offset = function () {

        return {top: this.element[0].offsetTop, left: this.element[0].offsetLeft};
    }
    this.scrollDown = function () {
        for (var i = 0; i < this.element.length; i++) {
            this.element[i].scrollTop = this.element[i].scrollHeight;
        }
        return this;
    }

    this.scrollTo = function () {
        for (var i = 0; i < this.element.length; i++) {
            this.element[i].scrollTop = arguments[0];
        }
        return this;
    }

    this.isChecked = function () {
        for (var i = 0; i < this.element.length; i++) {
            if (!(this.element[i].checked)) {
                return false;
            }
        }
        return true;
    }
    this.toggleCheck = function () {
        for (var i = 0; i < this.element.length; i++) {
            if ((this.element[i].checked)) {
                this.element[i].checked = false;
            } else {
                this.element[i].checked = true;
            }
        }
        return true;
    }

    this.dump = function () {
        console.log(this.element);
    }
    this.hide = function () {
        for (var i = 0; i < this.element.length; i++) {
            this.element[i].style.display = "none";
        }
        return this;
    }
    this.show = function () {
        for (var i = 0; i < this.element.length; i++) {
            this.element[i].style.display = "block";
        }
        return this;
    }
    this.toggle = function () {
        for (var i = 0; i < this.element.length; i++) {
            var tmp = this.element[i];
            if (this._getStyle(tmp, "display") == "none") {
                tmp.style.display = "block";
            } else {
                tmp.style.display = "none";
            }
        }
        return this;
    }
    //Depricated
    this.text = function () {
        if (arguments.length == 0) {
            return this.element[0].innerHTML;
        } else {
            for (var i = 0; i < this.element.length; i++) {
                var tmp = this.element[i];
                tmp.innerHTML = arguments[0];
            }
            return this;
        }
    }
    this.html = function () {
        if (arguments.length == 0) {
            return this.element[0].innerHTML;
        } else {
            for (var i = 0; i < this.element.length; i++) {
                var tmp = this.element[i];
                tmp.innerHTML = arguments[0];
            }
            return this;
        }
    }
    this.replace = function () {
        for (var i = 0; i < this.element.length; i++) {
            var tmp = this.element[i];
            tmp.outerHTML = arguments[0];
        }
        return this;
    }
    this.remove = function () {
        for (var i = 0; i < this.element.length; i++) {
            var tmp = this.element[i];
            tmp.outerHTML = "";
        }
        return this;
    }
    this.append = function () {
        for (var i = 0; i < this.element.length; i++) {
            var tmp = this.element[i];
            //tmp.innerHTML = tmp.innerHTML + arguments[0];
            if (typeof arguments[0] == "string") {
                ch = document.createElement("div");
                ch.innerHTML = arguments[0];
                tmp.appendChild(ch.firstElementChild);

            } else {
                tmp.appendChild(arguments[0]);
            }
        }
        return this;
    }
    this.appendStart = function () {
        for (var i = 0; i < this.element.length; i++) {
            var tmp = this.element[i];
            tmp.innerHTML = arguments[0] + tmp.innerHTML;
        }
        return this;
    }
    this.css = function () {
        if (arguments.length === 2) {
            for (var i = 0; i < this.element.length; i++) {
                var tmp = this.element[i];
                tmp.style.cssText += ";" + arguments[0] + ":" + arguments[1] + ";";
            }
        } else if (arguments.length === 1) {
            if (!(arguments[0].search(":") >= 0 || arguments[0].search(";") >= 0)) {
                return this._getStyle(this.element[0], arguments[0]);
            } else {
                for (var i = 0; i < this.element.length; i++) {
                    var tmp = this.element[i];
                    tmp.style.cssText += ";" + arguments[0] + ";";
                }
            }
        }
        return this;
    }

    this.toggleClass = function () {
        for (var i = 0; i < this.element.length; i++) {
            var tmp = this.element[i];
            tmp.classList.toggle(arguments[0]);
        }
        return this;
    }

    this.attr = function () {
        if (arguments.length === 2) {
            for (var i = 0; i < this.element.length; i++) {
                var tmp = this.element[i];
                tmp.setAttribute(arguments[0], arguments[1])
            }
        } else if (arguments.length === 1) {

            if (this.element.length > 0) {
                var tmp = this.element[this.element.length - 1];

                return tmp.getAttribute(arguments[0]);
            } else {
                return;
            }
        }
        return this;
    }
    this.removeAttr = function () {
        if (arguments.length === 1) {

            if (this.element.length > 0) {
                var tmp = this.element[this.element.length - 1];
                tmp.removeAttribute(arguments[0]);
            } else {
                return;
            }
        }
        return this;
    }


    this.val = function () {
        if (arguments.length === 1) {
            for (var i = 0; i < this.element.length; i++) {
                var tmp = this.element[i];
                tmp.value = arguments[0];
            }
        } else if (arguments.length === 0) {
            return this.element[0].value;
        }
        return this;
    }
    this.on = function () {
        for (var i = 0; i < this.element.length; i++) {
            var tmp = this.element[i];
            tmp.addEventListener(arguments[0], arguments[1].bind(tmp), true);
        }
        return this;
    }
    this.off = function () {
        for (var i = 0; i < this.element.length; i++) {
            var tmp = this.element[i];
            tmp.removeEventListener(arguments[0], arguments[1].bind(tmp), true);
        }
        return this;
    }
    this.click = function () {
        $(this).on("click", arguments[0]);
        return this;
    }


    this.addClass = function () {
        for (var i = 0; i < this.element.length; i++) {
            var tmp = this.element[i];
            tmp.classList.add(arguments[0]);
        }
        return this;
    }
    this.removeClass = function () {
        for (var i = 0; i < this.element.length; i++) {
            var tmp = this.element[i];
            tmp.classList.remove(arguments[0]);
        }
        return this;
    }
    this.child = function () {
        if (typeof arguments[0] === "object") {
            if (arguments[0].hasOwnProperty("CUSTOME")) {
                this.element = arguments[0].element;
            } else {
                var tmpElement = array();
                for (var i = 0; i < this.element.length; i++) {
                    var tmp = this.element[i];
                    tmpElement.push.apply(temElement, tmp.querySelectorAll(arguments[0]));
                }
                this.element = tmpElement;
            }
        } else {
            this.element = arguments[0];
        }
        return this;
    }
    this.each = function () {
        var functionname = arguments[0];
        for (var i = 0; i < this.element.length; i++) {
            var tmp = this.element[i];
            var anotherfun = functionname.bind(tmp);
            anotherfun();
        }
        return this;
    }
    this.focus = function () {

        for (var i = 0; i < this.element.length; i++) {
            var tmp = this.element[i];
            tmp.focus();
        }
        return this;
    }
    return this;
}

$.get = function () {
    console.log("A");
}

$.ajax = function () {
    var args = arguments[0];
    if (typeof args == "undefined") {
        args = {};
    }
    if (!args.hasOwnProperty("type")) {
        args.type = "get";
    }
    if (!args.hasOwnProperty("url")) {
        args.url = "/";
    }
    if (!args.hasOwnProperty("async")) {
        args.async = false;
    }
    if (!args.hasOwnProperty("data")) {
        args.data = {};
    }
    if (!args.hasOwnProperty("readystatechange")) {
        args.readystate = function () {

        };
    }
    if (!args.hasOwnProperty("success")) {
        args.success = function () {

        };
    }
    if (!args.hasOwnProperty("failure")) {
        args.failure = function () {

        };
    }
    if (!args.hasOwnProperty("start")) {
        args.start = function () {

        };
    }
    if (!args.hasOwnProperty("end")) {
        args.end = function () {

        };
    }

    if (!args.hasOwnProperty("dataType")) {
        args.dataType = "text";
    }

    if (!args.hasOwnProperty("contentType")) {
        args.contentType = "application/x-www-form-urlencoded";
    }


    args.type = args.type.toUpperCase();
    args.dataType = args.dataType.toUpperCase();
    if (args.data instanceof FormData) {

    } else {
        url = Object.keys(args.data).map(function (k) {
            return encodeURIComponent(k) + '=' + encodeURIComponent(args.data[k])
        }).join('&');
        args.data = url;
    }

    var xml = window.XMLHttpRequest ? new XMLHttpRequest : new ActiveXObject("Microsoft.XMLHTTP");

    xml.open(args.type, args.url, args.async);
    if (!(args.data instanceof FormData)) {
        xml.setRequestHeader("Content-type", args.contentType);
    }


    xml.onreadystatechange = function () {
        args.readystate(xml);
        if (xml.readyState == 4 && xml.status == 200) {
            try {
                if (args.dataType == "TEXT") {
                    args.success(xml.responseText);
                } else if (args.dataType == "HTML") {
                    args.success(xml.responseXML);
                } else if (args.dataType == "JSON") {
                    args.success(JSON.parse(xml.responseText));
                }
            } catch (exce) {
                args.failure();
                e(exce);
                e(xml);
            }
        }

        if (xml.readyState == 4 && xml.status != 200) {
            args.failure();
        }

        if (xml.readyState == 4) {
            args.end();
        }
    }
    args.start();
    xml.send(args.data);
    return xml;
}
function trim(e, t) {
    return "false" == typeof t && (t = 5), e = e.substr(0, t)
}
function changeAddr(e) {
    window.history.pushState("", "", e);

}
var config = {
    scrollOffsetForLoadMore: 300,
    scrollTimerForLoadMore: 50,
    photoPerPage: 12
}

var latestFullPhotoObject = null;
var photoTemplate, fullPhotoTemplate, albumTemplate;

photoTemplate = '<div class="photoContainer" itemscope itemtype ="http://schema.org/ImageObject">            <div class="photo" id="photo_{{id}}" style="background-image: url(\'/public/images/thumb/{{img}}\')">            </div>            <div class="title">                <div class="titleText" itemprop="caption">{{title}}</div>                <div class="likeAndFavContainer">                    <ul class="hl">                        <li class="likeIcon">                            <img src="/public/images/img/like_normal.png" id="like_{{id}}" data-id="{{id}}">                            <img src="/public/images/img/like_highlight.png" id="dislike_{{id}}" data-id="{{id}}">                        </li>                        <li class="favIcon">                            <img src="/public/images/img/fav_normal.png" id="fav_{{id}}" data-id="{{id}}">                            <img src="/public/images/img/fav_highlight.png" id="unfav_{{id}}" data-id="{{id}}">                        </li>                    </ul>                </div>            </div>        </div>';
fullPhotoTemplate = '<div class="fullScreenPhoto">            <div class="close" onclick="closeFullScreenPhoto()">X</div>            <div class="photoHolder">                <div class="previousFullPhoto" id="previousFullPhoto">                    <span>&lt;</span>                </div>                <div class="nextFullPhoto" id="nextFullPhoto">                    <span>&gt;</span>                </div>                <img src="/public/images/compressed/{{img}}">            </div>            <div class="photoDetailHolder">                <div class="userInfo">                    <img src="/public/images/profile_img/{{user_img}}">                    <a href="/user/{{username}}">{{username}}</a>                </div>                <div class="title">{{title}}</div>                <div>                    <ul class="hl likeFavViewContainer">                        <li>                            <ul class="hl ">                                <li class="word" id="like_{{id}}">Like</li>                                <li class="word likehighlight" id="dislike_{{id}}">Like</li>                                <li class="count likehighlight" id="likeCount_{{id}}">12</li>                            </ul>                        </li>                        <li>                            <ul class="hl">                                <li class="word" id="fav_{{id}}">Favorite</li>                                <li class="word favhighlight" id="unfav_{{id}}">Favorite</li>                                <li class="count favhighlight" id="favCount_{{id}}">12</li>                            </ul>                        </li>                        <li>                            <ul class="hl">                                <li class="word">View</li><li class="count viewhighlight" id="viewCount_{{id}}">12</li>                            </ul>                        </li>                    </ul>                </div>                <div class="viewFullPhoto">                    <a href="/photo/{{url}}"><button>View Full Photo</button></a>                </div>                <div>                    <table class="photoDetailTable">                        <tbody><tr><th>Name</th><th>Description</th></tr>                        <tr><td>Album</td><td><a href="/album/{{albumurl}}" title="{{album}}">{{album}}</a></td></tr>                        <tr><td>Width</td><td>{{width}}</td></tr>                        <tr><td>Height</td><td>{{height}}</td></tr>                        <tr><td>Size</td><td>{{size}}</td></tr>                    </tbody></table>                </div><div class="fullPhotoColorPalatteTitle">Color Palette</div> <div id="fullPhotoColorPalatte" class="fullPhotoColorPalatte"></div>           </div>        </div>';
albumTemplate = '<div class="album">            <div class="albumTitle">                {{album}}                <a href="/album/{{albumurl}}" class="seeAll">See All Photo</a>            </div>            <a href="/album/{{albumurl}}">                <div class="imgage" style="background-image: url(/public/images/compressed/{{img}});"></div>            </a>        </div>';

$(window).on("load", function () {
    $(window).on("click", function () {
        if ($("#hiddenTopMoreOptionContainer").is(":visible")) {
            toggleMoreOption();
        }
    })
});

function displayPhotos(config)
{
    var data = config.data;
    for (var i = 0; i < data.length; i++) {

        var photo = data[i];
        var title = photo.title;
        title = title.replace(/#(\S*)/g, '<a href="/hash/$1">\#$1</a>');

        var txtTemplate = photoTemplate;
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

function showFullScreenPhoto()
{
    var photo = this;
    $.ajax({
        type: "post",
        url: "/photo/info",
        async: true,
        dataType: "json",
        data: {photo: JSON.stringify(photo)},
        beforeSend: function (xhr) {
              $("#fullScreenPhoto").show();
        },
        success: function (response) {
            e(response);
            latestFullPhotoObject = response;
            if (response.code == 1)
            {
//                var txtTemplate = $("#fullScreenPhotoTemplate").text();
                var txtTemplate = fullPhotoTemplate;

                var title = response.title;
                title = title.replace(/#(\S*)/g, '<a href="/hash/$1">\#$1</a>');


                txtTemplate = txtTemplate.replace(/\{\{user_img\}\}/g, response.usericon);
                txtTemplate = txtTemplate.replace(/\{\{username\}\}/g, response.username);
                txtTemplate = txtTemplate.replace(/\{\{url\}\}/g, response.url);
                txtTemplate = txtTemplate.replace(/\{\{title\}\}/g, title);
                txtTemplate = txtTemplate.replace(/\{\{img\}\}/g, photo.img);
                txtTemplate = txtTemplate.replace(/\{\{id\}\}/g, photo.imgid);

                txtTemplate = txtTemplate.replace(/\{\{album\}\}/g, response.albumname);
                txtTemplate = txtTemplate.replace(/\{\{albumurl\}\}/g, response.albumurl);
                txtTemplate = txtTemplate.replace(/\{\{width\}\}/g, response.width);
                txtTemplate = txtTemplate.replace(/\{\{height\}\}/g, response.height);
                txtTemplate = txtTemplate.replace(/\{\{size\}\}/g, response.size);


                $("#fullScreenPhoto").text(txtTemplate);

                $("#likeCount_" + photo.imgid).text(response.likecount);
                $("#favCount_" + photo.imgid).text(response.favcount);
                $("#viewCount_" + photo.imgid).text(response.viewcount);

                if (response.userlike == "0")
                {
                    $("#like_" + photo.imgid).show();
                    $("#dislike_" + photo.imgid).hide();
                } else {
                    $("#like_" + photo.imgid).hide();
                    $("#dislike_" + photo.imgid).show();
                }

                if (response.userfav == "0")
                {
                    $("#fav_" + photo.imgid).show();
                    $("#unfav_" + photo.imgid).hide();
                } else {
                    $("#fav_" + photo.imgid).hide();
                    $("#unfav_" + photo.imgid).show();
                }

                $(window).on("keydown", instantCloseFullScreenPhoto);
                $("#like_" + photo.imgid).on("click", fullScreenLikePhoto.bind(photo));
                $("#dislike_" + photo.imgid).on("click", fullScreenDislikePhoto.bind(photo));
                $("#fav_" + photo.imgid).on("click", fullScreenFavPhoto.bind(photo));
                $("#unfav_" + photo.imgid).on("click", fullScreenUnfavPhoto.bind(photo));
                if (response.previous != false) {
                    $("#previousFullPhoto").show();
                    $("#previousFullPhoto").html("<span>&lt;</span>");
                    $("#previousFullPhoto span").on("click", showFullScreenPhoto.bind(response.previous));
                } else {
                    $("#previousFullPhoto").hide();
                }
                if (response.next != false) {
                    $("#nextFullPhoto").show();
                    $("#nextFullPhoto").html("<span>&gt;</span>");
                    $("#nextFullPhoto span").on("click", showFullScreenPhoto.bind(response.next));
                } else {
                    $("#nextFullPhoto").hide();
                }
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
                $("#fullPhotoColorPalatte").text(colorTxt);
            } else {
                addMessageBox(1, response.message);
            }
        },
        end: function () {

        },
        failure: function () {
            addMessageBox(1, "No Internet Connection");
            $("#fullScreenPhoto").hide();
        }
    });

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
function instantCloseFullScreenPhoto()
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
    id = "messageBox_" + (new Date()).getTime() + Math.ceil(Math.random() * 200);
    var boxMsg = '<div class="messageBox ' + ((type === 0) ? "messageBoxGreen" : "messageBoxRed") + '" id="' + id + '">\
                <ul class="hl">\
                    <li>' + msg + '</li>\
                    <li class="close" onclick="closeMessageBox(\'' + id + '\')">x</li>\
                </ul>\
            </div>';
    $("#rightMessageBox").appendStart(boxMsg);
    var msgid = {boxid: id};
    f = showMessageBox.bind(msgid);
    setTimeout(f, 0);
}
function showMessageBox() {
    $("#" + this.boxid).css("opacity:1;");
    f = hideMessageBox.bind(this);
    setTimeout(f, 3000);
}
function hideMessageBox() {
    $("#" + this.boxid).css("opacity:0;");
    f = removeMessageBox.bind(this);
    setTimeout(f, 500);
}
function removeMessageBox() {
    $("#" + this.boxid).remove();

}
function closeMessageBox(id) {
    var msgid = {boxid: id};
    f = hideMessageBox.bind(msgid);
    setTimeout(f, 0);
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
                    $(window).off("scroll", userPhotoScrollHandlerWithTime);
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
        },
        failure: function () {
            $("#retryFooter").show();
        }
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
    var isEnded = $("#messageFooter").css("display") == "block" ? true : false;
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
        return;
    } else {
        latestPhotoSent = photo;
        $.ajax({
            type: "post",
            url: "/home/more",
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
    var searchTerm = $("#searchBoxINput").val();
    var searchType = $("#searchType").val();

    var str = window.location.search
    str = replaceQueryParam('search', searchTerm, str)
    str = replaceQueryParam('type', searchType.toLowerCase(), str)

    if (history.pushState) {
        var newurl = window.location.protocol + "//" + window.location.host + window.location.pathname + str;
        window.history.pushState({path: newurl}, '', newurl);
    }

    $.ajax({
        type: "post",
        url: "/search/get",
        async: true,
        dataType: "json",
        data: {searchTerm: searchTerm, searchType: searchType},
        start: function () {
            $("#messageFooter").hide();
        },
        success: function (response) {
            e(response);
            if (response.code == 1)
            {
                if (response.type == "photo") {
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
        },
        end: function () {

        },
        failure: function () {

        }
    });
}

function displayUser(config) {
    for (photoJSONIndex in config.data)
    {
        var info = config.data[photoJSONIndex];
        var txtTemplate = $("#userSearchTemplate").text();
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
