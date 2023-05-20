<html>
<head>
<meta charset="utf-8">
<title>Order Multiple Tickets</title>
</head>
<body>
<h3>Please confirm the order data.</h3>
if all correct, press 'ORDER'<br><br>
<?php
$user = $_GET["user"];
$train_num1 = $_GET["train_num1"];
$train_num2 = $_GET["train_num2"];
$date1 = $_GET["date1"];
$date2 = $_GET["date2"];
$start_station1_id = $_GET["start_station1_id"];
$end_station1_id = $_GET["end_station1_id"];
$start_station2_id = $_GET["start_station2_id"];
$end_station2_id = $_GET["end_station2_id"];
echo "<a href=\"http://localhost:8080/CNTRAK_230513/query_train_between_cities.php?user=".$user."\">Return to query page</a><br>\n";
echo "user is " . $user . "<br>\n";
echo "train_num1 is " . $train_num1 . "<br>\n";
echo "train_num2 is " . $train_num2 . "<br>\n";
echo "date1 is " . $date1 . "<br>\n";
echo "date2 is " . $date2 . "<br>\n";
echo "start_station1_id is " . $start_station1_id . "<br>\n";
echo "end_station1_id is " . $end_station1_id . "<br>\n";
echo "start_station2_id is " . $start_station2_id . "<br>\n";
echo "end_station2_id is " . $end_station2_id . "<br>\n";
echo "<br>\n";
//connect to database
echo "Connecting to database...<br>\n";
$conn = pg_connect("host=localhost dbname=postgres user=postgres password=123")or die("Could not connect to database! " . pg_last_error());
echo "Successfully connected!<br>\n";

