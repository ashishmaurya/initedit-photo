<script type="text/javascript">
    var albumsJSON = <?php echo json_encode($data['albums']); ?>;
     var userAlbumScrollTimer;
    $(window).on("load", function () {
        displayAlbums({data: albumsJSON, id: "albumContainer"});
        $(window).on("scroll", userAlbumScrollHandlerWithTime);
        $("#retryFooter").on("click", userAlbumLoadMore);
    });
   
</script>
<div class="photoContainerHolder" id="albumContainer">

</div>

<?php /*
<!--<div class="albumContainer">
    <?php
    $albums = $data['albums'];
    foreach ($albums as $albumItem) {
        extract($albumItem);
        ?>
        <div class="album">
            <div class="albumTitle">
                <?php echo $album; ?>
                <a href="/album/<?php echo $albumurl; ?>" class="seeAll">See All Album</a>
            </div>
            <a href="/album/<?php echo $albumurl; ?>">
                <div class="imgage" style="background-image: url(/public/images/compressed/<?php echo $img; ?>);"></div>
            </a>

        </div>
        <?php
    }
    ?>
</div>-->
 * 
 * 
 */