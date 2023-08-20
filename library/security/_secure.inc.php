<?php
require_once 'library/security/_access.inc.php';

// Process logout
if (isset($_REQUEST['logout'])){
    
	unset($_SESSION['authorized']);
	unset($_SESSION['user_name']);
	unset($_SESSION['user_admin']);
	unset($_SESSION['user_web_start']);
	unset($_SESSION['user_disallowed_folders']);
	unset($_SESSION['user_disallowed_files']);
	unset($_SESSION['user_allowed_modules']);
	unset($_SESSION['hash']);
    
    if(isset($_COOKIE["user_id"])){
        clear_user_access_token($_COOKIE["user_id"]);
    }
    
	header('location:'.WEB_ROOT.'editor/index.php');
	exit;
}

function loggedIn(){

	if(!isset($_SESSION['authorized'])){
		return FALSE;
	}

	if($_SESSION['hash'] != md5($_SESSION['user_name'].$_SESSION['user_admin'].$_SESSION['user_web_start'].$_SESSION['user_disallowed_folders'].$_SESSION['user_disallowed_files'].$_SESSION['user_allowed_modules'])){
		header('location:'.WEB_ROOT.'editor/index.php?logout');
		exit;
	}

	checkLocation();

	return TRUE;
}

function checkLocation(){
    
	// check current location matches the users permissions
	// if admin always allow access

	if ($_SESSION['user_admin'] != 'Yes'){

		$self = "http://".$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF'];

		list($junk, $self) = explode(WEB_ROOT.'editor/', $self);

		$paths = explode('/', $self);

		if(count($paths)!=1){
			if(!in_array($paths[0], $_SESSION['user_allowed_modules'])){
				header('location:'.WEB_ROOT.'editor/index.php');
				exit;
			}
		}

	}
}

if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["sckeditor-login"])){

	$email = safeAddSlashes($_POST['email']);
	$password = safeAddSlashes($_POST['password']);
	
	// check if the user id and password combination exist in database
	$sql = "SELECT user_id FROM tbl_user WHERE user_email = '$email' AND user_password = PASSWORD('$password')";
	$result = getQuery($sql, 'Could not get query: ');
    
	if (mysql_num_rows($result) == 1){
        
		$row = mysql_fetch_array($result);
		$user_id = $row['user_id'];
        
        $give_access_token = isset($_POST['stay_logged_in']) && $_POST['stay_logged_in'] == '1';
        
        authorize_client($user_id, $give_access_token);
        
        redirect_to_self(); 
        
	} else {
		$errorMessage = 'Sorry, wrong user id / password, please try again.';
	}
}

function authorize_client($user_id, $give_access_token = false){
    
    $user_id_safe = safeaddslashes($user_id);
    
    $sql = "SELECT * FROM tbl_user WHERE user_id='$user_id_safe'";
	$result = getQuery($sql, 'Could not get query: ');
	
	if(mysql_num_rows($result) != 1){
	    throw new Exception("user not found");
	}
	
	$row = mysql_fetch_array($result);
	
    $_SESSION['authorized'] = TRUE;
    
	$_SESSION['user_name'] = $row['user_name'];
	$_SESSION['user_admin'] = $row['user_admin'];
	$_SESSION['user_web_start'] = $row['user_web_start'];

	$user_disallowed_folders = $row['user_disallowed_folders'];
	if($user_disallowed_folders!=''){
		$_SESSION['user_disallowed_folders'] = explode(',', $user_disallowed_folders);
	} else {
		$_SESSION['user_disallowed_folders'] = array();
	}

	$user_disallowed_files = $row['user_disallowed_files'];
	if($user_disallowed_files!=''){
		$_SESSION['user_disallowed_files'] = explode(',', $user_disallowed_files);
	} else {
		$_SESSION['user_disallowed_files'] = array();
	}

	$user_allowed_modules = $row['user_allowed_modules'];
	if($user_allowed_modules!=''){
		$_SESSION['user_allowed_modules'] = explode(',', $user_allowed_modules);
	} else {
		$_SESSION['user_allowed_modules'] = array();
	}

	$_SESSION['hash'] = md5($_SESSION['user_name'].$_SESSION['user_admin'].$_SESSION['user_web_start'].$_SESSION['user_disallowed_folders'].$_SESSION['user_disallowed_files'].$_SESSION['user_allowed_modules']);
    
    if($give_access_token){
    
        $access_token = assign_user_access_token($user_id);
        
		$EXPIRE = 63072000; // 2 years
        setcookie("access_token", $access_token, time() + $EXPIRE);
        setcookie("user_id", $user_id, time() + $EXPIRE);
    }
    else{
        clear_user_access_token($user_id);
    }
}

function assign_user_access_token($user_id){

    $user_id_safe = safeaddslashes($user_id);
    
    $access_token = uniqid();
    
    $access_token_hash = sha1($access_token);
    $access_token_hash_safe = safeaddslashes($access_token_hash);
    
    getQuery("UPDATE tbl_user SET
        user_access_token_hash='$access_token_hash_safe'
        WHERE user_id='$user_id_safe'");
    
    return $access_token;
}

function clear_user_access_token($user_id){
    
    $user_id_safe = safeaddslashes($user_id);
    
    getQuery("UPDATE tbl_user SET
        user_access_token_hash = ''
        WHERE user_id = '$user_id_safe'");
}

if (!loggedIn()){
    
    $require_login = true;
    
    if(isset($_COOKIE["access_token"]) && isset($_COOKIE["user_id"])){
        
        $user_id = $_COOKIE["user_id"];
        $user_id_safe = safeaddslashes($user_id);
        $result = getQuery("SELECT user_access_token_hash FROM tbl_user WHERE user_id = '$user_id_safe'");
        if(mysql_num_rows($result) == 1){
            $row = mysql_fetch_array($result);
            $user_access_token_hash = $row["user_access_token_hash"];
            if(sha1($_COOKIE["access_token"]) == $user_access_token_hash){
                authorize_client($user_id, true);
                $require_login = false;
            }
        }
    }
    
    if($require_login){
        require_once 'library/security/_login.inc.php';
        exit;
    }
}
