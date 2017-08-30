<div class="signupBoxContainerBackground min-height">
    <div class="signupBoxContainer">
        <h2 class="signupBoxLogo"><a href="/"><img src="/public/images/img/icon.png" alt="Home" align="center"/></a></h2>
        <div class="signupBox">
            <ul class="vl signupBoxList">
                <li><div class="centerText loginText pb-10">Sign Up</div></li>
                <li class="error-box none " id="signupBoxErrorBox"></li>
                <li><span id="errorSignupUsername" class="errorMessage"></span></li>
                <li class="pv-10">
                    <input type="text" placeholder="type username" autofocus="true" class="signupBoxUsername" id="signupBoxUsername"/>
                </li>
                <li> <span id="errorSignupPassword" class="errorMessage"></span></li>
                <li class="pv-10"><input type="password" placeholder="type password" class="signupBoxPassword" id="signupBoxPassword"/></li>
                <li> <span id="errorSignupConfirmPassword" class="errorMessage"></span></li>
                <li class="pv-10"><input type="password" placeholder="type password again" class="signupBoxPassword" id="signupBoxConfirmPassword"/></li>
                <?php if (CAPTCHA_ENABLED) { ?>
                    <li class="pv-10">
                        <div class="signupBoxCaptchaHeader">Captcha <span id="errorSignupCaptcha" class="errorMessage"></span></div>
                        <div>
                            <img src="/account/captcha/<?php echo rand(0, 1000); ?>" id='captchaimg'>
                            <span id="changeCapitcha" class="tooltip"><img src="/public/images/img/ic_captcha_refresh.png" /> </span>
                        </div>
                    </li>
                    <li> <input type="text" placeholder="Enter Captcha" class="signupBoxCaptcha" id="signupBoxCaptcha"/></li>
                <?php } ?>
                <li class="signupBoxButtonContainer pt-10">
                    <button  class="button cta lg" id="signupBoxButton"/>
                    Sign Up
                    </button>
                </li>
            </ul>
        </div>
        <h3 class="signupInLogin">
            <a href="/account/login">Already have an account?</a>
        </h3>
    </div>
</div>

<script>
    $("#changeCapitcha").on("click", changeCaptcha);

    function changeCaptcha() {
        $("#captchaimg").attr("src", $("#captchaimg").attr("src"));
    }

    $("#signupBoxButton").click(function () {
        var username = $("#signupBoxUsername").val();
        var password = $("#signupBoxPassword").val();
        var confirmPassword = $("#signupBoxConfirmPassword").val();
        var captchaText = $("#signupBoxCaptcha").val();
        var $action = $("#signupBoxButton");
        var $error = $("#signupBoxErrorBox");
        if (username.length == 0) {
            $error.html(" * Username is required.").show();
            return;
        }

        if (captchaText != undefined && captchaText.length == 0) {
            $error.html(" * Captcha is required.").show();
            return;
        }

        if (password.length == 0) {
            $error.html(" * Password is required.").show();
            return;
        }
        if (confirmPassword.length == 0) {

            $error.html(" * Confirm password is required.").show();

        } else {
            if (password !== confirmPassword)
            {
                $error.html(" * Password Didn't Match.").show();
            }
        }
        $error.hide();
        var captchaId = ($("#captchaimg").length == 0) ? ($("#captchaimg").attr("src").split("/")).pop() : "";
        $.ajax({
            type: "post",
            url: "/account/add",
            async: true,
            dataType: "json",
            beforeSend: function (xhr) {
                $action.prop("disabled", true);
            },
            data: {username: username, password: password, confirmPassword: confirmPassword, captcha: captchaText, captchaId: captchaId},
            success: function (response) {
                if (response.code == 1) {
                    addMessageBox(response.message);
                    $error.hide();
                    setTimeout(function () {
                        window.location.href = "/account/login";
                    }, 2000);
                } else {
                    $error.html(response.message).show();
                }
            }
        }).always(function () {
            $action.prop("disabled", false);
        });


    });
</script>