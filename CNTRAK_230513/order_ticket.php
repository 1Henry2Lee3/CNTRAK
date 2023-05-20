<html>
<head>
<meta charset="utf-8">
<title>Order Tickets</title>
</head>
<body>
<h3>Please confirm the order data.</h3>
if all correct, press 'ORDER'<br><br>
<?php
$user = $_GET["user"];
$train_num = $_GET["train_num"];
$date = $_GET["date"];
$start_station_id = $_GET["start_station_id"];
$end_station_id = $_GET["end_station_id"];
echo "<a href=\"http://localhost:8080/CNTRAK_230513/home_page.php?user=".$user."\">Return to home page</a><br>\n";
echo "user is " . $user . "<br>\n";
echo "train_num is " . $train_num . "<br>\n";
echo "date is " . $date . "<br>\n";
echo "start_station_id is " . $start_station_id . "<br>\n";
echo "end_station_id is " . $end_station_id . "<br>\n";
echo "<br>\n";
//connect to database
echo "Connecting to database...<br>\n";
$conn = pg_connect("host=localhost dbname=postgres user=postgres password=123")or die("Could not connect to database! " . pg_last_error());
echo "Successfully connected!<br>\n";

//search _en from all_trains, then output

$query_en_res = pg_query($conn, "select seaty_en, seatr_en, sleeperys_en, sleeperyz_en, sleeperyx_en, sleeperrs_en, sleeperrx_en from all_trains where train_num = '".$train_num."'")or die("Could not search _en! " . pg_last_error());

$query_ticket_res = pg_query($conn, "select * from ticket".$train_num." where ticket_date = '".$date."' and ticket_start_station_id <= ".$start_station_id." and ticket_end_station_id >= ".$end_station_id)or die("Could not search ticket! " . pg_last_error());

