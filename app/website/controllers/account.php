<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of account
 *
 * @author home
 */
class account extends Controller{
     public function index()
    {
//       Sent 404 Error
    }
    public function login()
    {
        $this->view("common/head", ["title" => "Login - Initedit Photo","description"=>"Main Page"]);
        $this->view("common/header", ["isloggedin" => SessionManagement::sessionExists("userid")]);
        $this->view("common/container_start");
        $this->view("account/login");
        $this->view("common/container_end");
        $this->view("common/footer");
    }
    private function logoutUser()
    {
        SessionManagement::sessionStart();
        SessionManagement::removeSession("userid");
        SessionManagement::removeSession("username");
        SessionManagement::removeSession("img");
        SessionManagement::removeSession("logininfo");

        if(CookieManagment::getCookie("remember")=="true")
        {
            CookieManagment::removeCookie("loginInfo");
            CookieManagment::setCookie("remember","false");
        }
    }
    public function logout()
    {
        $this->logoutUser();
        header("Location: /");
    }
     public function signup()
    {
         $this->view("common/head", ["title" => "Signup | Create New Account - Initedit Photo","description"=>"Main Page"]);
        $this->view("common/header", ["isloggedin" => SessionManagement::sessionExists("userid")]);
        $this->view("common/container_start");
        $this->view("account/signup");
        $this->view("common/container_end");
        $this->view("common/footer");
     }
     
     function captcha($id = "123456")
    {
        global $image;
        $image = imagecreatetruecolor(200, 50) or die("Cannot Initialize new GD image stream");
        $background_color = imagecolorallocate($image, 255, 255, 255);
        $text_color = imagecolorallocate($image, 0, 255, 255);
        $line_color = imagecolorallocate($image, 64, 64, 64);
        $pixel_color = imagecolorallocate($image, 0, 0, 255);
        imagefilledrectangle($image, 0, 0, 200, 50, $background_color);
        for ($i = 0; $i < 2; $i++) {
            imageline($image, 0, rand() % 50, 200, rand() % 50, $line_color);
        }
        for ($i = 0; $i < 100; $i++) {
            imagesetpixel($image, rand() % 200, rand() % 50, $pixel_color);
        }
        $letters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz123456789';
        $len = strlen($letters);
        $letter = $letters[rand(0, $len - 1)];
        $text_color = imagecolorallocate($image, 0, 0, 0);
        $word = "";
        $font = '/usr/share/fonts/truetypearial.ttf';
        for ($i = 0; $i < 1; $i++) {
            $letter = $letters[rand(0, $len - 1)];
            imagestring($image, 100, 5 + ($i * 60)+100, 20, $letter, $text_color);
//            imagettftext($image, 20, 0, 11, 20, $pixel_color, $font, $letter);
            
            $word .= $letter;
        }
        SessionManagement::sessionStart();
        SessionManagement::setSession("cap_" . $id, $word);
        header('Content-Type: image/jpeg');
        imagepng($image);
    }

