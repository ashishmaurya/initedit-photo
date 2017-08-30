<script type="text/javascript">
    var photosJSON = <?php echo json_encode($data['photos']); ?>;
    var albumScrollTimer;
    $(window).on("load", function () {
        displayPhotos({data: photosJSON, id: "photoContainer"});
        $(window).on("scroll", albumScrollHandlerWithTime);
        $("#retryFooter").on("click", albumLoadMore);
    });

    

</script>
<h2 class="editorPickTitle"><?php echo $data['albumname'];?></h2>
<div class="photoContainerHolder" id="photoContainer">

</div>
<?php

