<?php

class Comment
{

    static function getlinkcomment($linkid, $noOfComment = 100, $sort = "top")
    {
        if ($sort == "top") {
            $query = "select commentid,comment,userid,linkid,time as ctime,commenton,time,(select count(*) from linkcommentlike where linkcommentlike.commentid=linkcomment.commentid )-(select count(*) from linkcommentdislike where linkcommentdislike.commentid=linkcomment.commentid ) as lc  from linkcomment where linkid=:linkid order by lc DESC, time DESC limit :noofcomment";
        } else {
            $query = "select commentid,comment,userid,linkid,time as ctime,commenton,time from linkcomment where linkid=:linkid order by time DESC limit :noofcomment";
        }
        //$result=mysqli_query($con,$query);
        $controller = new Controller();

        $controller->database->query($query);
        $controller->database->bind("linkid", $linkid);
        $controller->database->bind("noofcomment", $noOfComment);


        $result = $controller->database->resultset();


        $text = "";
        //while($res=mysqli_fetch_array($result)){
        foreach ($result as $res) {
            $commenton = $res['commenton'];
            $commentid = $res['commentid'];
            if ($commenton == "") {
                $text .= Comment::linkcommentformate($res['comment'], $res['userid'], $commentid, TimeHelper::time_elapsed_string(strtotime($res['ctime'])));
            } else {
                $ex = explode(":", $res['commenton']);
                $text .= Comment::linkcommentformate($res['comment'], $res['userid'], $commentid, TimeHelper::time_elapsed_string(strtotime($res['ctime'])));
                foreach ($ex as $value) {
                    $text .= "<div class='comment'>" . Comment::getlinkcommentthread($value) . "</div>";
                }
            }
        }
        return $text;
    }

    static function getlinkcommentthread($commentid)
    {
        $query = "select commentid,comment,userid,linkid,time as ctime,commenton from linkcomment where commentid=:commentid order by time desc";
        $controller = new Controller();
        $controller->database->query($query);
        $controller->database->bind("commentid", $commentid);
        $result = $controller->database->resultset();
        $text = "";
//        while(@$res=mysqli_fetch_array($result)){
        foreach ($result as $res) {
            $commenton = $res['commenton'];
            $commentid = $res['commentid'];
            if ($commenton == "") {
                $text .= Comment::linkcommentformate($res['comment'], $res['userid'], $commentid, TimeHelper::time_elapsed_string(strtotime($res['ctime'])));
            } else {
                $ex = explode(":", $res['commenton']);
                $count = count($ex);
                $text .= Comment::linkcommentformate($res['comment'], $res['userid'], $commentid, TimeHelper::time_elapsed_string(strtotime($res['ctime'])));
                foreach ($ex as $value) {
                    $text .= "<div class='comment'>" . Comment::getlinkcommentthread($value) . "</div>";
                }
            }
        }
        return $text;
    }

