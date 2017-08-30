<?php

/**
 * Created by PhpStorm.
 * User: home
 * Date: 2/15/2016
 * Time: 2:24 PM
 */
class UserDetail
{
    public $userid;
    public $username;
    public $userImage;
    public $logininfo;

    /**
     * @return mixed
     */
    public function getLogininfo()
    {
        return $this->logininfo;
    }

    /**
     * @param mixed $logininfo
     */
    public function setLogininfo($logininfo)
    {
        $this->logininfo = $logininfo;
    }

    /**
     * @return mixed
     */
    public function getUserid()
    {
        return $this->userid;
    }

    /**
     * @param mixed $userid
     */
    public function setUserid($userid)
    {
        $this->userid = $userid;
    }

    /**
     * @return mixed
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param mixed $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @return mixed
     */
    public function getUserImage()
    {
        return $this->userImage;
    }

    /**
     * @param mixed $userImage
     */
    public function setUserImage($userImage)
    {
        $this->userImage = $userImage;
    }

}