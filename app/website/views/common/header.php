<body>
    <div class="header">
        <a href="/" class="topHomeLink">
            <i class="icon icon-home <?php echo (is_page("home"))?"enabled":"";?>"></i>
        </a>
        <img src="/public/images/img/menu_icon.svg" class="mobileMenuIcon" onclick="showMobileMenu()"/>
        <ul class="hl topNavLinkContainer">
            <li><a href="/hash" title="Hash Tag" class="icon icon-hash <?php echo (is_page("hash"))?"enabled":"";?>" >
                    </a></li>
            <li><a href="/editor" title="Editor Pick" class="icon icon-editor <?php echo (is_page("editor"))?"enabled":"";?>"></a></li>
        </ul>

        <ul class="hl topNavLinkContainer text-right">
            <li><form action="/search" class="">
                    <input type="search" placeholder="Search Here..." name="search" class="topSearchInput"/>

                </form></li>
            <?php if (!is_loggedin()) { ?>
                <li><a href="/account/login" title="login / signup" class="<?php echo (is_page("account"))?"enabled":"";?>">Login / Signup</a></li>
            <?php } ?>
            <li>
                <?php if (is_loggedin()) { ?>
                    <ul class="vl">
                        <li class="topMoreOption" onclick="toggleMoreOption()">
                            <img src="/public/images/img/setting.png?" alt="More" align="center"/>
                        </li>
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
    <div class="templates none" id="templates">
        <div class="photoTemplate">
            <div class="photoContainer" itemscope itemtype ="http://schema.org/ImageObject">  
                <div class="photo" id="photo_{{id}}"
                     style="background-image: url('/public/images/compressed/{{img}}')">  
                </div>          
                <div class="title">  
                    <div class="titleText" itemprop="caption">{{title}}</div>  
                    <div class="likeAndFavContainer">             
                        <ul class="hl">                  
                            <li class="likeIcon">        
                                <img src="/public/images/img/like_normal.png" id="like_{{id}}" data-id="{{id}}">  
                                <img src="/public/images/img/like_highlight.png" id="dislike_{{id}}" data-id="{{id}}">    
                            </li>            
                            <li class="favIcon">  
                                <img src="/public/images/img/fav_normal.png" id="fav_{{id}}" data-id="{{id}}">      
                                <img src="/public/images/img/fav_highlight.png" id="unfav_{{id}}" data-id="{{id}}">  
                            </li>               
                        </ul>              
                    </div>          
                </div>     
            </div>
        </div>
    </div>
    <div class="full-screen-photo">
        <div class="img-content left" style="background-image: url('/public/images/compressed/tmp_df4f6dd3bb9f43b49d2113650d2625c4.jpg')">
            <button class="nav nav-previous">
                &lt;
            </button>
            <button class="nav nav-next">
                &gt;
            </button>
        </div>
        <div class="img-detail right">
            <div class="close" onclick="closeFullScreenImage()">&times;</div>
            <div class="loading">Loading...</div>
            <div class="content">
                <div class="photoDetailHolder">           
                    <div class="userInfo">              
                        <img src=""/>     
                        <a href="/user/"></a>  
                    </div>   
                    <div class="title"></div>  
                    <div>               
                        <ul class="hl likeFavViewContainer">     
                            <li>                 
                                <ul class="hl ">    
                                    <li class="word img-like">Like</li>   
                                    <li class="word likehighlight img-dislike" >Like</li>  
                                    <li class="count likehighlight like-count">12</li>  
                                </ul>
                                &nbsp;
                            </li>     
                            <li>     
                                <ul class="hl">    
                                    <li class="word img-fav" >Favorite</li> 
                                    <li class="word favhighlight img-unfav" >Favorite</li> 
                                    <li class="count favhighlight fav-count" >12</li>  
                                </ul> 
                                &nbsp;
                            </li>         
                            <li>          
                                <ul class="hl">   
                                    <li class="word">View</li>
                                    <li class="count viewhighlight view-count">12</li> 
                                </ul>                 
                            </li>             
                        </ul>                
                    </div>                
                    <div class="viewFullPhoto">     
                        <a href="/photo/{{url}}"><button class="button">View Full Photo</button></a>  
                    </div>
                    <div class="viewFullPhotoFilter">   
                        <a href="/edit/?img={{url}}"><button class="button">Image Filter</button></a>     
                    </div>       
                    <div>           
                        <table class="photoDetailTable">  
                            <tbody><tr><th>Name</th><th>Description</th></tr>   
                                <tr><td>Album</td><td><a  class="album-url">{{album}}</a></td></tr> 
                                <tr><td>Width</td><td class="width">{{width}}</td></tr>           
                                <tr><td>Height</td><td class="height">{{height}}</td></tr>       
                                <tr><td>Size</td><td class="size">{{size}}</td></tr>       
                            </tbody>
                        </table>     
                    </div>
                    <div class="fullPhotoColorPalatteTitle">Color Palette</div>
                    <div id="fullPhotoColorPalatte" class="fullPhotoColorPalatte"></div>     
                </div> 
            </div>
        </div>
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