    static function linkcommentformate($commentComment, $userWhoComment, $commentid, $time = "just now")
    {
        $userid = (isset($_SESSION['userid'])) ? $_SESSION['userid'] : 0;
        $query = "select count(*) as coun from linkcomment where commentid=$commentid and enabled=1";
        $controller = new Controller();
        $controller->database->query($query);
        $controller->database->bind("commentid", $commentid);
        $decider = $controller->database->firstColumn();
        if ($decider != 0) {
            $userWhoComment = "[deleted]";
            $img = "default.jpg";
        } else {
            $query = "select * from usersignup where userid=:userid";
            $controller->database->query($query);
            $controller->database->bind("userid", $userWhoComment);
            $res = $controller->database->single();
            $userWhoComment = $res['username'];
            $img = $res['img'];
        }

        $query = "select count(*) as coun from linkcomment where userid=:userid and enabled=0 and commentid=:commentid";

        $controller->database->query($query);
        $controller->database->bind("userid", $userid);
        $controller->database->bind("commentid", $commentid);
        $decider = $controller->database->firstColumn();


        if ($decider != 0) {
            $delete = "<span style='color:#03F;cursor:pointer;' onclick='fakedeletelinkcomment({$commentid})' id='deletelinkcomment{$commentid}'> delete </span>";
        } else {
            $delete = "";
        }
        $query1 = "select (select count(*)  from linkcommentlike where commentid=$commentid)-(select count(*)  from linkcommentdislike where commentid=$commentid) as coun from dual";
        $controller->database->query($query1);
        $controller->database->bind("commentid", $commentid);
        $controller->database->bind("commentid", $commentid);
        $res1 = $controller->database->single();
        $likeCount = $res1['coun'];
        $likeimg = 'style="display:block;"';
        $dislikeimg = 'style="display:block;"';

        $unlikeimg = 'style="display:none;"';
        $undislikeimg = 'style="display:none;"';


        $replyfunction = "loginButton()";
        if (isset($_SESSION['username'])) {
            $replyfunction = "fakereplylinkcomment($commentid)";
            $query1 = "select count(*) as coun  from linkcommentlike where commentid=$commentid and userid=$userid";
            $controller->database->query($query1);
            $controller->database->bind("userid", $userid);
            $controller->database->bind("commentid", $commentid);
            $decider = $controller->database->firstColumn();
            if ($decider == 1) {
                $likeimg = "";
                $unlikeimg = 'style="display:block;"';
            } else {
                $query1 = "select count(*) as coun  from linkcommentdislike where commentid=$commentid and userid=$userid";
                $controller->database->query($query1);
                $controller->database->bind("userid", $userid);
                $controller->database->bind("commentid", $commentid);
                $decider = $controller->database->firstColumn();
                if ($decider == 1) {

                    $undislikeimg = 'style="display:block;"';
                    $dislikeimg = '';

                }
            }
        }
        $commentComment = str_replace("\n", "<br/>", $commentComment);
        //$commentComment=FORMATE::comment($commentComment);
        return "<a name='linkcomment{$commentid}'></a>
<div class='userCommentBox' id='highlight{$commentid}'>
<ul class='commentLikeAndDislikeLink vl'>
<li class='linkCommentLikeContainer'>
<img id='linkcommentlikeimg{$commentid}' $likeimg src='/public/images/imgs/u.png' width='20' height='15' onclick='likeLinkComment({$commentid})'/>
<img id='linkcommentunlikeimg{$commentid}' $unlikeimg src='/public/images/imgs/u1.png' width='20' height='15' onclick='likeLinkComment({$commentid})'/>
</li>
<li class='commentLikeCount' id='commentlikecount{$commentid}'>$likeCount</li>

<li class='linkCommentDislikeContainer'>
<img id='linkcommentdislikeimg{$commentid}' $dislikeimg src='/public/images/imgs/d.png' width='20' height='15' onclick='dislikeLinkComment({$commentid})'/>
<img id='linkcommentundislikeimg{$commentid}' $undislikeimg src='/public/images/imgs/d1.png' width='20' height='15' onclick='dislikeLinkComment({$commentid})'/>
</li>

</ul>
<div class='commentUserTimeAndComment'><div><a href='/user/{$userWhoComment}' class='anchor' id='commentlinkuser{$commentid}'>$userWhoComment</a><span class='commentTime'>$time</span></div><div>$commentComment</div><div><span onclick='{$replyfunction}'  class='commentReplyDummy'>reply</span>$delete </div><div id='linkreplycomment{$commentid}' class='displaynone singleLinkReplyBox'><div><textarea rows='4' cols='10' placeholder='Type Comment...' id='replylinkcommentcontent{$commentid}'></textarea></div><div class='singleLinkReplyBoxButtons'><input type='button' value='reply' id='replylinkbutton{$commentid}' onclick='replylink({$commentid})'/><input type='button' value='close' onclick='hidefakereplylinkcomment({$commentid})'/></div></div><div id='adduserlinkreply{$commentid}' class='commentAddUserLinkReply'></div></div></div>";
    }
    static function gettextcomment($textid, $noOfComment = 100, $sort = "top")
    {
        if ($sort == "top") {
            $query = "select commentid,comment,userid,textid,time as ctime,commenton,time,(select count(*) from textcommentlike where textcommentlike.commentid=textcomment.commentid )-(select count(*) from textcommentdislike where textcommentdislike.commentid=textcomment.commentid ) as lc  from textcomment where textid=:textid order by lc DESC, time DESC limit :noofcomment";
        } else {
            $query = "select commentid,comment,userid,textid,time as ctime,commenton,time from textcomment where textid=:textid order by time DESC limit :noofcomment";
        }
        //$result=mysqli_query($con,$query);
        $controller = new Controller();

        $controller->database->query($query);
        $controller->database->bind("textid", $textid);
        $controller->database->bind("noofcomment", $noOfComment);


        $result = $controller->database->resultset();


        $text = "";
        //while($res=mysqli_fetch_array($result)){
        foreach ($result as $res) {
            $commenton = $res['commenton'];
            $commentid = $res['commentid'];
            if ($commenton == "") {
                $text .= Comment::textcommentformate($res['comment'], $res['userid'], $commentid, TimeHelper::time_elapsed_string(strtotime($res['ctime'])));
            } else {
                $ex = explode(":", $res['commenton']);
                $text .= Comment::textcommentformate($res['comment'], $res['userid'], $commentid, TimeHelper::time_elapsed_string(strtotime($res['ctime'])));
                foreach ($ex as $value) {
                    $text .= "<div class='comment'>" . Comment::gettextcommentthread($value) . "</div>";
                }
            }
        }
        return $text;
    }

