<?php

/**
 * Created by PhpStorm.
 * User: home
 * Date: 2/11/2016
 * Time: 12:00 PM
 */
class Post
{
    public $singleDatabase;
    public $tmpUserid;
    public function __construct()
    {
        $this->singleDatabase = new Database();
        $this->tmpUserid = (SessionManagement::sessionExists("userid"))?SessionManagement::getSession("userid"):-100;
    }
    public function getShortLinkPost($id)
    {
        $linkQuery = "select title,link,userid,img,tag,time,privacy,img_available from userpostlink where linkid=:linkid";
        $this->singleDatabase->query($linkQuery);
        $this->singleDatabase->bind(":linkid",$id);
        $linkQueryData = $this->singleDatabase->single();
        $linkModel = new ShortLinkPost();
        $linkModel->setLinkid($id);
        $linkModel->setTitle($linkQueryData['title']);
        $linkModel->setLink($linkQueryData['link']);
        $linkModel->setImg($linkQueryData['img']);
        $linkModel->setImageAvailable($linkQueryData['img_available']==0?true:false);
        $linkModel->setPrivacy($linkQueryData['privacy']);
        $linkModel->setTime(strtotime($linkQueryData['time']));

        $this->singleDatabase->query("select username from usersignup where userid=:userid");
        $this->singleDatabase->bind(":userid",$linkQueryData['userid']);
        $linkModel->setUsername($this->singleDatabase->firstColumn());

        $this->singleDatabase->query("select tag from tags where tagid=:tagid");
        $this->singleDatabase->bind(":tagid",$linkQueryData['tag']);
        $linkModel->setTag($this->singleDatabase->firstColumn());

        $this->singleDatabase->query("select (select count(*) from linklike where linkid=:linkid) - (select count(*) from linkdislike where linkid=:linkid) as coun from dual");
        $this->singleDatabase->bind(":linkid",$id);
        $linkModel->setLikeCount($this->singleDatabase->firstColumn());

        $this->singleDatabase->query("select count(*) as coun from linksave where linkid=:linkid and userid=:userid");
        $this->singleDatabase->bind(":linkid",$id);
        $this->singleDatabase->bind(":userid",$this->tmpUserid);
        $linkModel->setIsSaved($this->singleDatabase->firstColumn()>0?true:false);



        $this->singleDatabase->query("select count(*) as coun from linkcomment where linkid=:linkid");
        $this->singleDatabase->bind(":linkid",$id);
        $linkModel->setCommentCount($this->singleDatabase->firstColumn());



        $this->singleDatabase->query("select count(*) as coun from linklike where linkid=:linkid and userid=:userid");
        $this->singleDatabase->bind(":linkid",$id);
        $this->singleDatabase->bind(":userid",$this->tmpUserid);
        $linkModel->setIsLiked($this->singleDatabase->firstColumn()>0?true:false);

        $this->singleDatabase->query("select count(*) as coun from linkdislike where linkid=:linkid and userid=:userid");
        $this->singleDatabase->bind(":linkid",$id);
        $this->singleDatabase->bind(":userid",$this->tmpUserid);
        $linkModel->setIsDisliked($this->singleDatabase->firstColumn()>0?true:false);





        $controller = new Controller();
        $controller->view("template/ShortLinkPostView", ["object" => $linkModel]);
    }