//what to output:
//train1:
//train_num
//start_timestamp, start_station
//end_timestamp, end_station
//seat_type, ticket_price
//order_price = 5*ticket_num
//price = ticket_price + order_price
//train2:
//train_num
//start_timestamp, start_station
//end_timestamp, end_station
//seat_type, ticket_price
//order_price = 5*ticket_num
//price = ticket_price + order_price
$query_train1_res1 = pg_query($conn, "select dep_time, station, seaty_p, seatr_p, sleeperys_p, sleeperyz_p, sleeperyx_p, sleeperrs_p, sleeperrx_p, running_time, train_num, station_id, arr_time from trains where train_num = '".$train_num1."' and train_station_id = ".$start_station1_id)or die("Could not query t1! " . pg_last_error());
$query_train1_res2 = pg_query($conn, "select arr_time, station, seaty_p, seatr_p, sleeperys_p, sleeperyz_p, sleeperyx_p, sleeperrs_p, sleeperrx_p, running_time, train_num, station_id from trains where train_num = '".$train_num1."' and train_station_id = ".$end_station1_id)or die("Could not query t2! " . pg_last_error());
echo "the information of the first_train is as follows:<br>\n";
//output train_num, start_station, end_station, dep_timestamp, arr_timestamp, price...
$start_station1 = pg_fetch_result($query_train1_res1, 0, 1);
$end_station1 = pg_fetch_result($query_train1_res2, 0, 1);
//timestamp---show on train_num1
$dep_time1 = pg_fetch_result($query_train1_res1, 0, 0);
echo "dep_time1 is " . $dep_time1 . "<br>\n";
//dep_timestamp1---the deo timestamp of the first train
$dep_timestamp1_res = pg_query($conn, "select date '".$date1."' + interval '".$dep_time1."'")or die("Could not add time! " . pg_last_error());
$dep_timestamp1 = pg_fetch_result($dep_timestamp1_res, 0, 0);
pg_free_result($dep_timestamp1_res);
//calculate the running time of the first train
$t1_running_time = pg_fetch_result($query_train1_res1, 0, 9);
echo "t1_running_time is " . $t1_running_time . "<br>\n";
$t2_running_time = pg_fetch_result($query_train1_res2, 0, 9);
echo "t2_running_time is " . $t2_running_time . "<br>\n";
$running_time1_res = pg_query($conn, "select interval '".$t2_running_time."' - interval '".$t1_running_time."'")or die("Could not calculate time! " . pg_last_error());
$running_time1 = pg_fetch_result($running_time1_res, 0, 0);
echo "running_time1 is " . $running_time1 . "<br>\n";
echo "dep_timestamp1 is " . $dep_timestamp1 . "<br>\n";
$arr_timestamp1_res = pg_query($conn, "select timestamp '".$dep_timestamp1."' + interval '".$running_time1."'")or die("Could not add time! " . pg_last_error());
$arr_timestamp1 = pg_fetch_result($arr_timestamp1_res, 0, 0);
echo "arr_timestamp1 is " . $arr_timestamp1 . "<br>\n";
//price---train_num1---seaty1_p
$t1_seaty_p = pg_fetch_result($query_train1_res1, 0, 2);
$t2_seaty_p = pg_fetch_result($query_train1_res2, 0, 2);
$seaty1_p = $t2_seaty_p - $t1_seaty_p;
$t1_seatr_p = pg_fetch_result($query_train1_res1, 0, 3);
$t2_seatr_p = pg_fetch_result($query_train1_res2, 0, 3);
$seatr1_p = $t2_seatr_p - $t1_seatr_p;
$t1_sleeperys_p = pg_fetch_result($query_train1_res1, 0, 4);
$t2_sleeperys_p = pg_fetch_result($query_train1_res2, 0, 4);
$sleeperys1_p = $t2_sleeperys_p - $t1_sleeperys_p;
$t1_sleeperyz_p = pg_fetch_result($query_train1_res1, 0, 5);
$t2_sleeperyz_p = pg_fetch_result($query_train1_res2, 0, 5);
$sleeperyz1_p = $t2_sleeperyz_p - $t1_sleeperyz_p;
$t1_sleeperyx_p = pg_fetch_result($query_train1_res1, 0, 6);
$t2_sleeperyx_p = pg_fetch_result($query_train1_res2, 0, 6);
$sleeperyx1_p = $t2_sleeperyx_p - $t1_sleeperyx_p;
$t1_sleeperrs_p = pg_fetch_result($query_train1_res1, 0, 7);
$t2_sleeperrs_p = pg_fetch_result($query_train1_res2, 0, 7);
$sleeperrs1_p = $t2_sleeperrs_p - $t1_sleeperrs_p;
$t1_sleeperrx_p = pg_fetch_result($query_train1_res1, 0, 8);
$t2_sleeperrx_p = pg_fetch_result($query_train1_res2, 0, 8);
$sleeperrx1_p = $t2_sleeperrx_p - $t1_sleeperrx_p;
//outout as a table
echo "<table border=\"1\">";
echo "<tr>";
echo "<td>train_num</td><td>start_station</td><td>end_station</td><td>start_time</td><td>end_time</td><td>seaty</td><td>seatr</td><td>sleeperys</td><td>sleeperyz</td><td>sleeperyx</td><td>sleeperrs</td><td>sleeperrx</td>";
echo "</tr>";
echo "<td>".$train_num1."</td><td>".$start_station1."</td><td>".$end_station1."</td><td>".$dep_timestamp1."</td><td>".$arr_timestamp1."</td><td>".$seaty1_p."</td><td>".$seatr1_p."</td><td>".$sleeperys1_p."</td><td>".$sleeperyz1_p."</td><td>".$sleeperyx1_p."</td><td>".$sleeperrs1_p."</td><td>".$sleeperrx1_p."</td>";
echo "</tr>";
echo "</table>";

