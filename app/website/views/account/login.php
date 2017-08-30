<div class="loginBoxContainerBackground min-height">
    <div class="loginBoxContainer">
        <h2 class="loginBoxLogo"><a href="/"><img src="/public/images/img/icon.png" alt="Home" align="center"/></a></h2>
        <div class="loginBox">
            <ul class="vl loginBoxList">
                <li><div class="centerText loginText pb-10">Log In</div></li>
                <li class="error-box none" id="loginBoxErrorBox"></li>

                <li><span id="errorLoginUsername" class="errorMessage"></span></li>
                <li class="pb-10 pt-10">
                    <input type="text" placeholder="type username" autofocus="true" class="loginBoxUsername" id="loginBoxUsername"/>
                </li>
                <li><span id="errorLoginPassword" class="errorMessage"></span></li>

                <li class="pb-10"> <input type="password" placeholder="type password" class="loginBoxPassword"
                           id="loginBoxPassword"/></li>
                <li class="pb-10">
                    <div class="loginBoxRememberMeContainer">
                        <label class="cursor">
                            <input type="checkbox" class="checkbox" id="loginBoxRememberMe"/>
                            Remember me on this device
                        </label>
                    </div>
                </li>

                <li class="loginBoxButtonContainer pt-10">
                    <button value="Login" 
                            class="button cta lg"
                            id="loginBoxLoginButton">
                        Login
                    </button>
                </li>
                <li class="loginBoxSignup">

                </li>
            </ul>
        </div>
        <h3 class="signupInLogin">
            <a href="/account/signup">Create new account?</a>
        </h3>
    </div>
</div>
<script data-cfasync="false" >
    var redirectto = "/";

    
    
    $("#loginBoxLoginButton").on("click", function () {
        var loginUsername = $("#loginBoxUsername").val();
        var loginPassword = $("#loginBoxPassword").val();
        var $action = $("#loginBoxLoginButton");
        var $error = $("#loginBoxErrorBox");
        
        if (loginUsername.length == 0) {
            $error.html(" * Username is required.").show();return;
        } 
        if (loginPassword.length == 0) {
            $error.text(" * Password is required.").show();return;
        } 
        $error.hide();
        
        $.ajax({
            type: "post",
            url: "/account/validate",
            async: true,
            dataType: "json",
            beforeSend: function (xhr) {
                $action.prop("disabled", true);
            },
            data: {username: loginUsername, password: loginPassword, rememberme: $("#loginBoxRememberMe").is(":checked")},

            success: function (response) {

                if (response.code == 1) {
                    window.location.href = redirectto;
                } else {
                   $error.html(response.message).show();
                }
            }
        }).always(function(){
             $action.prop("disabled", false);
        });
    });



</script>