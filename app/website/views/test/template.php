<div class="photoTemplate" id="photoTemplate">
    <div class="photoContainer">
        <div class="photo" id="photo_{{id}}" style="background-image: url('/public/images/compressed/{{img}}')">

        </div>
        <div class="title">
            <div class="titleText">{{title}}</div>
            <div class="likeAndFavContainer">
                <ul class="hl">
                    <li class="likeIcon">
                        <img src="/public/images/img/like_normal.png"  id="like_{{id}}" data-id="{{id}}"/>
                        <img src="/public/images/img/like_highlight.png" id="dislike_{{id}}" data-id="{{id}}"/>
                    </li>
                    <li class="favIcon">
                        <img src="/public/images/img/fav_normal.png"  id="fav_{{id}}" data-id="{{id}}"/>
                        <img src="/public/images/img/fav_highlight.png" id="unfav_{{id}}" data-id="{{id}}"/>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="fullScreenPhotoTemplateContainer" id="fullScreenPhotoTemplate">
    <div class="fullScreenPhoto" >
        <div class="close" onclick="closeFullScreenPhoto()">X</div>
        <div class="photoHolder">
            <div class="previousFullPhoto" id="previousFullPhoto">
                <span>&lt;</span>
            </div>
            <div class="nextFullPhoto" id="nextFullPhoto">
                <span>&gt;</span>
            </div>
            <img src="/public/images/compressed/{{img}}"/>
        </div>
        <div class="photoDetailHolder">
            <div class="userInfo">
                <img src="/public/images/profile_img/{{user_img}}"/>
                <a href="/user/{{username}}">{{username}}</a>
            </div>
            <div class="title">{{title}}</div>
            <div>
                <ul class="hl likeFavViewContainer">
                    <li>
                        <ul class="hl ">
                            <li class="word" id="like_{{id}}">Like</li>
                            <li class="word likehighlight" id="dislike_{{id}}">Like</li>
                            <li class="count likehighlight" id="likeCount_{{id}}">12</li>
                        </ul>
                    </li>
                    <li>
                        <ul class="hl">
                            <li class="word" id="fav_{{id}}">Favorite</li>
                            <li class="word favhighlight" id="unfav_{{id}}">Favorite</li>
                            <li class="count favhighlight" id="favCount_{{id}}">12</li>
                        </ul>
                    </li>
                    <li>
                        <ul class="hl">
                            <li class="word">View</li><li class="count viewhighlight" id="viewCount_{{id}}">12</li>
                        </ul>
                    </li>
                </ul>
            </div>
            <div class="viewFullPhoto">
                <a href="/photo/{{url}}"><button>View Full Photo</button></a>
            </div>
            <div >
                <table class="photoDetailTable">
                    <tr><th>Name</th><th>Description</th></tr>
                    <tr><td>Album</td><td><a href="/album/{{albumurl}}" title="{{album}}">{{album}}</a></td></tr>
                    <tr><td>Width</td><td>{{width}}</td></tr>
                    <tr><td>Height</td><td>{{height}}</td></tr>
                    <tr><td>Size</td><td>{{size}}</td></tr>
                </table>
            </div>
        </div>
    </div>
</div>
<div id="albumTemplate" class="albumTemplate">
    <!--<div class="albumContainer">-->
    <div class="album">
        <div class="albumTitle">
            {{album}}
            <a href="/album/{{albumurl}}" class="seeAll">See All Photo</a>
        </div>
        <a href="/album/{{albumurl}}">
            <div class="imgage" style="background-image: url(/public/images/compressed/{{img}});"></div>
        </a>
    </div>
    <!--</div>-->
</div>