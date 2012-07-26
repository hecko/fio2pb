<?php
include('menu.php');
include('db.php');

$trans_sql = "SELECT * FROM `transactions` t 
		LEFT JOIN `pairing` p ON p.transaction_id=t.transaction_id 
		LEFT JOIN `members` m ON p.member_id=m.member_id
		WHERE m.member_id!=0 ORDER BY t.timestamp";
$trans_raw = mysql_query($trans_sql);
if (!$trans_raw) {
	die ('Invalid query: '.mysql_error());
}

while ($t = mysql_fetch_array($trans_raw,MYSQL_ASSOC)) {
	$data[$t['member_id']][] = $t;
}

function count_months($m,$y) {
	$count = 0;
	$now_y = date("Y");
	$now_m = date("m");
	#prvy rok ak to nie je terajsi rok
	if ($y<$now_y) {
		$cm = $m;
		while ($cm<13) { 
			$cm++;
			$count++;
		}
	} else { #ak je prvy rok terajsi rok
		$cm = $m;
                while ($cm<($now_m+1)) {
                        $cm++;
                        $count++;
                }
	}
	return $count;
}

$i=0;
foreach ($data as $d) {
	echo ++$i." || ";
	echo "Meno clena: ".$d[0]['name']." / ".$d[0]['member_id']."<br>";
	echo "Mesacne clenske: ".$d[0]['fee']."<br>";
	$first_month = date("m",$d[0]['timestamp']);
	$first_year = date("Y",$d[0]['timestamp']);
	echo "Prvy mesiac a rok: $first_month/$first_year<br>";
	echo '<table border=1>';
	$sum = 0;
	$months = 5;
	foreach ($d as $t) {
		$sum = $t['amount'] + $sum;
		echo '<tr>
			<td>'.date("Y-m-d",$t['timestamp']).'</td>
			<td>'.$t['amount'].'</td>
			<td>'.$t['vs'].':'.$t['info'].'</td>
			<td>'.$t['name'].'</td>
			</tr>'."\n";
	}
	echo '</table>';
	$paid_months = $sum/$d[0]['fee'];
	$need_months = count_months($first_month,$first_year); 
	echo 'Spolu: '.$sum." Eur, potrebuje zaplatit spolu $need_months mesiacov a ma zaplatenych $paid_months mesiacov.<br>"; 
	//echo 'Zaplatenych mesiacov: '.$paid_months.'<br>';
	if ($paid_months < $need_months) {
		echo "<span class=red>Nezaplatenych ".($need_months-$paid_months)." mesiacov.</span><br>";
	} elseif ($paid_months > $need_months) {
		echo "<span class=green>Predplatenych ".abs($need_months-$paid_months)." mesiacov.</span><br>";
	}
	echo '<hr><hr>';
}
