<?php

$host = 'mysql.zzz.com.ua';
$login = 'host2280';
$password = 'Sanes0Play';
$db = 'artur_yurko';

$sql = mysqli_connect($host,$login,$password,$db);
if (!$sql) {
	echo "Не удалось подключиться к MySQL: " . mysqli_connect_error();
}
mysqli_set_charset($sql, "utf8");


function news($sql){
	$result = mysqli_query($sql, "SELECT * from news ORDER BY -ID");
	while($row = mysqli_fetch_row($result)){
		$date = implode('.', array_reverse(explode('-', $row[2])));
		echo "<div class='news'>
				<h1 class='news'>$row[1]</h1>
				<p class='date'>$date</p>
				<div class='news_text'>
					<p>$row[3]</p>
				</div>
				<div class='break'></div>
			</div>";
	}
	mysqli_free_result($result);
}


function Inews($sql, $a, $x=1){
	switch ($x) {
		case '1':
			$result = mysqli_query($sql, "SELECT name from news");
			echo '<option onClick="blurt(this); getValue(this);" value="1">Нова новина</option>';
			while ($row = mysqli_fetch_row($result)){
				if ($a == $row[0]) echo '<option onClick="blurt(this); getValue(this);" selected>'.$row[0].'</option>';
				else echo '<option onClick="blurt(this); getValue(this);">'.$row[0].'</option>';
			}
			mysqli_free_result($result);
			break;
		
		default:
			$result = mysqli_query($sql, "SELECT * from news WHERE name='$a'");
			$row = mysqli_fetch_row($result);
			mysqli_free_result($result);
			$date = explode('-', $row[2]);
			$arr = [$row[1], $date, $row[3]];
			return $arr;
			break;
	}	
}


function sd($x, $a){
	$Inews = Inews($sql, $a, 0);

	switch ($x) {
		case 'day':
			for ($i=1; $i <= 31 ; $i++) { 
				if ($Inews[1][2] == $i) {
					echo "<option onClick='blurt(this)' selected>".$i."</option>";
				}else{
					echo "<option onClick='blurt(this)'>".$i."</option>";
				}
			}
			break;
		
		case 'month':
			$month = ['Січень', 'Лютий', 'Березень', 'Квітень', 'Травень', 'Червень', 'Липень', 'Серпень', 'Вересень', 'Жовтень', 'Листопад', 'Грудень'];
			for ($i=1; $i <= 12 ; $i++) { 
				if ($Inews[1][1] == $i) {
					echo "<option onClick='blurt(this)' selected>".$month[$i-1]."</option>";
				}else{
					echo "<option onClick='blurt(this)'>".$month[$i-1]."</option>";
				}
			}
			break;
	}

}

function homework($sql, $day, $id, $t=1){
	$id = $id + 2;
	$result = mysqli_query($sql, "SELECT * FROM `homework` WHERE id=$day");
	$row = mysqli_fetch_row($result);
	mysqli_free_result($result);
	switch ($t) {
		case 1:
			echo $row[$id];
			break;
		
		case 2;
			return $row[$id];
			break;
	}		
}

function timetable($sql, $d, $mode=0) { //mode - режим. Если 0 тогда просто отображение, если 1 тогда текст меняем на инпут
	if ($d==1) {$_day=1; $_Day=3;}	 //Розділяем таблицю на 2 часті: l and r
	else {$_day=4; $_Day=5;}			// $_day - с какого дня начинаем; $_Day - каким днем заканчиваем
	for ($day=$_day; $day<=$_Day; $day++) {
		echo "<div class='table'><table>";
		$result = mysqli_query($sql, "SELECT * FROM `timetable` where id=$day"); //Витягуєм все с timetable
		$lesson = mysqli_fetch_row($result);
		mysqli_free_result($result);

		$result = mysqli_query($sql, "SELECT * FROM `homework` WHERE id=$day"); //Витягуєм все с homework
		$homework = mysqli_fetch_row($result);
		mysqli_free_result($result);
		for ($i=0; $i <= 8; $i++) {
			if ($i==8) $last = ['nl', 'bl', 'cl'];
			else $last = [' ', ' ', ' '];
			if ($i==0) echo "<tr><td class='date' colspan='3'>$lesson[1]</td></tr>";
			else {
				$less = explode("<^>", $lesson[$i + 1]); //Проверка, урок поділено на 2 групи (например Англ. Мов.№1<^>Англ. Мов.№2) или нет
				$h = explode("<^>", $homework[$i + 1]); //Проверка дз
				if ($mode==1){
					$less[0] = less($sql, $less[0], $day, $i); // + select
					$h[0] = "<input type='text' name='".$day.$i."' class='homework' value='".$h[0]."'>";  // + input
				}
				echo "<tr>
				<td class='".$last[0]." n'>$i.</td>
				<td class='".$last[1]." b'>$less[0]</td>
				<td class='".$last[2]." c'>$h[0]</td>
				</tr>";
				if (count($less)==2) {
					if ($mode==1) {
						$less[1] = less($sql, $less[1], $day, $i, 1); // + selcet
						$h[1] = "<input type='text' name='".$day.$i."1' class='homework' value='".$h[1]."'>";  // + input
					}
					echo "<tr>
					<td class='".$last[0]." n'></td>
					<td class='".$last[1]." b'>$less[1]</td>
					<td class='".$last[2]." c'>$h[1]</td>
					</tr>";
				}
			}
		}
		echo "</table></div>";
	}
}

function less($sql, $lesson, $day, $i, $x=0){
	$str = "<div class='form_sel'><select name='lesson".$day.$i.$x."' class='select'>";

	$result = mysqli_query($sql, "SELECT name from lessons order by name");
	while($row = mysqli_fetch_row($result)) {
		$row = explode("<^>", $row[0]);

		if (count($row)==2) $b = $x;
		else $b = 0;

		if ($lesson == $row[$x]) $str .= '<option onClick="blurt(this)" selected>'.$row[$b].'</option>';
		else $str .= '<option onClick="blurt(this)">'.$row[$b].'</option>';
	}
	$str .= "</select></div>";
	return $str;
}

function teachs($sql){
	$result = mysqli_query($sql, "SELECT * from teachers order by tech");
	while ($row = mysqli_fetch_row($result)) {			
		echo "<div class='tdiv'>
				<h3 class='tname' style='margin: 2%;'>
					".$row[1]."
				</h3>
				<div class='to' style='margin: 2%;'>
					".$row[2]."
				</div>
			</div>";
	}
}

function books($sql){
	$result = mysqli_query($sql, "SELECT * from books order by name");
	while($row = mysqli_fetch_row($result)){
		echo "<div class='tdiv'>
			<img src='".$row[2]."' class='img'>
			<h3 class='tname'>
				".$row[1]."
			</h3>
			<a class='download' href='".$row[3]."'>Завантажити</a>
			<div style='height: 15px;'></div>
		</div>";
	}
}

?>