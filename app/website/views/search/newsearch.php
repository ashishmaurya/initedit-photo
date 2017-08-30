<div class="searchContainer" style="background-image: url(/public/images/img/search_bg.jpg)">
    <div class="content">
        <div class="searchInputContainer">
            <input type="search" autofocus="true" id="searchBoxINput" value="<?php echo (isset($_GET['search'])) ? $_GET['search'] : ""; ?>" onkeyup="instantSearch()" class="searchInput" placeholder="search royalty-free photos on here"/>

            <select class="searchType" id="searchType" onchange="searchPhoto()">
                <option value="photo" <?php echo (isset($_GET['type'])) ? ($_GET['type'] == "photo") ? "selected" : "" : ""; ?>>Photo</option>
                <option value="People" <?php echo (isset($_GET['type'])) ? ($_GET['type'] == "people") ? "selected" : "" : ""; ?>>People</option>
            </select>
        </div>
        <br/>
        <button class="button-cta" onclick="searchPhoto()">Search</button>
    </div>
</div>
<script>
    $(window).on("load", loadedSearchPage);
    function loadedSearchPage() {
        $(".searchTopForm").hide();
        $(".fixedHeader").addClass("headerWithShade");
        $("#searchBoxINput").on("keyup", searchBoxInputKeyUp);

        function searchBoxInputKeyUp() {
            if ($(this).val() != "") {
                $(".searchContainer").addClass("searchContainerWithResult");
                $("#tagextra").hide();
                $(".searchType").addClass("searchTypeShow");
            } else {
                $(".searchContainer").removeClass("searchContainerWithResult");
                $("#tagextra").show();
                $(".searchType").removeClass("searchTypeShow");
            }
        }

        var QueryString = function () {
            // This function is anonymous, is executed immediately and 
            // the return value is assigned to QueryString!
            var query_string = {};
            var query = window.location.search.substring(1);
            var vars = query.split("&");
            for (var i = 0; i < vars.length; i++) {
                var pair = vars[i].split("=");
                // If first entry with this name
                if (typeof query_string[pair[0]] === "undefined") {
                    query_string[pair[0]] = decodeURIComponent(pair[1]);
                    // If second entry with this name
                } else if (typeof query_string[pair[0]] === "string") {
                    var arr = [query_string[pair[0]], decodeURIComponent(pair[1])];
                    query_string[pair[0]] = arr;
                    // If third or later entry with this name
                } else {
                    query_string[pair[0]].push(decodeURIComponent(pair[1]));
                }
            }
            return query_string;
        }();
        if (QueryString.search) {
            if (QueryString.search != "") {
                $(".searchContainer").addClass("searchContainerWithResult");
                $("#tagextra").hide();
                searchPhoto();
                $(".searchType").addClass("searchTypeShow");

            }
        }
    }
</script>
<div id="tagextra">
    <h2 class="text-center">Popular Tags</h2>
    <div class="hashtagcontainer">
        <?php
        $hashimages = $data["hash"];
        foreach ($hashimages as $hashimage) {
            ?>
            <div>
                <a href="/hash/<?php echo $hashimage["hash"]; ?>">
                    <div class="searchhash" style="background-image: url(/public/images/thumb/<?php echo $hashimage["hashimg"]; ?>)">
                        #<?php echo $hashimage["hash"]; ?>
                    </div>
                </a>
            </div>
            <?php
        }
        ?>
    </div>
</div>

<!-- Normal Search -->

<div class="searchExtraInfo">
    <span id="searchQueryExecutionTime" class="searchQueryExecutionTime"></span>
</div>
<div class="photoContainerHolder" id="photoContainer"></div>
<div id="userSearchTemplate" class="userSearchTemplate">
    <div class="userSearch">
        <div class="userSearchBGImage" style="background-image: url(/public/images/profile_cover/{{cover}})"></div>
        <div class="userSearchIconImage" style="background-image: url(/public/images/profile_img/{{img}})"></div>
        <div class="userSearchName"><strong>{{username}}</strong></div>
        <div class="userSearchProfileButton"><a href="/user/{{username}}"><button >See Profile</button></a></div>
    </div>
</div>
<script>
    var photosJSON = [];
    var userJSON = [];

    var searchScrollTimer;
    $(window).on("load", function () {
        $(window).on("scroll", searchScrollHandlerWithTime);
        $("#retryFooter").on("click", searchLoadMore);
        //searchPhoto();






    });
    var searchPhotoTimer;


</script>