//train2
$query_train2_res1 = pg_query($conn, "select dep_time, station, seaty_p, seatr_p, sleeperys_p, sleeperyz_p, sleeperyx_p, sleeperrs_p, sleeperrx_p, running_time, train_num, station_id, arr_time from trains where train_num = '".$train_num2."' and train_station_id = ".$start_station2_id)or die("Could not query t1! " . pg_last_error());
$query_train2_res2 = pg_query($conn, "select arr_time, station, seaty_p, seatr_p, sleeperys_p, sleeperyz_p, sleeperyx_p, sleeperrs_p, sleeperrx_p, running_time, train_num, station_id from trains where train_num = '".$train_num2."' and train_station_id = ".$end_station2_id)or die("Could not query t2! " . pg_last_error());
echo "the information of the second_train is as follows:<br>\n";
//output train_num, start_station, end_station, dep_timestamp, arr_timestamp, price...
$start_station2 = pg_fetch_result($query_train2_res1, 0, 1);
$end_station2 = pg_fetch_result($query_train2_res2, 0, 1);
//timestamp---show on train_num2
$dep_time2 = pg_fetch_result($query_train2_res1, 0, 0);
echo "dep_time2 is " . $dep_time2 . "<br>\n";
//dep_timestamp2---the deo timestamp of the second train
$dep_timestamp2_res = pg_query($conn, "select date '".$date2."' + interval '".$dep_time2."'")or die("Could not add time! " . pg_last_error());
$dep_timestamp2 = pg_fetch_result($dep_timestamp2_res, 0, 0);
pg_free_result($dep_timestamp2_res);
//calculate the running time of the second train
$t3_running_time = pg_fetch_result($query_train2_res1, 0, 9);
echo "t3_running_time is " . $t3_running_time . "<br>\n";
$t4_running_time = pg_fetch_result($query_train2_res2, 0, 9);
echo "t4_running_time is " . $t4_running_time . "<br>\n";
$running_time2_res = pg_query($conn, "select interval '".$t4_running_time."' - interval '".$t3_running_time."'")or die("Could not calculate time! " . pg_last_error());
$running_time2 = pg_fetch_result($running_time2_res, 0, 0);
echo "running_time2 is " . $running_time2 . "<br>\n";
echo "dep_timestmp2 is " . $dep_timestamp2 . "<br>\n";
$arr_timestamp2_res = pg_query($conn, "select timestamp '".$dep_timestamp2."' + interval '".$running_time2."'")or die("Could not add time! " . pg_last_error());
$arr_timestamp2 = pg_fetch_result($arr_timestamp2_res, 0, 0);
echo "arr_timestamp2 is " . $arr_timestamp2 . "<br>\n";
//price---train_num2---seaty2_p
$t3_seaty_p = pg_fetch_result($query_train2_res1, 0, 2);
$t4_seaty_p = pg_fetch_result($query_train2_res2, 0, 2);
$seaty2_p = $t4_seaty_p - $t3_seaty_p;
$t3_seatr_p = pg_fetch_result($query_train2_res1, 0, 3);
$t4_seatr_p = pg_fetch_result($query_train2_res2, 0, 3);
$seatr2_p = $t4_seatr_p - $t3_seatr_p;
$t3_sleeperys_p = pg_fetch_result($query_train2_res1, 0, 4);
$t4_sleeperys_p = pg_fetch_result($query_train2_res2, 0, 4);
$sleeperys2_p = $t4_sleeperys_p - $t3_sleeperys_p;
$t3_sleeperyz_p = pg_fetch_result($query_train2_res1, 0, 5);
$t4_sleeperyz_p = pg_fetch_result($query_train2_res2, 0, 5);
$sleeperyz2_p = $t4_sleeperyz_p - $t3_sleeperyz_p;
$t3_sleeperyx_p = pg_fetch_result($query_train2_res1, 0, 6);
$t4_sleeperyx_p = pg_fetch_result($query_train2_res2, 0, 6);
$sleeperyx2_p = $t4_sleeperyx_p - $t3_sleeperyx_p;
$t3_sleeperrs_p = pg_fetch_result($query_train2_res1, 0, 7);
$t4_sleeperrs_p = pg_fetch_result($query_train2_res2, 0, 7);
$sleeperrs2_p = $t4_sleeperrs_p - $t3_sleeperrs_p;
$t3_sleeperrx_p = pg_fetch_result($query_train2_res1, 0, 8);
$t4_sleeperrx_p = pg_fetch_result($query_train2_res2, 0, 8);
$sleeperrx2_p = $t4_sleeperrx_p - $t3_sleeperrx_p;
//outout as a table
echo "<table border=\"1\">";
echo "<tr>";
echo "<td>train_num</td><td>start_station</td><td>end_station</td><td>start_time</td><td>end_time</td><td>seaty</td><td>seatr</td><td>sleeperys</td><td>sleeperyz</td><td>sleeperyx</td><td>sleeperrs</td><td>sleeperrx</td>";
echo "</tr>";
echo "<td>".$train_num2."</td><td>".$start_station2."</td><td>".$end_station2."</td><td>".$dep_timestamp2."</td><td>".$arr_timestamp2."</td><td>".$seaty2_p."</td><td>".$seatr2_p."</td><td>".$sleeperys2_p."</td><td>".$sleeperyz2_p."</td><td>".$sleeperyx2_p."</td><td>".$sleeperrs2_p."</td><td>".$sleeperrx2_p."</td>";
echo "</tr>";
echo "</table>";

