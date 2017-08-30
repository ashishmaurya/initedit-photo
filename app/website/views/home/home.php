<script type="text/javascript">
    var photosJSON = <?php echo json_encode($data['photos']); ?>;
    var homeScrollTimer;
    $(window).on("load", function () {
        displayPhotos({data: photosJSON, id: "photoContainer"});
        $(window).on("scroll", homeScrollHandlerWithTime);
        $("#retryFooter").on("click",homeLoadMore);
    });
    
</script>
<div class="homeContainer photoContainerHolder" id="photoContainer"></div>