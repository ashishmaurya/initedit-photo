<?php

/**
 * Created by PhpStorm.
 * User: home
 * Date: 2/18/2016
 * Time: 1:14 PM
 */
class ShortBlogPost
{
    public $id;

    public $title;
    public $time;
    public $user;
    public $commentCount;
    public $image;
    public $tagname;
    public $body;

    /**
     * @return mixed
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param mixed $body
     */
    public function setBody($body)
    {
        $this->body = $body;
    }


    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getTagname()
    {
        return $this->tagname;
    }

    /**
     * @param mixed $tagname
     */
    public function setTagname($tagname)
    {
        $this->tagname = $tagname;
    }


    /**
     * @return mixed
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param mixed $image
     */
    public function setImage($image)
    {
        $this->image = $image;
    }


    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * @param mixed $time
     */
    public function setTime($time)
    {
        $this->time = $time;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return mixed
     */
    public function getCommentCount()
    {
        return $this->commentCount;
    }

    /**
     * @param mixed $commentCount
     */
    public function setCommentCount($commentCount)
    {
        $this->commentCount = $commentCount;
    }

    public function getTitleNormalize()
    {
        $tmp = preg_replace("/[^A-Za-z0-9 \.]/", '', $this->title);
        return preg_replace("/[ \.]/", '-', $tmp);
    }

    public function getBodyFormatted()
    {

        $tmp = $this->body;
        $tmp = preg_replace('#\*{2}(.*?)\*{2}#', '<b>$1</b>', $tmp);
        $tmp = preg_replace('#\*{1}(.*?)\*{1}#', '<i>$1</i>', $tmp);
        $tmp = preg_replace('#\~{2}(.*?)\~{2}#', '<strike>$1</strike>', $tmp);
        $tmp = preg_replace('#\`{1}(.*?)\`{1}#', '<code>$1</code>', $tmp);
        $tmp = preg_replace('#\#{2}(.*?)\#{2}#', '<p class=\'longBlogBodyNote\'>$1</p>', $tmp);
        $tmp = preg_replace('#\{{4}(.*?)\}{4}#', '<p class=\'longBlogBodyHead\'>$1</p>', $tmp);
        $tmp = preg_replace_callback('#\[{2}(.*?)\]{2}#', function ($matches) {
            $image_array = explode("|", $matches[1]);
            if (!isset($$image_array[1])) {
                $image_array[1] = 300;
            }
            return "<img src='" . $image_array[0] . "' width='" . $image_array[1] . "'/>";
        }, $tmp);
        $tmp = preg_replace('#\[{1}(.*?)\]{1}#', "<a href='$1'>$1</a>", $tmp);
        $tmp = str_replace("\n", "<br/>", $tmp);
        return $tmp;
    }

}