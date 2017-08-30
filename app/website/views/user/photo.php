<script type="text/javascript">
    var photosJSON = <?php echo json_encode($data['photos']); ?>;
    var userPhotoScrollTimer;
    $(window).on("load", function () {
        displayPhotos({data: photosJSON, id: "photoContainer"});
        $(window).on("scroll", userPhotoScrollHandlerWithTime);
        $("#retryFooter").on("click", userPhotoLoadMore);
    });

   
</script>
<div class="photoContainerHolder" id="photoContainer">

</div>

