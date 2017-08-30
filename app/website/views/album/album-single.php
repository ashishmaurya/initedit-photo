<?php
$photo = $data["photos"];
$album_url = $data["albumname"];
get_view("common/head", ["title" => $album_url]);
get_view("common/header");
?>
<script type="text/javascript">
    var photosJSON = <?php echo json_encode($data['photos']); ?>;
    var albumScrollTimer;
    $(window).on("load", function () {
        displayPhotos({data: photosJSON, id: "photoContainer"});
        $(window).on("scroll", albumScrollHandlerWithTime);
        $("#retryFooter").on("click", albumLoadMore);
    });
</script>
<h2 class="editorPickTitle"><?php echo $data['albumname']; ?></h2>
<div style="overflow: hidden;">
    <div class="photoContainerHolder" id="photoContainer">

    </div>
</div>
<?php
get_view("common/footer");