     public function add()
    {
        $result = ["code" => 100, "message" => "Username or password is wrong"];
        $username = $_POST['username'];
        $password = $_POST['password'];
        $confirmPassword = $_POST['confirmPassword'];
        $captcha = $_POST['captcha'];
        $captchaId = $_POST['captchaId'];
        SessionManagement::sessionStart();
        
        if (!preg_match('/^[A-Za-z][A-Za-z0-9\._]{2,31}$/', $username)) {
            $result = ["code" => 101, "message" => "Username is not valid."];
        } else if (!preg_match('/^[A-Za-z][A-Za-z0-9\._@#]{2,31}$/', $password)) {
            $result = ["code" => 102, "message" => "Password is not valid.(Must be grater then 3 charecters)"];
        } else if (!preg_match('/^[A-Za-z][A-Za-z0-9\._@#]{2,31}$/', $confirmPassword)) {
            $result = ["code" => 103, "message" => "Confirm Password is not valid.(Must be grater then 3 charecters)"];
        } else if ($password !== $confirmPassword) {
            $result = ["code" => 104, "message" => "Password Didn't Match."];
        }  else if (strlen($captcha) == 0 || strlen($captchaId) == 0) {
            $result = ["code" => 105, "message" => "Captcha is not valid."];
        } else if (!SessionManagement::sessionExists("cap_" . $captchaId)) {
            $result = ["code" => 106, "message" => "Try Again....<br/>Captcha is not valid.", "Session" => $_SESSION];
        } else if (SessionManagement::getSession("cap_" . $captchaId) != $captcha) {
            $result = ["code" => 107, "message" => "Try Again....<br/>Captcha is wrong.", "Session" => $_SESSION];
        } else if (in_array(strtolower($username), Settings::getPrivateUser())) {
            $result = ["code" => 107, "message" => "Username is not available."];
        } else if ($this->userExists($username)) {
            $result = ["code" => 108, "message" => "Username is already taken."];
        } else {
            $password = md5($password);
            $i = md5(rand());
            $query = "insert into usersignup(username,password,logininfo) values(:username,:password,:logininfo)";
            $this->database->query($query);
            $this->database->bind("username", $username);
            $this->database->bind("password", $password);
            $this->database->bind("logininfo", $i);
            $status = $this->database->execute();
            if ($status) {
                $result = ["code" => 1, "message" => "Signed Up Successfully."];
            } else {
                $result = ["code" => 109, "message" => "Internal Error <br/> Try Again in some time."];
            }
        }
        echo json_encode($result);
    }

    private function userExists($username)
    {
        $this->database->query("select count(*) from usersignup where username=:username");
        $this->database->bind("username", $username);
        return ($this->database->firstColumn() == "0") ? false : true;
    }
    
    
    public function validate()
    {
        $result = ["code" => 100, "message" => "Username or password is wrong"];
        $username = $_POST["username"];
        $password = $_POST["password"];
        $rememberMe = $_POST["rememberme"];
        if (!preg_match('/^[A-Za-z][A-Za-z0-9\._]{2,31}$/', $username)) {
            $result = ["code" => 101, "message" => "Username is not valid."];
        } else if (!preg_match('/^[A-Za-z][A-Za-z0-9\._@#]{2,31}$/', $password)) {
            $result = ["code" => 102, "message" => "Password is not valid.(Must be grater then 3 charecters)"];
        }else if (strlen($username) >100) {
            $result = ["code" => 110, "message" => "Username must be less then 100 charecter"];
        }else if (strlen($password) >100) {
            $result = ["code" => 110, "message" => "Password must be less then 100 charecter"];
        } else {
            $this->model("UserDetail");
            if ($this->validateUser($username, $password)) {
                $userDetailObject = $this->getUserByName($username);
                SessionManagement::sessionStart();
                SessionManagement::setSession("userid", $userDetailObject->getUserid());
                SessionManagement::setSession("img", $userDetailObject->getUserImage());
                SessionManagement::setSession("username", $userDetailObject->getUsername());
                SessionManagement::setSession("logininfo", $userDetailObject->getLogininfo());

                if($rememberMe=="true")
                {
                    CookieManagment::setCookie("loginInfo",$userDetailObject->getLogininfo());
                    CookieManagment::setCookie("remember","true");
                }else{
                    CookieManagment::setCookie("remember","false");
                }

                $result = ["code" => 1, "message" => "Successfully Logged In."];
            } else {
                $result = ["code" => 100, "message" => "Username or password is wrong."];
            }
            $result["remember"]= $rememberMe;
        }
        echo json_encode($result);
    }

    
    private function validateUser($username, $password)
    {
        $this->database->query("select count(*) from usersignup where username=:username and password=:password");
        $this->database->bind("username", $username);
        $this->database->bind("password", md5($password));
        return ($this->database->firstColumn() == 1) ? true : false;
    }
    private function getUserByName($username)
    {

        $this->database->query("select * from usersignup where username=:username");
        $this->database->bind("username", $username);
        $userDetail = $this->model("UserDetail");
        $info = $this->database->single();
        $userDetail->setUserid($info['userid']);
        $userDetail->setUsername($info['username']);
        $userDetail->setUserImage($info['img']);
        $userDetail->setLogininfo($info['logininfo']);
        return $userDetail;
    }
    
    
    
}
