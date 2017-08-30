<div class="uploadProgressBar" id="uploadProgressBar"></div>
<!--<h2 class="loginBoxLogo">
    <a href="/">
        <img src="/public/images/img/icon.png" alt="Home" align="center"/></a>
</h2>-->
<div style="    background-image: url(https://photo.initedit.com/public/images/compressed/tmp_9d824a5b05a0951ca107829c6ff40c3d.jpg);
    background-size: 200%;
    background-position: center;
    opacity: 0.3;
    position: absolute;
    left: 0px
    ;
    top: 0px;">
    
</div>
<div class="uploadBox" id="uploadBoxStepOne">
    <h2 class="centerText">Upload</h2>
    <div class="uploadInputContainer">
        <input type="file" multiple="true" id="uploadphoto" onchange="previewImageAndUpload()"/>
        <div class="uploadInputContainerText">Select <br/> Or <br/> Drop Your Files Here</div>
    </div>
    <h2 class="centerText uploadInputNext">
        <button>Next</button>
    </h2>
</div>
<div class="uploadBoxStepTwo" id="uploadBoxStepTwo">
    <h2 class="centerText">Upload</h2>
    <h2>
        Album Name
        <button class="right button" onclick="showNewAlbum()">Create New Album</button>
    </h2>
    <div class="inputName">
        <div id="newalbumname" class="newalbumname">
            <input type="text" placeholder="Enter New Album Name" id="newalbumnameinput"/>
            <div class="buttonContainer">
                <button class="button cta" onclick="createNewAlbum()">Create New</button>
                <button class="button" onclick="showNewAlbum()">Close</button>
            </div>
        </div>
        <select id="albumname">
            <option value="self">Self</option>
            <?php
            foreach ($data['albums'] as $album) {
                if ($album['album'] == "self") {
                    continue;
                }
                ?>
                <option value="<?php echo $album['album']; ?>"><?php echo ucfirst($album['album']); ?></option>
            <?php } ?>
        </select>
    </div>
    <h2>Privacy</h2>
    <div>
        <select id="privacy" name="privacy">
            <option value="Public">Public</option>
            <option value="Private">Private</option>
        </select>
    </div>
    <div id="photoUploadStepTwo">

    </div>
    <div>
        <button class="button cta lg" id="uploadPhotoButton" onclick="uploadAndSave()">Upload & Save</button>
    </div>
    <div id="photoUploadStepTwoTemplate">
        <div id="photoUploadStepTwo_{{id}}">
            <div class="photoTitle" id="photoTitle_{{id}}" onclick="showPhotoDetail({{id}})">
                <div>{{title}}</div>
                <div class="removePhotoUpload" onclick="removePhotoFromUpload({{id}})">X</div>
            </div>

            <div class="photoDetail" id="photoDetail_{{id}}">
                <ul class="vl">
                    <li><input type="text" value="{{title}}" placeholder="Enter Image Title" id="photoDetailTitle_{{id}}"/></li>
                    <li class="centerText"><img src="{{img}}" class="imgUpload"></li>
                </ul>
            </div>
        </div>
    </div>