    static function gettextcommentthread($commentid)
    {
        $query = "select commentid,comment,userid,textid,time as ctime,commenton from textcomment where commentid=:commentid order by time desc";
        $controller = new Controller();
        $controller->database->query($query);
        $controller->database->bind("commentid", $commentid);
        $result = $controller->database->resultset();
        $text = "";
//        while(@$res=mysqli_fetch_array($result)){
        foreach ($result as $res) {
            $commenton = $res['commenton'];
            $commentid = $res['commentid'];
            if ($commenton == "") {
                $text .= Comment::textcommentformate($res['comment'], $res['userid'], $commentid, TimeHelper::time_elapsed_string(strtotime($res['ctime'])));
            } else {
                $ex = explode(":", $res['commenton']);
                $count = count($ex);
                $text .= Comment::textcommentformate($res['comment'], $res['userid'], $commentid, TimeHelper::time_elapsed_string(strtotime($res['ctime'])));
                foreach ($ex as $value) {
                    $text .= "<div class='comment'>" . Comment::gettextcommentthread($value) . "</div>";
                }
            }
        }
        return $text;
    }

    static function textcommentformate($commentComment, $userWhoComment, $commentid, $time = "just now")
    {
        $userid = (isset($_SESSION['userid'])) ? $_SESSION['userid'] : 0;
        $query = "select count(*) as coun from textcomment where commentid=$commentid and enabled=1";
        $controller = new Controller();
        $controller->database->query($query);
        $controller->database->bind("commentid", $commentid);
        $decider = $controller->database->firstColumn();
        if ($decider != 0) {
            $userWhoComment = "[deleted]";
            $img = "default.jpg";
        } else {
            $query = "select * from usersignup where userid=:userid";
            $controller->database->query($query);
            $controller->database->bind("userid", $userWhoComment);
            $res = $controller->database->single();
            $userWhoComment = $res['username'];
            $img = $res['img'];
        }

        $query = "select count(*) as coun from textcomment where userid=:userid and enabled=0 and commentid=:commentid";

        $controller->database->query($query);
        $controller->database->bind("userid", $userid);
        $controller->database->bind("commentid", $commentid);
        $decider = $controller->database->firstColumn();


        if ($decider != 0) {
            $delete = "<span style='color:#03F;cursor:pointer;' onclick='fakedeletetextcomment({$commentid})' id='deletetextcomment{$commentid}'> delete </span>";
        } else {
            $delete = "";
        }
        $query1 = "select (select count(*)  from textcommentlike where commentid=$commentid)-(select count(*)  from textcommentdislike where commentid=$commentid) as coun from dual";
        $controller->database->query($query1);
        $controller->database->bind("commentid", $commentid);
        $controller->database->bind("commentid", $commentid);
        $res1 = $controller->database->single();
        $likeCount = $res1['coun'];
        $likeimg = 'style="display:block;"';
        $dislikeimg = 'style="display:block;"';

        $unlikeimg = 'style="display:none;"';
        $undislikeimg = 'style="display:none;"';


        $replyfunction = "loginButton()";
        if (isset($_SESSION['username'])) {
            $replyfunction = "fakereplytextcomment($commentid)";
            $query1 = "select count(*) as coun  from textcommentlike where commentid=$commentid and userid=$userid";
            $controller->database->query($query1);
            $controller->database->bind("userid", $userid);
            $controller->database->bind("commentid", $commentid);
            $decider = $controller->database->firstColumn();
            if ($decider == 1) {
                $likeimg = "";
                $unlikeimg = 'style="display:block;"';
            } else {
                $query1 = "select count(*) as coun  from textcommentdislike where commentid=$commentid and userid=$userid";
                $controller->database->query($query1);
                $controller->database->bind("userid", $userid);
                $controller->database->bind("commentid", $commentid);
                $decider = $controller->database->firstColumn();
                if ($decider == 1) {

                    $undislikeimg = 'style="display:block;"';
                    $dislikeimg = '';

                }
            }
        }
        $commentComment = str_replace("\n", "<br/>", $commentComment);
        //$commentComment=FORMATE::comment($commentComment);
        return "<a name='textcomment{$commentid}'></a>
<div class='userCommentBox' id='highlight{$commentid}'>
<ul class='commentLikeAndDislikeText vl'>
<li class='textCommentLikeContainer'>
<img id='textcommentlikeimg{$commentid}' $likeimg src='/public/images/imgs/u.png' width='20' height='15' onclick='likeTextComment({$commentid})'/>
<img id='textcommentunlikeimg{$commentid}' $unlikeimg src='/public/images/imgs/u1.png' width='20' height='15' onclick='likeTextComment({$commentid})'/>
</li>
<li class='commentLikeCount' id='commentlikecount{$commentid}'>$likeCount</li>

<li class='textCommentDislikeContainer'>
<img id='textcommentdislikeimg{$commentid}' $dislikeimg src='/public/images/imgs/d.png' width='20' height='15' onclick='dislikeTextComment({$commentid})'/>
<img id='textcommentundislikeimg{$commentid}' $undislikeimg src='/public/images/imgs/d1.png' width='20' height='15' onclick='dislikeTextComment({$commentid})'/>
</li>

</ul>
<div class='commentUserTimeAndComment'><div><a href='/user/{$userWhoComment}' class='anchor' id='commenttextuser{$commentid}'>$userWhoComment</a><span class='commentTime'>$time</span></div><div>$commentComment</div><div><span onclick='{$replyfunction}'  class='commentReplyDummy'>reply</span>$delete </div><div id='textreplycomment{$commentid}' class='displaynone singleTextReplyBox'><div><textarea rows='4' cols='10' placeholder='Type Comment...' id='replytextcommentcontent{$commentid}'></textarea></div><div class='singleTextReplyBoxButtons'><input type='button' value='reply' id='replytextbutton{$commentid}' onclick='replytext({$commentid})'/><input type='button' value='close' onclick='hidefakereplytextcomment({$commentid})'/></div></div><div id='addusertextreply{$commentid}' class='commentAddUserTextReply'></div></div></div>";
    }

