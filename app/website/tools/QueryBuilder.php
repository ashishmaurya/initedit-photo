<?php

/**
 * Created by PhpStorm.
 * User: home
 * Date: 2/12/2016
 * Time: 1:07 PM
 */
class QueryBuilder
{
    public static function getHome($SORT_BY)
    {

        if($SORT_BY=="new"){
            $query="select id,type,time from(
		          select linkid as id,'link' as type,time from userpostlink where (privacy=0 ) and linkid
				  NOT IN
				     (select linkid from linkreport where userid=:userid) and enabled=0
		       UNION
		       select textid as id,'text' as type,time from userposttext where (privacy=0 ) and textid
			   NOT IN
			      (select textid from textreport where userid=:userid) and enabled=0
			   ) as table1
			   order by time desc
		";
        }else if($SORT_BY=="top"){

            $query="select id,type,time,dummyrate from(
		          select linkid as id,'link' as type,time,(select count(*) from linklike where linklike.linkid=userpostlink.linkid)+((select count(*) from linkcomment where linkcomment.linkid=userpostlink.linkid)+(select count(*) from linksave where linksave.linkid=userpostlink.linkid))/3 as dummyrate  from userpostlink where (privacy=0 ) and linkid
				  NOT IN
				     (select linkid from linkreport where userid=:userid) and enabled=0
		       UNION
		       select textid as id,'text' as type,time,(select count(*) from textlike where textlike.textid=userposttext.textid) as dummyrate   from userposttext where (privacy=0 ) and textid
			   NOT IN
			      (select textid from textreport where userid=:userid) and enabled=0
			   ) as table1
			   order by dummyrate desc";
        }else if($SORT_BY=="rising"){
            $query="select id,type,time,dummysort from(
		          select linkid as id,'link' as type,time, (select count(*) from linklike where linklike.linkid=userpostlink.linkid)+(select count(*) from linkcomment where linkcomment.linkid=userpostlink.linkid ) as dummysort  from userpostlink where (privacy=0 ) and linkid
				  NOT IN
				     (select linkid from linkreport where userid=:userid) and time>NOW()-INTERVAL 1 MONTH and enabled=0
		       UNION
		       select textid as id,'text' as type,time, (select count(*) from textlike where textlike.textid=userposttext.textid)+(select count(*) from textcomment where textcomment.textid=userposttext.textid ) as dummysort    from userposttext where (privacy=0 ) and textid
			   NOT IN
			      (select textid from textreport where userid=:userid) and time>NOW()-INTERVAL 1 MONTH and enabled=0
			   ) as table1
			   order by dummysort desc
		";
        }else if($SORT_BY=="hot"){
            $query="select id,type,time,dummyrate from(
		          select linkid as id,'link' as type,time,(select count(*) from linklike where linklike.linkid=userpostlink.linkid) as dummyrate  from userpostlink where (privacy=0 ) and linkid
				  NOT IN
				     (select linkid from linkreport where userid=:userid) and enabled=0
		       UNION
		       select textid as id,'text' as type,time,(select count(*) from textlike where textlike.textid=userposttext.textid) as dummyrate   from userposttext where (privacy=0 ) and textid
			   NOT IN
			      (select textid from textreport where userid=:userid) and enabled=0
			   ) as table1
			   order by dummyrate desc";
        }else if($SORT_BY=="view"){
            $query="select id,type,time,view from(
		          select linkid as id,'link' as type,time,view  from userpostlink where (privacy=0 ) and linkid
				  NOT IN
				     (select linkid from linkreport where userid=:userid) and enabled=0
		       UNION
		       select textid as id,'text' as type,time,view   from userposttext where (privacy=0 ) and textid
			   NOT IN
			      (select textid from textreport where userid=:userid)  and enabled=0
			   ) as table1
			   order by view desc
		";
        }

        return $query;
    }

    public static function getDomain($SORT_BY)
    {

        if($SORT_BY=="new"){
            $query="select id,type,time from(
		          select linkid as id,'link' as type,time from userpostlink where (privacy=0 ) and linkid
				  NOT IN
				     (select linkid from linkreport where userid=:userid) and enabled=0
		          and link like :domain
			   ) as table1
			   order by time desc
		";
        }else if($SORT_BY=="top"){

            $query="select id,type,time,dummyrate from(
		          select linkid as id,'link' as type,time,(select count(*) from linklike where linklike.linkid=userpostlink.linkid)+((select count(*) from linkcomment where linkcomment.linkid=userpostlink.linkid)+(select count(*) from linksave where linksave.linkid=userpostlink.linkid))/3 as dummyrate  from userpostlink where (privacy=0 ) and linkid
				  NOT IN
				     (select linkid from linkreport where userid=:userid) and enabled=0
		       and link like :domain
			   ) as table1
			   order by dummyrate desc";
        }else if($SORT_BY=="rising"){
            $query="select id,type,time,dummysort from(
		          select linkid as id,'link' as type,time, (select count(*) from linklike where linklike.linkid=userpostlink.linkid)+(select count(*) from linkcomment where linkcomment.linkid=userpostlink.linkid ) as dummysort  from userpostlink where (privacy=0 ) and linkid
				  NOT IN
				     (select linkid from linkreport where userid=:userid) and time>NOW()-INTERVAL 1 MONTH and enabled=0
		       and link like :domain
			   ) as table1
			   order by dummysort desc
		";
        }else if($SORT_BY=="hot"){
            $query="select id,type,time,dummyrate from(
		          select linkid as id,'link' as type,time,(select count(*) from linklike where linklike.linkid=userpostlink.linkid) as dummyrate  from userpostlink where (privacy=0 ) and linkid
				  NOT IN
				     (select linkid from linkreport where userid=:userid) and enabled=0
		        and link like :domain
			   ) as table1
			   order by dummyrate desc";
        }else if($SORT_BY=="view"){
            $query="select id,type,time,view from(
		          select linkid as id,'link' as type,time,view  from userpostlink where (privacy=0 ) and linkid
				  NOT IN
				     (select linkid from linkreport where userid=:userid) and enabled=0
		        and link like :domain
			   ) as table1
			   order by view desc
		";
        }

        return $query;
    }


    public static function getSearch($SORT_BY)
    {
        return "select id,type,time from(
		          select linkid as id,'link' as type,time from userpostlink where (privacy=0 ) and title like :search and linkid
				  NOT IN
				     (select linkid from linkreport where userid=:userid) and enabled=0
		       UNION
		       select textid as id,'text' as type,time from userposttext where (privacy=0 ) and title like :search and textid
			   NOT IN
			      (select textid from textreport where userid=:userid) and enabled=0
			   ) as table1
			   order by time desc
		";
    }

    public static function getProfile($MENU)
    {
        $query = "select * from usersignup where 1=0";
        if($MENU=="overview") {
            $query = "select id,type,time from(
		          select linkid as id,'link' as type,time from userpostlink where (privacy=0 ) and linkid
				  NOT IN
				     (select linkid from linkreport where userid=:userid) and enabled=0 and userid=:profileuserid
		       UNION
		       select textid as id,'text' as type,time from userposttext where (privacy=0 ) and textid
			   NOT IN
			      (select textid from textreport where userid=:userid) and enabled=0 and userid=:profileuserid
			   ) as table1
			   order by time desc
		";
        }else if($MENU=="likes") {
            $query = "select id,type,time from(
		          select linkid as id,'link' as type,time  from linklike where linkid
				  NOT IN
				     (select linkid from linkreport where userid=:userid) and userid=:profileuserid

		       UNION
		       select textid as id,'text' as type,time  from textlike where  textid
			   NOT IN
			      (select textid from textreport where userid=:userid) and userid=:profileuserid
			   ) as table1
			   order by time desc";
        }else if($MENU=="comment") {
            $query = "select DISTINCT id,type from (select  id,type,time from(
		          select DISTINCT linkid as id,'link' as type,time  from linkcomment where linkid
				  NOT IN
				     (select linkid from linkreport where userid=:userid)
					  and userid=:profileuserid

		       UNION

		       select DISTINCT textid as id,'text' as type,time  from textcomment where  textid
			   NOT IN
			      (select textid from textreport where userid=:userid)
				   and userid=:profileuserid
			   )
			    as table1 order by time desc
				) as table2 where id>0";
        }else if($MENU=="dislikes") {
            $query = "select id,type,time from(
		          select linkid as id,'link' as type,time  from linkdislike where linkid
				  NOT IN
				     (select linkid from linkreport where userid=:userid) and userid=:profileuserid

		       UNION
		       select textid as id,'text' as type,time  from textdislike where  textid
			   NOT IN
			      (select textid from textreport where userid=:userid) and userid=:profileuserid
			   ) as table1
			   order by time desc";
        }else if($MENU=="saved") {
            $query = "select id,type,time from(
		          select linkid as id,'link' as type,time  from linksave where linkid
				  NOT IN
				     (select linkid from linkreport where userid=:userid) and linkid IN
					 (select linkid from userpostlink  where (privacy=0 or (privacy<>0 and userid=:profileuserid)))

		       UNION
		       select textid as id,'text' as type,time  from textsave where  textid
			   NOT IN
			      (select textid from textreport where userid=:userid) and textid IN
				  (select textid from userposttext where (privacy=0 or (privacy<>0 and userid=:profileuserid)))
			   ) as table1
			   order by time desc";
        }else if($MENU=="private") {
            $query = "select id,type,time from(
		          select linkid as id,'link' as type,time  from userpostlink where enabled=0 and privacy=1 and linkid
				  NOT IN
				     (select linkid from linkreport where userid=:userid) and userid=:profileuserid

		       UNION
		       select textid as id,'text' as type,time  from userposttext where enabled=0 and privacy=1 and textid
			   NOT IN
			      (select textid from textreport where userid=:userid) and userid=:profileuserid
			   ) as table1
			   order by time desc";
        }
        return $query;
    }
    public static function getBlog()
    {
        return "select * from blog order by time desc";
    }
    public static function getBlogSingle()
    {
        return "select * from blog where blogid=:blogid";
    }
}