<h2 class="hashTitle">
    <!--#Hash-->
</h2>
<div id="tagextra">
    <h2 class="text-center">Popular Tags</h2>
    <div class="hashtagcontainer">
        <?php
        $hashimages = $data["popular"];
        foreach ($hashimages as $hashimage) {
            ?>
            <div>
                <a href="/hash/<?php echo $hashimage["hash"]; ?>">
                    <div class="searchhash" style="background-image: url(/public/images/thumb/<?php echo $hashimage["hashimg"]; ?>)">
                        #<?php echo $hashimage["hash"]; ?>
                    </div>
                </a>
            </div>
            <?php
        }
        ?>
    </div>
</div>
<div id="tagextra">
    <h2 class="text-center">Trending Tag</h2>
    <div class="hashtagcontainer">
        <?php
        $hashimages = $data["trending"];
        foreach ($hashimages as $hashimage) {
            ?>
            <div>
                <a href="/hash/<?php echo $hashimage["hash"]; ?>">
                    <div class="searchhash" style="background-image: url(/public/images/thumb/<?php echo $hashimage["hashimg"]; ?>)">
                        #<?php echo $hashimage["hash"]; ?>
                    </div>
                </a>
            </div>
            <?php
        }
        ?>
    </div>
</div>
<p>
    <br/>
</p>
<h2 class="text-center">All Tags</h2>

<div style="text-align: justify;">
    <?php
    $hashes = $data['hash'];

    foreach ($hashes as $h) {
        extract($h);
        ?>
        <a href="/hash/<?php echo $hash; ?>" class="hash"><?php echo $hash; ?></a>
        <?php
    }
    ?>
</div>