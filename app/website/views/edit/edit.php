<?php 
$photoURL = $data["img"];

?>

<style>
    .imageEdit{
        margin: 0 auto;
        text-align: center;
        padding-top: 74px;

    }
    .imageThumb{
        margin: 0px 3px;
        display: inline-block;
    }
    .imageThumb img{
        max-width: 100px;
        margin: 0px;
        box-shadow: 0px 0px 3px #999;
        padding: 3px;
    }
    .originalImage{
        height: 70vh;
        margin: 0 auto;
    }
    .fixedHeader {
        background-color: #333;   
    }

</style>

<div class="imageEdit">
    <img class="originalImage" src="/public/images/compressed/<?php echo $photoURL;?>"/>

    <div class="filterContainer">
        <?php
        $filters = array(
            "toaster",
            "gotham", 
            "nashville", 
            "lomo", 
            "kelvin",
            "tiltShift",
            "charcoal",
            "blackandwhite",
            "noise",
            "mylomo",
            "mylomo2",
            "coollomo",
            "coolcharcoal",
            "highcontrast",
            "colorize",
            "colorize2",
            "colorize3",
            "coolcolorize",
            "fuzz");
        foreach ($filters as $filter) {
            $imgPath = "/edit/filter/?img=$photoURL&filter=". $filter;;
            ?> 
            <ul class="vl imageThumb">
                <li><img onclick="changeImage('<?php echo $imgPath;?>')" src="<?php echo $imgPath;?>"/></li>
                <li><?php echo $filter; ?>
                    <!--<a target="_blank" href="<?php echo $imgPath;?>">Open</a>-->
                </li>
            </ul>    
            <?php
        }
        ?>
    </div>
    <script>
        function changeImage(path){
            $(".originalImage").attr("src",path+"&res=compressed");
        }
    </script>
</div>