    public function getShortTextPost($id)
        {
            $textQuery = "select title,text,userid,tag,time,privacy from userposttext where textid=:textid";
            $this->singleDatabase->query($textQuery);
            $this->singleDatabase->bind(":textid",$id);
            $textQueryData = $this->singleDatabase->single();
            $textModel = new ShortTextPost();
            $textModel->setTextid($id);
            $textModel->setTitle($textQueryData['title']);
            $textModel->setText($textQueryData['text']);


            $textModel->setPrivacy($textQueryData['privacy']);
            $textModel->setTime(strtotime($textQueryData['time']));

            $this->singleDatabase->query("select username from usersignup where userid=:userid");
            $this->singleDatabase->bind(":userid",$textQueryData['userid']);
            $textModel->setUsername($this->singleDatabase->firstColumn());

            $this->singleDatabase->query("select tag from tags where tagid=:tagid");
            $this->singleDatabase->bind(":tagid",$textQueryData['tag']);
            $textModel->setTag($this->singleDatabase->firstColumn());

            $this->singleDatabase->query("select (select count(*) from textlike where textid=:textid) - (select count(*) from textdislike where textid=:textid) as coun from dual");
            $this->singleDatabase->bind(":textid",$id);
            $textModel->setLikeCount($this->singleDatabase->firstColumn());

            $this->singleDatabase->query("select count(*) as coun from textsave where textid=:textid and userid=:userid");
            $this->singleDatabase->bind(":textid",$id);
            $this->singleDatabase->bind(":userid",$this->tmpUserid);
            $textModel->setIsSaved($this->singleDatabase->firstColumn()>0?true:false);



            $this->singleDatabase->query("select count(*) as coun from textcomment where textid=:textid");
            $this->singleDatabase->bind(":textid",$id);
            $textModel->setCommentCount($this->singleDatabase->firstColumn());



            $this->singleDatabase->query("select count(*) as coun from textlike where textid=:textid and userid=:userid");
            $this->singleDatabase->bind(":textid",$id);
            $this->singleDatabase->bind(":userid",$this->tmpUserid);
            $textModel->setIsLiked($this->singleDatabase->firstColumn()>0?true:false);

            $this->singleDatabase->query("select count(*) as coun from textdislike where textid=:textid and userid=:userid");
            $this->singleDatabase->bind(":textid",$id);
            $this->singleDatabase->bind(":userid",$this->tmpUserid);
            $textModel->setIsDisliked($this->singleDatabase->firstColumn()>0?true:false);

            $controller = new Controller();
            $controller->view("template/ShortTextPostView", ["object" => $textModel]);
        }
    public function getLongTextPost($id)
        {
            $textQuery = "select title,text,userid,tag,time,privacy from userposttext where textid=:textid";
            $this->singleDatabase->query($textQuery);
            $this->singleDatabase->bind(":textid",$id);
            $textQueryData = $this->singleDatabase->single();
            $textModel = new ShortTextPost();
            $textModel->setTextid($id);
            $textModel->setTitle($textQueryData['title']);
            $textModel->setText($textQueryData['text']);


            $textModel->setPrivacy($textQueryData['privacy']);
            $textModel->setTime(strtotime($textQueryData['time']));

            $this->singleDatabase->query("select username from usersignup where userid=:userid");
            $this->singleDatabase->bind(":userid",$textQueryData['userid']);
            $textModel->setUsername($this->singleDatabase->firstColumn());

            $this->singleDatabase->query("select tag from tags where tagid=:tagid");
            $this->singleDatabase->bind(":tagid",$textQueryData['tag']);
            $textModel->setTag($this->singleDatabase->firstColumn());

            $this->singleDatabase->query("select (select count(*) from textlike where textid=:textid) - (select count(*) from textdislike where textid=:textid) as coun from dual");
            $this->singleDatabase->bind(":textid",$id);
            $textModel->setLikeCount($this->singleDatabase->firstColumn());

            $this->singleDatabase->query("select count(*) as coun from textsave where textid=:textid and userid=:userid");
            $this->singleDatabase->bind(":textid",$id);
            $this->singleDatabase->bind(":userid",$this->tmpUserid);
            $textModel->setIsSaved($this->singleDatabase->firstColumn()>0?true:false);



            $this->singleDatabase->query("select count(*) as coun from textcomment where textid=:textid");
            $this->singleDatabase->bind(":textid",$id);
            $textModel->setCommentCount($this->singleDatabase->firstColumn());



            $this->singleDatabase->query("select count(*) as coun from textlike where textid=:textid and userid=:userid");
            $this->singleDatabase->bind(":textid",$id);
            $this->singleDatabase->bind(":userid",$this->tmpUserid);
            $textModel->setIsLiked($this->singleDatabase->firstColumn()>0?true:false);

            $this->singleDatabase->query("select count(*) as coun from textdislike where textid=:textid and userid=:userid");
            $this->singleDatabase->bind(":textid",$id);
            $this->singleDatabase->bind(":userid",$this->tmpUserid);
            $textModel->setIsDisliked($this->singleDatabase->firstColumn()>0?true:false);

            $controller = new Controller();
            $controller->view("template/LongTextPostView", ["object" => $textModel]);
        }

}