<link href="/public/css/newheader.css?t=<?php echo time(); ?>" rel="stylesheet" type="text/css"/>

<div class="fixedHeader">
    <a href="/home"><img src="/public/images/img/icon_white.png" class="home-icon"/></a>
    <div class="inline">
        <a href="/hash">Hash</a>
        <a href="/editor">Editor</a>
    </div>

    <div class="right">
        <img src="/public/images/img/menu_icon_white.svg" class="mobileMenuIcon" onclick="showMobileMenu()"/>

        <form action="/search" class="searchTopForm">
            <input type="search" placeholder="Search Here..." name="search" class="topSearchInput"/>

        </form>
        <?php if (SessionManagement::sessionExists("userid")) { ?>


            <?php
            $name = SessionManagement::getSession("username");
            $img = SessionManagement::getSession("img");
            ?>
            <a href="/user/<?php echo $name; ?>">
                <img class="profileNewImage" src="/public/images/profile_img/<?php echo $img; ?>"/>
            </a>
            <ul class="vl settingCog">
                <li class="topMoreOption" onclick="toggleMoreOption()"><img src="/public/images/img/setting-white.png" alt="More" align="center"/></li>
                <li class="hiddenTopMoreOptionContainer" id="hiddenTopMoreOptionContainer">
                    <ul class="vl hiddenTopMoreOption">
                        <li><a href="/upload" >Upload</a></li>

                        <li><a href="/account/logout" >Logout</a></li>
                    </ul>
                </li>
            </ul>

        <?php } else { ?>
            <div class="loginSignup">
                <a href="/account/login">Login</a>
                <a href="/account/signup">Signup</a>
            </div>
        <?php } ?>
    </div>
</div>
<div id="mobileMenuContainer" class="mobileMenuContainer">
    <span class="close" onclick="hideMobileMenu()">
        <img src="/public/images/img/close-icon.png" alt=""/>
    </span>
    
    <h2 class="mobileMenuTitle">MENU</h2>
    <ul class="vl mobileMenu" id="mobileMenu" >
        <li><a href="/hash" title="Hash Tag">Hash Tag</a></li>
        <li><a href="/editor" title="Editor Pick">Editor Pick</a></li>
        <li><a href="/search" title="Search">Search</a></li>
        <?php if ($data['isloggedin']) { ?>
            <li><a href="/upload" ><span>Upload</span></a></li>
            <li><a href="/user/<?php echo SessionManagement::getSession("username"); ?>" ><span>Profile</span></a></li>
            <li><a href="/account/logout" ><span>Logout</span></a></li>
        <?php } ?>

        <?php if (!$data['isloggedin']) { ?>
            <li><a href="/account/login" title="login / signup">Login / Signup</a></li>
        <?php } ?>
    </ul>
</div>
<div id="fullScreenPhoto">
</div>
<div  id="rightMessageBox" class="rightMessageBox">
</div>
<script>
    function showMobileMenu()
    {

        $("#mobileMenuContainer").show();

    }
    function hideMobileMenu()
    {

        $("#mobileMenuContainer").hide();
    }
</script>