echo "Now output all the ticket option using a table. <br>\n";
//what to show???
//mul_order_process.php
//user
//train_num1,date1,start_station1_id,end_station1_id,seat_type1,price1
//train_num2,date2,start_station2_id,end_station2_id,seat_type2,price2
//for seat_type1, for seat_type2: price1 and price2 are generated
//seaty1_p, seatr1_p...
//seaty2_p, seatr2_p...
$seat1_arr = array($seaty1_p, $seatr1_p, $sleeperys1_p, $sleeperyz1_p, $sleeperyx1_p, $sleeperrs1_p, $sleeperrx1_p);
$seat2_arr = array($seaty2_p, $seatr2_p, $sleeperys2_p, $sleeperyz2_p, $sleeperyx2_p, $sleeperrs2_p, $sleeperrx2_p);
//echo var_dump($seat1_arr) . "<br>\n";
//echo var_dump($seat2_arr) . "<br>\n";
echo "<table border=\"1\">";
echo "<tr>";
echo "<td></td><td>seaty1</td><td>seatr1</td><td>sleeperys1</td><td>sleeperyz1</td><td>sleeperyx1</td><td>sleeperrs1</td><td>sleeperrx1</td>";
echo "</tr>";
for($i=0;$i<7;$i++){
	echo "<tr>";
	switch($i){
	case 0:$row="seaty";break;
	case 1:$row="seatr";break;
	case 2:$row="sleeperys";break;
	case 3:$row="sleeperyz";break;
	case 4:$row="sleeperyx";break;
	case 5:$row="sleeperrs";break;
	case 6:$row="sleeperrx";break;
	}
	echo "<td>".$row."2</td>";
	//in one row, every col
	for($j=0;$j<7;$j++){
		switch($j){
		case 0:$col="seaty";break;
        	case 1:$col="seatr";break;
        	case 2:$col="sleeperys";break;
        	case 3:$col="sleeperyz";break;
        	case 4:$col="sleeperyx";break;
        	case 5:$col="sleeperrs";break;
		case 6:$col="sleeperrx";break;
		}
		$total_price = $seat1_arr[$j] + $seat2_arr[$i] + 10;
		if($seat1_arr[$j]==0 || $seat2_arr[$i]==0){
			$real_total_price = "N/A";
		}else{
			//output as a href
			$real_total_price = "<a href=\"http://localhost:8080/CNTRAK_230513/process/mul_order_process.php?user=".$user."&train_num1=".$train_num1."&date1=".$date1."&start_station1_id=".$start_station1_id."&end_station1_id=".$end_station1_id."&seat_type1=".$col."&price1=".($seat1_arr[$j]+5)."&train_num2=".$train_num2."&date2=".$date2."&start_station2_id=".$start_station2_id."&end_station2_id=".$end_station2_id."&seat_type2=".$row."&price2=".($seat2_arr[$i]+5)."\">".$total_price."</a>";
		}
		echo "<td>";
		echo $real_total_price;
		echo "</td>";
	}
	echo "</tr>";
}
echo "</table>";
		
?>
</body>
</html>