</div>
<script>
    function showNewAlbum()
    {
    $("#newalbumname").toggle();
    $("#albumname").toggle();
    }
    function createNewAlbum()
    {
    albumname = $("#newalbumnameinput").val();
    if (albumname != ""){
    $.ajax({
    type: "post",
            url: "/upload/addalbum",
            async: true,
            dataType: "json",
            data: {albumname:albumname},
            start: function () {

            },
            success: function (response) {
            if (response.code == 1){
            addMessageBox(0, response.message);
            $("#albumname").prepend("<option value='" + response.album + "'>" + response.displayname + "</option>")
                    $("#newalbumname").hide();
            $("#albumname").show();
            } else{
            addMessageBox(1, response.message);
            }
            },
            end: function () {

            }
    });
    }

    }
    function showPhotoDetail(id)
    {
    $(".photoDetail").hide();
    $("#photoDetail_" + id).toggle();
    }
    function removePhotoFromUpload(id)
    {
    $("#photoUploadStepTwo_" + id).remove();
    }
    var uploadInfo = {
    uploadedname:"",
            info:"",
            rawInfo:""
    }

    function uploadAndSave()
    {
    $("#uploadPhotoButton").attr("disabled", "disabled");
    if (uploadInfo.info == ""){
    setTimeout(uploadAndSave, 1000);
    } else{
    for (photoIndex in uploadInfo.info.photos){
    var photo = uploadInfo.info.photos[photoIndex];
    photo.title = $("#photoDetailTitle_" + photo.index).val();
    }

    var albumname = $("#albumname").val();
    var privacy = $("#privacy").val();
    var formdata = new FormData();
    formdata.append("albumname", albumname);
    formdata.append("privacy", privacy);
    formdata.append("info", JSON.stringify(uploadInfo.info.photos));
    $.ajax({
    type: "post",
            url: "/upload/add",
            async: true,
            dataType: "json",
            processData: false,
            contentType: false,
            data: formdata,
            start: function () {

            },
            success: function (response) {
            if (response.code == 1){
            addMessageBox(0, response.message);
            window.location.href = response.redirect;
            } else{
            addMessageBox(1, response.message);
            }
            },
            end: function () {
            $("#uploadPhotoButton").removeAttr("disabled", "disabled");
            }
    });
    }
    }


    function previewImageAndUpload()
    {
    var e = document.getElementById("uploadphoto");
    startPhotoUpload();
    for (j = 0; j < e.files.length; j++) {
    var file = e.files[j];
    if (file.type == undefined || file.size == 0) {
    continue;
    }
    name = file.name;
    var n = name.lastIndexOf(".");
    name = n > - 1 ? name.substr(0, n) : name;
    name = name.replace(/[^a-zA-Z0-9]/g, " ");
    txtTemplate = $("#photoUploadStepTwoTemplate").html();
    txtTemplate = txtTemplate.replace(/\{\{id\}\}/g, j);
    txtTemplate = txtTemplate.replace(/\{\{title\}\}/g, name);
//                     txtTemplate = txtTemplate.replace(/\{\{img\}\}/g, photo.imgid);

    var r, o, t, a = new FileReader;
    a.txt = txtTemplate;
    a.onload = function (a) {

    var i = a.target.result;
    d = document.createElement("img");
    d.src = i;
    r = d.width;
    o = d.height;
    t = a.size;
    a.target.txt = a.target.txt.replace(/\{\{img\}\}/g, d.src);
    appendPhotoToUI(a.target.txt);
    }, a.onerror = function (e) {
    console.error("File could not be read! Code " + e.target.error.code)
    }, a.readAsDataURL(e.files[j])





    }
    $("#uploadBoxStepOne").hide();
    $("#uploadBoxStepTwo").show();
    }

    function startPhotoUpload()
    {
    var e = document.getElementById("uploadphoto");
    $("#progressBar").show();
    $("#startUploadButton").hide();
    $("#stopUploadButton").show();
    var formdata = new FormData();
    var names = [];
    for (j = 0; j < e.files.length; j++) {
    var file = e.files[j];
    if (file.type == undefined || file.size == 0) {
    continue;
    }
    names.push("file_" + j);
    formdata.append("file_" + j, file);
    }
    formdata.append("names", names);
    ajax = new XMLHttpRequest();
    ajax.upload.addEventListener("progress", progressHandler, false);
    ajax.addEventListener("load", completeHandler, false);
    ajax.open("POST", "/upload/savedraft");
    ajax.send(formdata);
    }
    function progressHandler(event) {
    var percent = (event.loaded / event.total) * 100;
    $("#uploadProgressBar").css("width:" + percent + "%");
    }
    function completeHandler(event) {
    $("#uploadProgressBar").css("width:0%");
    uploadInfo.rawInfo = ajax.responseText;
    uploadInfo.info = JSON.parse(ajax.responseText);
    if (uploadInfo.info.code == 101){
    window.location.href = "/account/login";
    } else if (uploadInfo.info.code == 1){
    validateUIPhotoUploaded();
    } else{
    addMessageBox(1, uploadInfo.info.message)
    }
    }
    function validateUIPhotoUploaded()
    {

    for (photoIndex in uploadInfo.info.photos){
    var photo = uploadInfo.info.photos[photoIndex];
    if (photo.code == 1){
    $("#photoTitle_" + photo.index).css("background: #5fbf39;");
    } else{
    $("#photoTitle_" + photo.index).css("background: #bf5f39;");
    }
    }
    console.log(uploadInfo.info);
    }

    function appendPhotoToUI(txt)
    {
    console.log(txt)
            $("#photoUploadStepTwo").prepend(txt);
    }

</script>