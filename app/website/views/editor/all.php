<script type="text/javascript">
    var photosJSON = <?php echo json_encode($data['photos']); ?>;
    var editorScrollTimer;
    $(window).on("load", function () {
        displayPhotos({data: photosJSON, id: "photoContainer"});
        $(window).on("scroll", editorScrollHandlerWithTime);
        $("#retryFooter").on("click", editorLoadMore);
    });
</script>
<h2 class="editorPickTitle none" 
    style="background-image: url(/public/images/img/editor-pick.png?v=1.1)">
    <!--EDITOR's PICK-->
</h2>
<div class="photoContainerHolder min-height" id="photoContainer">

</div>
<?php

