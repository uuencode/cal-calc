<?php

$table.='<tr>';
$table.='<td>'.$lang['date'].'</td>';
$table.='<td>'.$lang['kg'].'</td>';
$table.='<td>'.$lang['cal'].'</td>';
$table.='<td colspan="2" class="c b">'.$lang['fat_s'].'</td>';
$table.='<td colspan="2" class="c b">'.$lang['carb_s'].'</td>';
$table.='<td colspan="2" class="c b">'.$lang['prot_s'].'</td>';
$table.='</tr>';

while($row=$res->fetchArray()){
	$days+=1; $kg=''; 
	$global_cal+=$row['cal'];
	$global_f+=$row['f'];
	$global_c+=$row['c'];
	$global_p+=$row['p'];
	if(is_numeric($row['kg'])){ 
		$wdays+=1;
		$kg=number_format($row['kg'],1);
		$global_weight+=$row['kg']; }

	$percent_f=number_format($row['f']*9*100/$row['cal'],1);
	$percent_c=number_format($row['c']*4*100/$row['cal'],1);
	$percent_p=number_format($row['p']*4*100/$row['cal'],1);

	$table.='<tr>';

	$table.='<td>'.$row['date_full'].'</td>';
	$table.='<td>'.$kg.'</td>';
	$table.='<td class="b">'.number_format($row['cal'],0).'</td>';

	$table.='<td class="color_f">'.number_format($row['f'],0).$lang['grams'].'</td>';
	$table.='<td class="color_f">'.$percent_f.'%</td>';

	$table.='<td class="color_c">'.number_format($row['c'],0).$lang['grams'].'</td>';
	$table.='<td class="color_c">'.$percent_c.'%</td>';

	$table.='<td class="color_p">'.number_format($row['p'],0).$lang['grams'].'</td>';
	$table.='<td class="color_p">'.$percent_p.'%</td>';

	$table.='</tr>';
}

$percent_f=0; $percent_c=0; $percent_p=0; $average=0; $average_kg='';

if($global_cal>0 && $days>0){
	$percent_f=number_format($global_f*9*100/$global_cal,1);
	$percent_c=number_format($global_c*4*100/$global_cal,1);
	$percent_p=number_format($global_p*4*100/$global_cal,1);
	$average=$global_cal/$days;
}

if($wdays>0){
	$average_kg=number_format($global_weight/$wdays,1);
}

$table.='<tr>';
$table.='<td class="b">'.$lang['average'].'</td>';
$table.='<td class="b">'.$average_kg.$lang['kg'].'</td>';
$table.='<td class="b">'.number_format($average,0).$lang['cal'].'</td>';
$table.='<td class="b" colspan="2">'.number_format($percent_f,1).'%</td>';
$table.='<td class="b" colspan="2">'.number_format($percent_c,1).'%</td>';
$table.='<td class="b" colspan="2">'.number_format($percent_p,1).'%</td>';
$table.='</tr>';

?>
