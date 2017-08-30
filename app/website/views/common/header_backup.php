<body>
    <div class="header">
        <a href="/" class="topHomeLink"><img src="/public/images/img/icon.png" alt="Home" align="center"/></a>
        <img src="/public/images/img/menu_icon.svg" class="mobileMenuIcon" onclick="showMobileMenu()"/>
        <ul class="hl topNavLinkContainer">
            <li><a href="/hash" title="Hash Tag">Hash Tag</a></li>
            <li><a href="/editor" title="Editor Pick">Editor Pick</a></li>
        </ul>




        <ul class="hl topNavLinkContainer text-right">
            <li><form action="/search" class="">
                    <input type="search" placeholder="Search Here..." name="search" class="topSearchInput"/>

                </form></li>
            <?php if (!$data['isloggedin']) { ?>
                <li><a href="/account/login" title="login / signup">Login / Signup</a></li>
            <?php } ?>
            <li>
                <?php if ($data['isloggedin']) { ?>
                    <ul class="vl">
                        <li class="topMoreOption" onclick="toggleMoreOption()"><img src="/public/images/img/setting1.png" alt="More" align="center"/></li>
                        <li class="hiddenTopMoreOptionContainer" id="hiddenTopMoreOptionContainer">
                            <ul class="vl hiddenTopMoreOption">
                                <li><a href="/upload" ><span>Upload</span></a></li>
                                <li><a href="/user/<?php echo SessionManagement::getSession("username"); ?>" ><span>Profile</span></a></li>
                                <li><a href="/account/logout" ><span>Logout</span></a></li>
                            </ul>
                        </li>
                    </ul>
                <?php } ?>
            </li>


        </ul>

    </div>


    <div  id="rightMessageBox" class="rightMessageBox">
    </div>
    <div id="mobileMenuContainer" class="mobileMenuContainer">
        <span class="close" onclick="hideMobileMenu()">X</span>
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