    static function getblogcomment($blogid, $noOfComment = 100, $sort = "top")
    {

        if ($sort == "top") {
            $query = "select commentid,comment,userid,blogid,time as ctime,commenton,time,(select count(*) from blogcommentlike where blogcommentlike.commentid=blogcomment.commentid )-(select count(*) from blogcommentdislike where blogcommentdislike.commentid=blogcomment.commentid ) as lc  from blogcomment where blogid=:blogid order by lc DESC, time DESC limit :noofcomment";
        } else {
            $query = "select commentid,comment,userid,blogid,time as ctime,commenton,time from blogcomment where blogid=:blogid order by time DESC limit :noofcomment";
        }

        $controller = new Controller();

        $controller->database->query($query);
        $controller->database->bind("blogid", $blogid);
        $controller->database->bind("noofcomment", $noOfComment);

        $result = $controller->database->resultset();
        $text = "";
        foreach ($result as $res) {
            $commenton = $res['commenton'];
            $commentid = $res['commentid'];
            if ($commenton == "") {

                $text .= Comment::blogcommentformate($res['comment'], $res['userid'], $commentid, TimeHelper::time_elapsed_string(strtotime($res['ctime'])));
            } else {

                $ex = explode(":", $res['commenton']);
                $text .= Comment::blogcommentformate($res['comment'], $res['userid'], $commentid, TimeHelper::time_elapsed_string(strtotime($res['ctime'])));
                foreach ($ex as $value) {
                    $text .= "<div class='comment'>" . Comment::getblogcommentthread($value) . "</div>";
                }
            }
        }
        return $text;
    }

