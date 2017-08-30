<div class="searchBoxContainer">
    <div class="searchBoxInputContainer">

    <input type="search" 
           value="<?php echo (isset($_GET['search'])) ? $_GET['search'] : ""; ?>" 
           placeholder="Search Photos Here" onkeyup="instantSearch()" 
           class="searchBoxInput" 
           id="searchBoxInput"/>
    
    <select id="searchType" class="searchType" onchange="searchPhoto()">
        <option value="photo" <?php echo (isset($_GET['type'])) ? ($_GET['type'] == "photo") ? "selected" : "" : ""; ?>>Photo</option>
        <option value="People" <?php echo (isset($_GET['type'])) ? ($_GET['type'] == "people") ? "selected" : "" : ""; ?>>People</option>
    </select>        
    </div>
    
    <button class="searchBoxButton button cta" onclick="searchPhoto()">Search</button>
    
</div>
<div class="searchExtraInfo">
    <span id="searchQueryExecutionTime" class="searchQueryExecutionTime"></span>
</div>
<div class="photoContainerHolder" id="photoContainer"></div>
<div id="userSearchTemplate" class="userSearchTemplate">
    <div class="userSearch">
        <div class="userSearchBGImage" style="background-image: url(/public/images/profile_cover/{{cover}})"></div>
        <div class="userSearchIconImage" style="background-image: url(/public/images/profile_img/{{img}})"></div>
        <div class="userSearchName"><strong>{{username}}</strong></div>
        <div class="userSearchProfileButton"><a href="/user/{{username}}"><button class="button cta">See Profile</button></a></div>
    </div>
</div>
<script>
    var photosJSON = [];
    var userJSON = [];

    var searchScrollTimer;
    $(window).on("load", function () {
        $(window).on("scroll", searchScrollHandlerWithTime);
        $("#retryFooter").on("click", searchLoadMore);
        searchPhoto();
    });
    var searchPhotoTimer;
    
    
</script>
