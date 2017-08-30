<style>
    @media screen and (max-width: 786px) {
        .homeBgImage .content h1 {
            font-size: 1.5rem;
        }
        .homeBgImage .content .thin {
            font-size: 1.1rem;
        }

    }
    @media screen and (min-width: 786px) {
        .photoContainerHolder .photoContainer:nth-child(4n+3),
        .photoContainerHolder .photoContainer:nth-child(4n+2){
            width: calc(60% - 10px);
        }
        .photoContainerHolder .photoContainer:nth-child(4n+1),
        .photoContainerHolder .photoContainer:nth-child(4n){
            width: calc(40% - 10px);
        }

        .photoContainer .photo{

            min-height: 320px;
            height: auto;
            padding-bottom: unset;
        }
        .split .startUpload{
            padding-left: 0px;
        }
    }
</style>


<div class="homeBgImage" style="background-image: url(/public/images/img/bg.jpg)">
    <div class="content">
        <h1 class="text-center thick">Home to everyone's best photo</h1>
        <div class="thin">Showcase your work and stay inspired</div>

        <div><a href="/account/signup"><button class="button-cta">Get started</button></a></div>
    </div>
</div>
<div class="split-container">
    <div class="split">
        <div class="startUpload">
            <h3>Photography enthusiasts</h3>
            <p>Share your best photos and get exposure.</p>
            <div>
                <a href="/upload" class="button-cta-small">Start uploading</a>
            </div>
        </div>
        <div class="startSearch">
            <h3>Search Photo</h3>
            <p>Find the perfect royalty-free photos on here</p>
            <div>
                <a href="/search" class="button-cta-small">Start searching</a>
            </div>
        </div>
    </div>
</div>
<div class="photos">
    <div class="text-wrap">
        <h2>A photography community unlike any other</h2>
        <p>Explore  inspiring photos, 
            connect with other enthusiasts 
            and learn more about the photography.</p>
    </div>
    <script type="text/javascript">
        var photosJSON = <?php echo json_encode($data['photos']); ?>;
        var homeScrollTimer;
        $(window).on("load", function () {
            displayPhotos({data: photosJSON, id: "photoContainer"});
            $(window).on("scroll", homeScrollHandlerWithTime);
            $("#retryFooter").on("click", homeLoadMore);
        });

    </script>
    <div class="homeContainer photoContainerHolder" id="photoContainer"></div>

    <div class="buttons text-center">
        <a href="/account/signup" class="button-cta">Sign Up Now</a>
        <a href="/recent" class="button-default">Discover More Photos</a>
    </div>
</div>
<div class="footerContainer">Initedit © All Right Reserved.</div>
