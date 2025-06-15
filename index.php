<?php

/* 

Inserts food data in the database
The correct URL to insert one or multiple entries: URL/?accesskey=ACCESSKEY&food[salad]=1,2,3,4,5&food[meat]=1,2,3,4,5 
where 1=calories, 2=food weight in grams, 3=fat in grams, 4=carbs in grams, 5=protein in grams  

*/

require 'config.php';
require $lang_file;
$db=$databasef.'/db.sqlite';

// can we write to the database?
if(!is_writeable($db)){
	die('Database not writeable or missing...');}

$db = new SQLite3($db); $msg=[]; $tstamp=time(); $date_f=date('Y-m-d',$tstamp);

function abc123($n){
	$n=preg_replace('/[^\p{L}\p{N} \-\.]/u',' ',$n);
	$n=preg_replace('/([\s])\1+/',' ',$n);
	return trim($n);}

// ---

// check if weight or food array is OK
if(!isset($_GET['weight']) && (!isset($_GET['food']) || !is_array($_GET['food']))){
	die('Input seems to be wrong...');}

// check if accesskey is OK
if(!isset($_GET['accesskey']) || $_GET['accesskey']!==$accesskey){
	die('Wrong or missing accesskey...');}

// adding weight
if(isset($_GET['weight']) && is_numeric($_GET['weight'])){
	$weight=(float)$_GET['weight'];
	$db->query('BEGIN');
	$db->query("DELETE FROM weight WHERE date_full='$date_f'");
	$db->query("INSERT INTO weight VALUES('$date_f',$weight)");
	$db->query('COMMIT');
	$msg[]='⚖️ '.$weight;
}

// adding food
if(isset($_GET['food']) && is_array($_GET['food'])){
	$get_array_ok=true; $food=[]; 

	foreach ($_GET['food'] as $key => $val){

		$name=abc123($key);
		$d=explode(',',$val);

		if(count($d)<>5 || !is_numeric($d[0]) || !is_numeric($d[1]) || !is_numeric($d[2]) || !is_numeric($d[3]) || !is_numeric($d[4])){
			$get_array_ok=false;
			break;
		}

		// calc from grams and overwrite received calories 
		$d[0] = ($d[2]*9) + ($d[3]*4) + ($d[4]*4);

		$food[$name]=[$d[0],$d[1],$d[2],$d[3],$d[4]];

	}

	// one of the food entries does have the required parameters
	if($get_array_ok!==true){
		die('Incorrect food entry...');}

	// insert into db
	$date_y=(int)date('Y',$tstamp);
	$date_m=(int)date('m',$tstamp);
	$date_d=(int)date('d',$tstamp);

	$db->query('BEGIN');

	foreach ($food as $key => $val){
		$db->query("INSERT INTO data values(NULL,'$key',$val[0],$val[1],$val[2],$val[3],$val[4],$date_y,$date_m,$date_d,'$date_f',$tstamp)");

		$msg[]=$key.': '.$val[1].$lang['grams'].' ('.$lang['cal'].' = '.$val[0].')';
	}

	$db->query('COMMIT');	
}

$msg=implode("\n",$msg);

?>
<!DOCTYPE html>
<html lang="en"><head>
<title>cal-calc</title>
<meta charset="utf-8">
<meta name="referrer" content="same-origin">
<meta name="viewport" content="width=device-width, height=device-height, initial-scale=1.0, user-scalable=yes">
</head>
<body>

<pre style="width:600px;max-width:95%;margin:auto;margin-top:2vh">
<b style="font-size:24px">👍 OK</b>
<div style="margin:10px 0;border-top:1px solid #000"></div>
<?php print $msg;?><br>
<button onclick="self.location.href='view.php'" style="border-width:0;padding:15px 0;width:100%;margin:10px 0;border-radius:5px"><?php print $lang['view'];?></button>
</pre>
</body>
</html>