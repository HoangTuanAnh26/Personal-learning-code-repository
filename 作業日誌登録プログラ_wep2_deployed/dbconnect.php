<?php

//dbconnect.php ; DB接続

	// define('DB_HOST','sql303.infinityfree.com');
	// define('DB_NAME','if0_38922260_webpg2_02');
	// define('DB_USER','if0_38922260');
	// define('DB_PASSWORD','lHKVnnGhSiIJfb');


    // $options = array(PDO::MYSQL_ATTR_INIT_COMMAND=>"SET CHARACTER SET 'utf8'");
    // error_reporting(E_ALL & ~E_NOTICE);

	// try{
	// 	$dbh = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME,DB_USER,DB_PASSWORD,$options);
	// 	$dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
	// }catch(PDOException $e){
	// 	echo $e->getMessage();
	// 	exit;
	// }

	// date_default_timezone_set('Asia/Tokyo');
//	header("Content-Type: text/html; charset=UTF-8");
?>


<?php

//dbconnect.php ; DB接続

	define('DB_HOST','localhost');
	define('DB_NAME','webpg2_2025');
	define('DB_USER','root');
	define('DB_PASSWORD','');


    $options = array(PDO::MYSQL_ATTR_INIT_COMMAND=>"SET CHARACTER SET 'utf8'");
    error_reporting(E_ALL & ~E_NOTICE);

	try{
		$dbh = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME,DB_USER,DB_PASSWORD,$options);
		$dbh->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
	}catch(PDOException $e){
		echo $e->getMessage();
		exit;
	}

	date_default_timezone_set('Asia/Tokyo');
//	header("Content-Type: text/html; charset=UTF-8");
?>


<!-- Chỉnh sửa để có thể chạy được cả trên local và trên hosting -->