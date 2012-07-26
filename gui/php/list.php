<?php
include('menu.php');
include("db.php");

$trans_sql = "SELECT *,t.transaction_id,p.member_id FROM `transactions` t
		LEFT OUTER JOIN `pairing` p ON t.transaction_id=p.transaction_id 
		LEFT OUTER JOIN `members` m ON p.member_id=m.member_id
		ORDER BY t.timestamp ASC";
$trans_raw = mysql_query($trans_sql);
if (!$trans_raw) {
	die ('Invalid query: '.mysql_error());
}

function members_list() {
	$raw = mysql_query('SELECT * FROM `members` ORDER BY `name`');
	while ($m = mysql_fetch_array($raw)) {
		$members[$m['member_id']] = $m['name'];
	}
	return $members;
}

function members_options($selected) {
	$members = members_list();
	$out = "";
	foreach ($members as $id => $name) {
		if ($id == $selected) {
			$sel = 'selected';
		} else {
			$sel = '';
		}
		$out .= '<option value='.$id.' '.$sel.'>'.$name."</option>\n";
	}
	return $out;
}

$members = members_list();

echo '<table border=1>';
while ($t = mysql_fetch_array($trans_raw,MYSQL_ASSOC)) {
	$members_options = members_options($t['member_id']);
	if (!$t['member_id']) {
		$row_style = 'red';
	} else {
		$row_style = 'normal';
	}
	echo '<tr class='.$row_style.'>
		<td>'.$t['transaction_id'].'</td>
		<td>'.date("d.M Y",$t['timestamp']).'</td>
		<td>'.$t['amount'].'</td>
		<td>VS: '.$t['vs'].'</td><td>'.$t['info'].'</td>
		<td><form method=POST action=pair_save.php><select name=member_id>'.$members_options.@$members[$t['member_id']].'</select>'.
		'<input type=hidden name=transaction_id value='.$t['transaction_id'].'><input type=submit value=go></form></td>'
		.'<td>Navrh:<form method=POST action=pair_save.php>
		<input type=hidden name=transaction_id value='.$t['transaction_id'].'>
		<input type=hidden name=member_id value='.$t['vs'].'>
		<input type=submit value="'.@$members[$t['vs']].'"></form></td>'
		.'</tr>'."\n";
}
echo '</table>';
?>
