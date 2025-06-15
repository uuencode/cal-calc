<?php

/* 

Displays food data from the database

*/

require 'config.php';
require $lang_file;
$db=$databasef.'/db.sqlite';

// can we write to the database?
if(!is_writeable($db)){
	die('Database not writeable or missing...');}

$db = new SQLite3($db);

if(isset($_POST['key']) && $_POST['key']==$accesskey && isset($_POST['fid']) && is_numeric($_POST['fid'])){
	$fid=(int)$_POST['fid'];
	$db->query('BEGIN');
	$db->query('DELETE FROM data WHERE id='.$fid);
	$db->query('COMMIT');

}

$tstamp=time();

$this_month=(int)date('m',$tstamp);
$last_month=(int)date('m',strtotime("-1 month"));
$today=date('Y-m-d',$tstamp);
$yesterday=date('Y-m-d',strtotime("-1 days"));


$q='today'; $formget='today'; $finc='day.php';  $title=$lang['today'];

// query params $_GET['q'] not set
if(isset($_GET['q'])){
	$q=$_GET['q'];
}


if(!is_numeric($q)){
	switch($q){
		case 'today':     $q="SELECT * FROM data WHERE date_full='$today'"; $title=$lang['today'];     break;
		case 'yesterday': $q="SELECT * FROM data WHERE date_full='$yesterday'"; $title=$lang['y_day']; $formget='yesterday'; break;
		case 'thismonth': $q="SELECT a.date_full AS date_full,b.weight AS kg,sum(cal) AS cal,sum(fat) AS f,sum(carb) AS c,sum(protein) AS p FROM data a LEFT JOIN weight b ON a.date_full=b.date_full WHERE date_m=$this_month GROUP BY a.date_full"; $finc='month.php'; $title=$lang['t_month']; break;
		case 'lastmonth': $q="SELECT a.date_full AS date_full,b.weight AS kg,sum(cal) AS cal,sum(fat) AS f,sum(carb) AS c,sum(protein) AS p FROM data a LEFT JOIN weight b ON a.date_full=b.date_full WHERE date_m=$last_month GROUP BY a.date_full"; $finc='month.php'; $title=$lang['t_month']; break;
		default:          $q="SELECT * FROM data WHERE date_full='$today'"; $title=$lang['today'];    break;
	}
}
else{
	if($q>60){ header('location:view.php?q=today'); die(); }
		
	$finc='month.php';
	$title=$q.' '.$lang['days'];
	$q=date('Y-m-d',strtotime("-$q days"));  
	$q="SELECT a.date_full AS date_full,b.weight AS kg,sum(cal) AS cal,sum(fat) AS f,sum(carb) AS c,sum(protein) AS p FROM data a LEFT JOIN weight b ON a.date_full=b.date_full WHERE a.date_full>'$q' GROUP BY a.date_full";
}

$table=''; $info=''; $global_weight=0; $global_cal=0; $global_f=0; $global_c=0; $global_p=0; $days=0; $wdays=0;

$res=$db->query($q);
require 'incl/'.$finc;

$month_curr=date('M',$tstamp);
$month_last=date('M',strtotime("-1 month"));

?>
<!DOCTYPE html>
<html lang="en"><head>
<title>cal-calc</title>
<meta charset="utf-8">
<meta name="referrer" content="same-origin">
<meta name="viewport" content="width=device-width, height=device-height, initial-scale=1.0, user-scalable=yes">
</head>

<script>

function de(x){return document.getElementById(x)}

function del(d){
	a=prompt('<?php print $lang['del_p'];?>')
	if(a!=''){
		de('fid').value=d
		de('key').value=a
		de('frm').submit()
	}
}

function apply_theme(){
const styleSheet = document.createElement('style')
styleSheet.type = 'text/css'
if(user_dark){
	localStorage.setItem('dark_saved','1')
	styleSheet.innerText = ':root{--f-color:#fff;--b-color:#222;--a-color:#444}'
}
else{
	localStorage.removeItem('dark_saved')
	styleSheet.innerText = ':root{--f-color:#000;--b-color:#fff;--a-color:#eee}'
} document.head.appendChild(styleSheet) }

initb=false
function change_tbutton(m){

	d=de('todark')
	l=de('tolght')

	if(user_dark){
	d.style.display='none'
	l.style.display='block';initb=l}
	else{
	d.style.display='block'
	l.style.display='none';initb=d}

	if(m>0){initb.className+=' sut'
	setTimeout("initb.className=initb.className.replace('sut','')",2000)}
}

// ---

dark_saved=null; user_dark=false;
try{dark_saved=localStorage.getItem('dark_saved')} 
catch(e){console.log('No theme (saved) '+e)}
if(dark_saved!==null){user_dark=true}
apply_theme()

</script>

<style>

body{color:var(--f-color);background-color:var(--b-color)}
body,div,td{font-family:monospace;font-size:13px}
.sans{font-family:sans-serif;font-size:15px}
table{width:100%;border-spacing:1px;border:1px solid var(--a-color);background-color:var(--a-color);}
td{background-color:var(--b-color);padding:8px;text-align:right}
td.c{text-align:center}
td.b{font-weight:600;}
.main_w{overflow:auto;width:600px;max-width:95%;margin:auto;margin-top:2vh}