$query_ticket_res_num = pg_num_rows($query_ticket_res);
//echo "Query complete!<br>\n";
//train_num
//start_date, dep_time, start_station
//end_date  , arr_time, end_station
//seat_type, ticket_price
//order_price = 5*ticket_num
//price = ticket_price + order_price
$query_train_res1 = pg_query($conn, "select dep_time, station, seaty_p, seatr_p, sleeperys_p, sleeperyz_p, sleeperyx_p, sleeperrs_p, sleeperrx_p, running_time, train_num, station_id, arr_time from trains where train_num = '".$train_num."' and train_station_id = ".$start_station_id)or die("Could not query t1! " . pg_last_error());
$query_train_res2 = pg_query($conn, "select arr_time, station, seaty_p, seatr_p, sleeperys_p, sleeperyz_p, sleeperyx_p, sleeperrs_p, sleeperrx_p, running_time, train_num, station_id from trains where train_num = '".$train_num."' and train_station_id = ".$end_station_id)or die("Could not query t2! " . pg_last_error());
echo "the information of the order is as follows:<br>\n";
//output train_num, start_station, end_station, dep_timestamp, arr_timestamp, price...
$start_station = pg_fetch_result($query_train_res1, 0, 1);
$end_station = pg_fetch_result($query_train_res2, 0, 1);
//timestamp
$dep_time = pg_fetch_result($query_train_res1, 0, 0);
echo "dep_time is " . $dep_time . "<br>\n";
$dep_timestamp_res = pg_query($conn, "select date '".$date."' + interval '".$dep_time."'")or die("Could not add time! " . pg_last_error());
$dep_timestamp = pg_fetch_result($dep_timestamp_res, 0, 0);
$t1_running_time = pg_fetch_result($query_train_res1, 0, 9);
echo "t1_running_time is " . $t1_running_time . "<br>\n";
$t2_running_time = pg_fetch_result($query_train_res2, 0, 9);
echo "t2_running_time is " . $t2_running_time . "<br>\n";
$running_time_res = pg_query($conn, "select interval '".$t2_running_time."' - interval '".$t1_running_time."'")or die("Could not calculate time! " . pg_last_error());
$running_time = pg_fetch_result($running_time_res, 0, 0);
echo "running_time is " . $running_time . "<br>\n";
echo "dep_timestmp is " . $dep_timestamp . "<br>\n";
$arr_timestamp_res = pg_query($conn, "select timestamp '".$dep_timestamp."' + interval '".$running_time."'")or die("Could not add time! " . pg_last_error());
$arr_timestamp = pg_fetch_result($arr_timestamp_res, 0, 0);
echo "arr_timestamp is " . $arr_timestamp . "<br>\n";
//price
$t1_seaty_p = pg_fetch_result($query_train_res1, 0, 2);
$t2_seaty_p = pg_fetch_result($query_train_res2, 0, 2);
$seaty_p = $t2_seaty_p - $t1_seaty_p;
$t1_seatr_p = pg_fetch_result($query_train_res1, 0, 3);
$t2_seatr_p = pg_fetch_result($query_train_res2, 0, 3);
$seatr_p = $t2_seatr_p - $t1_seatr_p;
$t1_sleeperys_p = pg_fetch_result($query_train_res1, 0, 4);
$t2_sleeperys_p = pg_fetch_result($query_train_res2, 0, 4);
$sleeperys_p = $t2_sleeperys_p - $t1_sleeperys_p;
$t1_sleeperyz_p = pg_fetch_result($query_train_res1, 0, 5);
$t2_sleeperyz_p = pg_fetch_result($query_train_res2, 0, 5);
$sleeperyz_p = $t2_sleeperyz_p - $t1_sleeperyz_p;
$t1_sleeperyx_p = pg_fetch_result($query_train_res1, 0, 6);
$t2_sleeperyx_p = pg_fetch_result($query_train_res2, 0, 6);
$sleeperyx_p = $t2_sleeperyx_p - $t1_sleeperyx_p;
$t1_sleeperrs_p = pg_fetch_result($query_train_res1, 0, 7);
$t2_sleeperrs_p = pg_fetch_result($query_train_res2, 0, 7);
$sleeperrs_p = $t2_sleeperrs_p - $t1_sleeperrs_p;
$t1_sleeperrx_p = pg_fetch_result($query_train_res1, 0, 8);
$t2_sleeperrx_p = pg_fetch_result($query_train_res2, 0, 8);
$sleeperrx_p = $t2_sleeperrx_p - $t1_sleeperrx_p;
//outout as a table
echo "<table border=\"1\">";
echo "<tr>";
echo "<td>train_num</td><td>start_station</td><td>end_station</td><td>start_time</td><td>end_time</td><td>seaty</td><td>seatr</td><td>sleeperys</td><td>sleeperyz</td><td>sleeperyx</td><td>sleeperrs</td><td>sleeperrx</td>";
echo "</tr>";
echo "<td>".$train_num."</td><td>".$start_station."</td><td>".$end_station."</td><td>".$dep_timestamp."</td><td>".$arr_timestamp."</td><td>".$seaty_p."</td><td>".$seatr_p."</td><td>".$sleeperys_p."</td><td>".$sleeperyz_p."</td><td>".$sleeperyx_p."</td><td>".$sleeperrs_p."</td><td>".$sleeperrx_p."</td>";
echo "</tr>";
echo "</table>";
$order_price = 5;
echo "order_price: " . $order_price . "<br>\n";
echo "<br>\n";
if($query_ticket_res_num==0){
	//no ticket!
	echo "No ticket found!<br>\n";
}else{
	if($query_ticket_res_num==1){
		//output directly
		//echo "Only one res!<br>\n";
		
		$seaty_num = pg_fetch_result($query_ticket_res, 0, 3);
		$seatr_num = pg_fetch_result($query_ticket_res, 0, 4);
		$sleeperys_num = pg_fetch_result($query_ticket_res, 0, 5);
		$sleeperyz_num = pg_fetch_result($query_ticket_res, 0, 6);
		$sleeperyx_num = pg_fetch_result($query_ticket_res, 0, 7);
		$sleeperrs_num = pg_fetch_result($query_ticket_res, 0, 8);
		$sleeperrx_num = pg_fetch_result($query_ticket_res, 0, 9);

		//echo "Output the res...<br>\n";
		
		for($i=0;$i<7;$i++){
			$seat_type_en = pg_fetch_result($query_en_res, 0, $i);
			//echo "seat_type_en is " . $seat_type_en . "<br>\n";
			
			switch($i){
			case 0:$seat_type="seaty";$seat_type_num=$seaty_num;$seat_p=$order_price+$seaty_p;break;
			case 1:$seat_type="seatr";$seat_type_num=$seatr_num;$seat_p=$order_price+$seatr_p;break;
			case 2:$seat_type="sleeperys";$seat_type_num=$sleeperys_num;$seat_p=$order_price+$sleeperys_p;break;
			case 3:$seat_type="sleeperyz";$seat_type_num=$sleeperyz_num;$seat_p=$order_price+$sleeperyz_p;break;
			case 4:$seat_type="sleeperyx";$seat_type_num=$sleeperyx_num;$seat_p=$order_price+$sleeperyx_p;break;
			case 5:$seat_type="sleeperrs";$seat_type_num=$sleeperrs_num;$seat_p=$order_price+$sleeperrs_p;break;
			case 6:$seat_type="sleeperrx";$seat_type_num=$sleeperrx_num;$seat_p=$order_price+$sleeperrx_p;break;
			}
			//echo "seat_type is " . $seat_type . "<br>\n";
			//echo "seat_type_num is " . $seat_type_num . "<br>\n";
			if($seat_type_en==1){
				//output as a href
				echo "<a href=\"http://localhost:8080/CNTRAK_230513/process/order_process.php?user=".$user."&train_num=".$train_num."&date=".$date."&start_station_id=".$start_station_id."&end_station_id=".$end_station_id."&seat_type=".$seat_type."&price=".$seat_p."\">order ".$seat_type.", remain: ".$seat_type_num.", order price: ".$seat_p." </a><br>\n";
			}
		}
	}else{
		//add sum, then output
		//echo "More than one res!<br>\n";
		
		$seaty_num = 0;
		$seatr_num = 0;
		$sleeperys_num = 0;
		$sleeperyz_num = 0;
		$sleeperyx_num = 0;
		$sleeperrs_num = 0;
		$sleeperrx_num = 0;
		for($j=0;$j<$query_ticket_res_num;$j++){
			$seaty_num += pg_fetch_result($query_ticket_res, $j, 3);
                	$seatr_num += pg_fetch_result($query_ticket_res, $j, 4);
                	$sleeperys_num += pg_fetch_result($query_ticket_res, $j, 5);
                	$sleeperyz_num += pg_fetch_result($query_ticket_res, $j, 6);
                	$sleeperyx_num += pg_fetch_result($query_ticket_res, $j, 7);
                	$sleeperrs_num += pg_fetch_result($query_ticket_res, $j, 8);
			$sleeperrx_num += pg_fetch_result($query_ticket_res, $j, 9);
		}
		//output every seat_type if possible
		//echo "Output the seat...<br>\n";
                for($i=0;$i<7;$i++){
                        $seat_type_en = pg_fetch_result($query_en_res, 0, $i);
			switch($i){
                        case 0:$seat_type="seaty";$seat_type_num=$seaty_num;$seat_p=$order_price+$seaty_p;break;
                        case 1:$seat_type="seatr";$seat_type_num=$seatr_num;$seat_p=$order_price+$seatr_p;break;
                        case 2:$seat_type="sleeperys";$seat_type_num=$sleeperys_num;$seat_p=$order_price+$sleeperys_p;break;
                        case 3:$seat_type="sleeperyz";$seat_type_num=$sleeperyz_num;$seat_p=$order_price+$sleeperyz_p;break;
                        case 4:$seat_type="sleeperyx";$seat_type_num=$sleeperyx_num;$seat_p=$order_price+$sleeperyx_p;break;
                        case 5:$seat_type="sleeperrs";$seat_type_num=$sleeperrs_num;$seat_p=$order_price+$sleeperrs_p;break;
                        case 6:$seat_type="sleeperrx";$seat_type_num=$sleeperrx_num;$seat_p=$order_price+$sleeperrx_p;break;
                        }
                        if($seat_type_en==1){
                                //output as a href
                                echo "<a href=\"http://localhost:8080/CNTRAK_230513/process/order_process.php?user=".$user."&train_num=".$train_num."&date=".$date."&start_station_id=".$start_station_id."&end_station_id=".$end_station_id."&seat_type=".$seat_type."&price=".$seat_p."\">order ".$seat_type." remain: ".$seat_type_num.", order price: ".$seat_p." </a><br>\n";
                        }
		}
	}
}
pg_free_result($query_ticket_res);
pg_free_result($query_en_res);

pg_close($conn);
?>
</body>
</html>