    static function getblogcommentthread($commentid)
    {
        //include_once "function.php";
        $query = "select commentid,comment,userid,blogid,time as ctime,commenton from blogcomment where commentid=:commentid order by time desc";

        $controller = new Controller();
        $controller->database->query($query);
        $controller->database->bind("commentid", $commentid);
        $result = $controller->database->resultset();

        $text = "";
        foreach ($result as $res) {
            $commenton = $res['commenton'];
            $commentid = $res['commentid'];

            if ($commenton == "") {
                $text .= Comment::blogcommentformate($res['comment'], $res['userid'], $commentid, TimeHelper::time_elapsed_string(strtotime($res['ctime'])));
            } else {
                $ex = explode(":", $res['commenton']);
                $count = count($ex);
                $text .= Comment::blogcommentformate($res['comment'], $res['userid'], $commentid, TimeHelper::time_elapsed_string(strtotime($res['ctime'])));
                foreach ($ex as $value) {
                    $text .= "<div class='comment'>" . Comment::getblogcommentthread($value) . "</div>";
                }
            }
        }
        return $text;
    }

    static function blogcommentformate($commentComment, $userWhoComment, $commentid, $time = "just now")
    {

        //include "session/session_start.php";
        $userid = (isset($_SESSION['userid'])) ? $_SESSION['userid'] : 0;;

        $query = "select count(*) as coun from blogcomment where commentid=:commentid and enabled=1";

        $controller = new Controller();
        $controller->database->query($query);
        $controller->database->bind("commentid", $commentid);
        $decider = $controller->database->firstColumn();

        if ($decider != 0) {
            $userWhoComment = "[deleted]";
            $img = "default.jpg";
        } else {
            $query = "select * from usersignup where userid=:userid";
            $controller->database->query($query);
            $controller->database->bind("userid", $userWhoComment);
            $res = $controller->database->single();
            $userWhoComment = $res['username'];
            $img = $res['img'];
        }

        $query = "select count(*) as coun from blogcomment where userid=:userid and enabled=0 and commentid=:commentid";

        $controller->database->query($query);
        $controller->database->bind("userid", $userid);
        $controller->database->bind("commentid", $commentid);
        $decider = $controller->database->firstColumn();

        if ($decider != 0) {
            $delete = "<span style='color:#03F;cursor:pointer;' onclick='fakedeleteblogcomment({$commentid})' id='deleteblogcomment{$commentid}'> delete </span>";
        } else {
            $delete = "";
        }


        $query1 = "select (select count(*)  from blogcommentlike where commentid=:commentid)-(select count(*)  from blogcommentdislike where commentid=:commentid) as coun from dual";

        $controller->database->query($query1);
        $controller->database->bind("commentid", $commentid);
        $controller->database->bind("commentid", $commentid);
        $res1 = $controller->database->single();

        $likeCount = $res1['coun'];
        $likeimg = 'style="display:block;"';
        $dislikeimg = 'style="display:block;"';

        $unlikeimg = 'style="display:none;"';
        $undislikeimg = 'style="display:none;"';

        $replyfunction = "loginButton()";
        if (isset($_SESSION['username'])) {
            $replyfunction = "fakereplyblogcomment($commentid)";
            $query1 = "select count(*) as coun  from blogcommentlike where commentid=$commentid and userid=$userid";

            $controller->database->query($query1);
            $controller->database->bind("userid", $userid);
            $controller->database->bind("commentid", $commentid);
            $decider = $controller->database->firstColumn();
            if ($decider == 1) {
                $likeimg = "";
                $unlikeimg = 'style="display:block;"';
            } else {
                $query1 = "select count(*) as coun  from blogcommentdislike where commentid=$commentid and userid=$userid";
                $controller->database->query($query1);
                $controller->database->bind("userid", $userid);
                $controller->database->bind("commentid", $commentid);
                $decider = $controller->database->firstColumn();

                if ($decider == 1) {
                    $undislikeimg = 'style="display:block;"';
                    $dislikeimg = '';

                }

            }
        }
        $commentComment = str_replace("\n", "<br/>", $commentComment);
        //$commentComment=FORMATE::comment($commentComment);
        //return "<div style='overflow:hidden;'><div style='float:left;padding-right:5px;'><div><img id='blogcommentlikeimg{$commentid}' src='/public/images/imgs/{$likeimg}' width='20' height='15' onclick='likeBlogComment({$commentid})'/></div><div style='font-size:15px;text-align:center;' id='commentlikecount{$commentid}'>$likeCount</div><div><img id='blogcommentdislikeimg{$commentid}' src='/public/images/imgs/img/{$dislikeimg}' width='20' height='15' onclick='dislikeBlogComment({$commentid})'/></div></div><div style='float:left;width:70%;font-size:15px;'><div><a href='/user/{$userWhoComment}' class='anchor' id='commentbloguser{$commentid}'>$userWhoComment</a><span style='color:#666;padding-left:35px;'>$time</span></div><div>$commentComment</div><div style=''><span onclick='{$replyfunction}' style='color:#03F;cursor:pointer;'>reply</span>$delete </div><div id='blogreplycomment{$commentid}' style='display:none;'><div><textarea rows='2' cols='10' id='replyblogcommentcontent{$commentid}'></textarea></div><div ><input type='button' value='reply' onclick='replyblog({$commentid})'/><input type='button' value='close' onclick='hidefakereplyblogcomment({$commentid})'/></div></div><div id='adduserblogreply{$commentid}' style='padding-left:35px;'></div></div></div>";
        return "<a name='blogcomment{$commentid}'></a>
<div class='userCommentBox' id='highlight{$commentid}'>
<ul class='commentLikeAndDislikeBlog vl'>
<li class='blogCommentLikeContainer'>
<img id='blogcommentlikeimg{$commentid}' $likeimg src='/public/images/imgs/u.png' width='20' height='15' onclick='likeBlogComment({$commentid})'/>
<img id='blogcommentunlikeimg{$commentid}' $unlikeimg src='/public/images/imgs/u1.png' width='20' height='15' onclick='likeBlogComment({$commentid})'/>
</li>
<li class='commentLikeCount' id='commentlikecount{$commentid}'>$likeCount</li>

<li class='blogCommentDislikeContainer'>
<img id='blogcommentdislikeimg{$commentid}' $dislikeimg src='/public/images/imgs/d.png' width='20' height='15' onclick='dislikeBlogComment({$commentid})'/>
<img id='blogcommentundislikeimg{$commentid}' $undislikeimg src='/public/images/imgs/d1.png' width='20' height='15' onclick='dislikeBlogComment({$commentid})'/>
</li>

</ul>
<div class='commentUserTimeAndComment'><div><a href='/user/{$userWhoComment}' class='anchor' id='commentbloguser{$commentid}'>$userWhoComment</a><span class='commentTime'>$time</span></div><div>$commentComment</div><div><span onclick='{$replyfunction}'  class='commentReplyDummy'>reply</span>$delete </div><div id='blogreplycomment{$commentid}' class='displaynone singleBlogReplyBox'><div><textarea rows='4' cols='10' placeholder='Type Comment...' id='replyblogcommentcontent{$commentid}'></textarea></div><div class='singleBlogReplyBoxButtons'><input type='button' value='reply' id='replyblogbutton{$commentid}' onclick='replyblog({$commentid})'/><input type='button' value='close' onclick='hidefakereplyblogcomment({$commentid})'/></div></div><div id='adduserblogreply{$commentid}' class='commentAddUserBlogReply'></div></div></div>";

    }


  


}
