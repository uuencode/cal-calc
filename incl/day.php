<?php

$transition = $time_zone->getTransitions($tstamp,$tstamp); 
$offset = $transition[0]['offset']; 

$table.='<tr>';
$table.='<td>'.$lang['food'].'</td>';
$table.='<td>'.$lang['grams'].'</td>';
$table.='<td>'.$lang['cal'].'</td>';
$table.='<td>'.$lang['fat_s'].'</td>';
$table.='<td>'.$lang['carb_s'].'</td>';
$table.='<td>'.$lang['prot_s'].'</td>';
$table.='<td class="c">'.$lang['time'].'</td>';
$table.='</tr>';

while($row=$res->fetchArray()){

$when=$q=date('H:i',$row['timestamp']+$offset);
$global_cal+=$row['cal'];
$global_weight+=$row['weight'];
$global_f+=$row['fat'];
$global_c+=$row['carb'];
$global_p+=$row['protein'];

$table.='<tr>';
$table.='<td class="b sans" style="cursor:pointer" onclick="del('.$row['id'].')">'.$row['food'].'</td>';
$table.='<td>'.number_format($row['weight'],1).$lang['grams'].'</td>';
$table.='<td class="b">'.number_format($row['cal'],1).'</td>';
$table.='<td class="color_f">'.number_format($row['fat'],1).$lang['grams'].'</td>';
$table.='<td class="color_c">'.number_format($row['carb'],1).$lang['grams'].'</td>';
$table.='<td class="color_p">'.number_format($row['protein'],1).$lang['grams'].'</td>';
$table.='<td class="c">'.$when.'</td>';
$table.='</tr>';

}

$table.='<tr><td colspan="7" style="padding:0;border-top:1px solid #666"></td></tr>';

$table.='<tr>';
$table.='<td class="b" colspan="2" rowspan="2">'.$lang['total'].'</td>';
$table.='<td class="b" rowspan="2">'.number_format($global_cal,0).$lang['cal'].'</td>';
$table.='<td class="color_f">'.number_format($global_f,1).$lang['grams'].'</td>';
$table.='<td class="color_c">'.number_format($global_c,1).$lang['grams'].'</td>';
$table.='<td class="color_p">'.number_format($global_p,1).$lang['grams'].'</td>';
$table.='<td rowspan="2">&nbsp;</td>';
$table.='</tr>';

$percent_f=0; $percent_c=0; $percent_p=0;

if($global_cal>0){
	$percent_f=number_format($global_f*9*100/$global_cal,1);
	$percent_c=number_format($global_c*4*100/$global_cal,1);
	$percent_p=number_format($global_p*4*100/$global_cal,1);
}

$table.='<tr>';
$table.='<td class="b">'.number_format($percent_f,1).'%</td>';
$table.='<td class="b">'.number_format($percent_c,1).'%</td>';
$table.='<td class="b">'.number_format($percent_p,1).'%</td>';
$table.='</tr>';

?>