.ut{position:fixed;right:0;top:0;width:40px;height:40px;padding:0;border-radius:0 0 0 100%;cursor:pointer;background-size:24px;background-repeat:no-repeat;background-position:14px 4px}
.utl{background-color:#fff;background-image:url(data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIyNCIgaGVpZ2h0PSIyNCIgdmlld0JveD0iMCAwIDI0IDI0Ij48cGF0aCBkPSJNNi45OTMgMTJjMCAyLjc2MSAyLjI0NiA1LjAwNyA1LjAwNyA1LjAwN3M1LjAwNy0yLjI0NiA1LjAwNy01LjAwN1MxNC43NjEgNi45OTMgMTIgNi45OTMgNi45OTMgOS4yMzkgNi45OTMgMTJ6TTEyIDguOTkzYzEuNjU4IDAgMy4wMDcgMS4zNDkgMy4wMDcgMy4wMDdTMTMuNjU4IDE1LjAwNyAxMiAxNS4wMDcgOC45OTMgMTMuNjU4IDguOTkzIDEyIDEwLjM0MiA4Ljk5MyAxMiA4Ljk5M3pNMTAuOTk4IDE5aDJ2M2gtMnptMC0xN2gydjNoLTJ6bS05IDloM3YyaC0zem0xNyAwaDN2MmgtM3pNNC4yMTkgMTguMzYzbDIuMTItMi4xMjIgMS40MTUgMS40MTQtMi4xMiAyLjEyMnpNMTYuMjQgNi4zNDRsMi4xMjItMi4xMjIgMS40MTQgMS40MTQtMi4xMjIgMi4xMjJ6TTYuMzQyIDcuNzU5IDQuMjIgNS42MzdsMS40MTUtMS40MTQgMi4xMiAyLjEyMnptMTMuNDM0IDEwLjYwNS0xLjQxNCAxLjQxNC0yLjEyMi0yLjEyMiAxLjQxNC0xLjQxNHoiLz48L3N2Zz4=)}
.utd{background-color:#000;background-image:url(data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIyNCIgaGVpZ2h0PSIyNCIgdmlld0JveD0iMCAwIDI0IDI0IiBmaWxsPSIjZmZmIj48cGF0aCBkPSJNMjAuNzQyIDEzLjA0NWE4LjA4OCA4LjA4OCAwIDAgMS0yLjA3Ny4yNzFjLTIuMTM1IDAtNC4xNC0uODMtNS42NDYtMi4zMzZhOC4wMjUgOC4wMjUgMCAwIDEtMi4wNjQtNy43MjNBMSAxIDAgMCAwIDkuNzMgMi4wMzRhMTAuMDE0IDEwLjAxNCAwIDAgMC00LjQ4OSAyLjU4MmMtMy44OTggMy44OTgtMy44OTggMTAuMjQzIDAgMTQuMTQzYTkuOTM3IDkuOTM3IDAgMCAwIDcuMDcyIDIuOTMgOS45MyA5LjkzIDAgMCAwIDcuMDctMi45MjkgMTAuMDA3IDEwLjAwNyAwIDAgMCAyLjU4My00LjQ5MSAxLjAwMSAxLjAwMSAwIDAgMC0xLjIyNC0xLjIyNHptLTIuNzcyIDQuMzAxYTcuOTQ3IDcuOTQ3IDAgMCAxLTUuNjU2IDIuMzQzIDcuOTUzIDcuOTUzIDAgMCAxLTUuNjU4LTIuMzQ0Yy0zLjExOC0zLjExOS0zLjExOC04LjE5NSAwLTExLjMxNGE3LjkyMyA3LjkyMyAwIDAgMSAyLjA2LTEuNDgzIDEwLjAyNyAxMC4wMjcgMCAwIDAgMi44OSA3Ljg0OCA5Ljk3MiA5Ljk3MiAwIDAgMCA3Ljg0OCAyLjg5MSA4LjAzNiA4LjAzNiAwIDAgMS0xLjQ4NCAyLjA1OXoiLz48L3N2Zz4=)}

a:link{color:var(--f-color)}
a:visited{color:var(--f-color)}
a:hover{color:#E91E63;text-decoration:none}
a:active{color:#E91E63;text-decoration:none}

.color_f{color:#ffb000} .color_c{color:#3db4f2} .color_p{color:#38c172} 

</style>

<body onload="change_tbutton(1)">

<h1 class="main_w"><?php print $title;?></h1>

<div class="main_w">
<table> <?php print $table;?> </table>
<div> <?php print $info;?> </div>
</div>


<div class="main_w sans" style="text-align:right">
<span style="float:left;margin-right:5px">
<a href="<?php print $ai_prompt;?>"><?php print $lang['ai_prompt'];?></a>
</span>
<a href="view.php?q=today"><?php print $lang['today'];?></a> &middot;
<a href="view.php?q=yesterday"><?php print $lang['y_day'];?></a> &middot;
<a href="view.php?q=5">5 <?php print $lang['days'];?></a> &middot;
<a href="view.php?q=7">7</a> &middot;
<a href="view.php?q=10">10</a> &middot;
<a href="view.php?q=thismonth"><?php print $month_curr;?></a> &middot;
<a href="view.php?q=lastmonth"><?php print $month_last;?></a>
</div>


<div id="todark" style="display:none" class="ut utd" onclick="user_dark=true;apply_theme();change_tbutton(0)"></div>
<div id="tolght" style="display:none" class="ut utl" onclick="user_dark=false;apply_theme();change_tbutton(0)"></div>

<form id="frm" action="view.php?q=<?php print $formget;?>" method="post">
<input type="hidden" id="key" name="key" value="">
<input type="hidden" id="fid" name="fid" value="0">
</form>

</body>
</html>