<?php

function get_web_safe_url($str, $replace = array(), $delimiter = '-') {
    if (!empty($replace)) {
        $str = str_replace((array) $replace, ' ', $str);
    }

    $clean = iconv('UTF-8', 'ASCII//TRANSLIT', $str);
    $clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $clean);
    $clean = strtolower(trim($clean, '-'));
    $clean = preg_replace("/[\/_|+ -]+/", $delimiter, $clean);

    return $clean;
}
function is_page($page) {
    $url = get_input("url", "");
    $url_explode = explode("/", $url);
    $current_page = $url_explode[0];
    $current_page = empty($current_page) ? "home" : $current_page;
    return $current_page == $page;
}

function get_page() {
    $url = get_input("url", "");
    $url_explode = explode("/", $url);
    $current_page = $url_explode[0];
    $current_page = empty($current_page) ? "home" : $current_page;
    return $current_page;
}
function is_loggedin() {
    return session_exists("userid");
}

function get_session($key, $default = NULL) {
    if (SessionManagement::sessionExists($key)) {
        return SessionManagement::getSession($key);
    } else {
        return $default;
    }
}

function set_session($key, $val) {
    SessionManagement::setSession($key, $val);
}

function session_exists($key) {
    return SessionManagement::sessionExists($key);
}

function get_cookie($key, $default = NULL) {
    if (CookieManagment::cookieExists($key)) {
        return CookieManagment::getCookie($key);
    }
    return $default;
}

function set_cookie($key, $value) {
    CookieManagment::setCookie($key, $value);
    return true;
}

function cookie_exists($key) {
    return CookieManagment::cookieExists($key);
}

function remove_cookie($key) {
    CookieManagment::removeCookie($key);
    return true;
}

function current_userid() {
    return get_session("userid", -1);
}

function get_input($key, $default = NULL) {
    return isset($_GET[$key]) ? trim($_GET[$key]) : $default;
}

function post_input($key, $default = NULL) {
    return isset($_POST[$key]) ? trim($_POST[$key]) : $default;
}

function get_vars($arry, $key, $default = NULL) {
    return isset($arry[$key]) ? $arry[$key] : $default;
}

function time_elapsed_string($datetime, $full = false) {
    $now = new DateTime;
    $ago = new DateTime(date('Y/m/d H:i:s', $datetime));
    $diff = $now->diff($ago);

    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;

    $string = array(
        'y' => 'year',
        'm' => 'month',
        'w' => 'week',
        'd' => 'day',
        'h' => 'hour',
        'i' => 'minute',
        's' => 'second',
    );
    foreach ($string as $k => &$v) {
        if ($diff->$k) {
            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
        } else {
            unset($string[$k]);
        }
    }

    if (!$full)
        $string = array_slice($string, 0, 1);
    return $string ? implode(', ', $string) . ' ago' : 'just now';
}

function insert($table, $data) {
    $singleDatabase = new Database();
    $keys = array_keys($data);
    $columns = implode(",", $keys);
    $values = implode(",:", $keys);
    $values = ":" . $values;
    $query = "insert into $table($columns) values($values)";
    $singleDatabase->query($query);
    foreach ($data as $key => $value) {
        $singleDatabase->bind($key, $value);
    }
    $singleDatabase->execute();
    $lastId = $singleDatabase->lastInsertId();
    return $lastId;
}

function update($table, $data, $where = []) {
    $singleDatabase = new Database();
    $keys = array_keys($data);
    $update_column = [];
    foreach ($keys as $key => $value) {
        $update_column[] = $key . "=:" . $key;
    }
    $update_str = implode(",", $update_column);

    $keys = array_keys($where);
    $where_column = [];
    foreach ($keys as $key => $value) {
        $where_column[] = $key . "=:where" . $key;
    }
    $where_str = implode(",", $where_column);
    if (!empty($where_str)) {
        $where_str = " where " . $where_str;
    }



    $query = "update $table set $update_str $where_str";
    $singleDatabase->query($query);
    foreach ($data as $key => $value) {
        $singleDatabase->bind($key, $value);
    }
    foreach ($where as $key => $value) {
        $singleDatabase->bind("where" . $key, $value);
    }
    $status = $singleDatabase->execute();
    return $status;
}

function delete($table, $where = []) {
    $singleDatabase = new Database();

    $keys = array_keys($where);
    $where_column = [];
    foreach ($keys as $key => $value) {
        $where_column[] = $key . "=:where" . $key;
    }
    $where_str = implode(",", $where_column);
    if (!empty($where_str)) {
        $where_str = " where " . $where_str;
    }



    $query = "delete from $table where $where_str";
    $singleDatabase->query($query);

    foreach ($where as $key => $value) {
        $singleDatabase->bind("where" . $key, $value);
    }
    $status = $singleDatabase->execute();
    return $status;
}

function select($select) {
    $from = $attr["from"];
    $selection = get_vars($attr, "select", "*");
    $where = get_vars($attr, "where", "");
    $group = get_vars($attr, "group", "");
    $order_by = get_vars($attr, "order", "");
    $bind = get_vars($attr, "bind", []);
    $return = get_vars($attr, "return");
    $limit = get_vars($attr, "limit");

    if ($where) {
        $where = " where " . $where;
    }
    if ($limit) {
        $limit = " limit " . $limit;
    }
    if ($order_by) {
        $order_by = " order by " . $order_by;
    }

    $query = "select $selection from $from $where $group $order_by $limit";
    $singleDatabase = new Database();
    $singleDatabase->query($query);
    foreach ($bind as $bind_key => $bind_value) {
        $singleDatabase->bind(":" . $bind_key, $bind_value);
    }
    if ($return == "single") {
        return $singleDatabase->single();
    } else if ($return == "first") {
        return $singleDatabase->firstColumn();
    } else {
        return $singleDatabase->resultset();
    }
}


function has_whitespace($str) {
    return !(preg_match('/\s/', $str) == 0);
}

function db_insert($table, $bind = []) {
    $db = new Database();


    $colums = [];

    foreach ($bind as $key => $value) {
        $colums[] = $key;
    }
    $table_col = implode(",", $colums);
    $table_val = ":" . implode(",:", $colums);

    $query = "insert into $table($table_col) values($table_val)";

    if (has_whitespace($table)) {
        $query = $table;
    }
    

    $db->query($query);

    foreach ($bind as $key => $value) {
        $db->bind(":$key", $value);
    }
    $db->execute();
    return $db->lastInsertId();
}

function db_count($table, $bind = []) {
    $db = new Database();


    $colums = [];
    foreach ($bind as $key => $value) {
        $colums[] = $key . "=:" . $key;
    }
    $table_col = implode(" and ", $colums);
    $where = "";
    if (count($bind) > 0) {
        $where = " where $table_col ";
    }
    $query = "select COALESCE(count(*),0) from $table $where";

    if (has_whitespace($table)) {
        $query = $table;
    }

    $db->query($query);

    foreach ($bind as $key => $value) {
        $db->bind(":$key", $value);
    }
    return $db->firstColumn();
}
function db_delete($table, $bind = []) {
    $db = new Database();


    $colums = [];
    foreach ($bind as $key => $value) {
        $colums[] = $key . "=:" . $key;
    }
    $table_col = implode(" and ", $colums);
    $where = "";
    if (count($bind) > 0) {
        $where = " where $table_col ";
    }
    $query = "delete from $table $where";

    if (has_whitespace($table)) {
        $query = $table;
    }

    $db->query($query);

    foreach ($bind as $key => $value) {
        $db->bind(":$key", $value);
    }
    return $db->execute();
}

function db_update($table, $sets = [], $where = []) {
    $db = new Database();


    $colums = [];
    foreach ($sets as $key => $value) {
        $colums[] = $key . "=:" . $key;
    }
    $table_col = implode(" and ", $colums);
    $set_val = "";
    if (count($sets) > 0) {
        $set_val = " $table_col ";
    }

    $colums = [];
    foreach ($where as $key => $value) {
        $colums[] = $key . "=:" . $key;
    }
    $table_col = implode(" and ", $colums);
    $where_val = "";
    if (count($sets) > 0) {
        $where_val = " where $table_col ";
    }


    $query = "update $table set $set_val $where_val";

    if (has_whitespace($table)) {
        $query = $table;
    }

    $db->query($query);

    foreach ($sets as $key => $value) {
        $db->bind(":$key", $value);
    }
    foreach ($where as $key => $value) {
        $db->bind(":$key", $value);
    }
    return $db->execute();
}

function db_single($query, $bind = []) {
    $db = new Database();
    $db->query($query);
    foreach ($bind as $key => $value) {
        $db->bind(":$key", $value);
    }
    return $db->single();
}

function db_result($query, $bind = []) {
    $db = new Database();
    $db->query($query);
    foreach ($bind as $key => $value) {
        $db->bind(":$key", $value);
    }
    return $db->resultset();
}

function db_column($query, $bind = []) {
    $db = new Database();
    $db->query($query);
    foreach ($bind as $key => $value) {
        $db->bind(":$key", $value);
    }
    return $db->columnArray();
}

function db_execute($query, $bind = []) {
    $db = new Database();
    $db->query($query);
    foreach ($bind as $key => $value) {
        $db->bind(":$key", $value);
    }
    return $db->execute();
}

function get_current_userid() {
    return session_exists("userid")?get_session("userid"):-1;
}