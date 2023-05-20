<html>
<head>
<meta charset="utf-8">
<title>Query Train Between Cities</title>
</head>
<body>
<?php
//QUERING TRAIN BETWEEN CITIES
$user = $_GET["user"];
$start_city = $_GET["start_city"];
$end_city = $_GET["end_city"];
$date = $_GET["date"];
$time = $_GET["time"];

echo "<h3>Welcome " . $user . "!</h3>";
echo "Please input start city, end_city, date and time.<br><br>\n";
echo "<a href=\"http://localhost:8080/CNTRAK_230513/home_page.php?user=".$user."\">return to home page</a><br>\n";

echo "<br>\n";
echo "User is " . $user . "<br>\n";
echo "Start_city is " . $start_city . "<br>\n";
echo "End_city is " . $end_city . "<br>\n";
echo "Date is " . $date . "<br>\n";
echo "Time is " . $time . "<br><br>\n";
?>
<form action="" method="get">
user: <input type="text" name="user" value="<?php echo $user;?>">
<br>
start_city: <input type="text" name="start_city" value="<?php echo $start_city;?>">
<br>
end_city: <input type="text" name="end_city" value="<?php echo $end_city;?>">
<br>
date: <input type="text" name="date" value="<?php echo $date;?>">
<br>
time: <input type="text" name="time" value="<?php echo $time;?>">
<br>
<input type="submit" value="OK"><br><br>
<?php
//connect the database
echo "Connecting the database...<br>\n";
$conn = pg_connect("host=localhost dbname=postgres user=postgres password=123")or die("Could not connect! " . pg_last_error());
echo "Connect success!<br>\n";

$nextdate_res = pg_query($conn, "select date '".$date."' + interval '1 day';");
$nextdate_timestamp = pg_fetch_result($nextdate_res, 0, 0);
$nextdate = substr($nextdate_timestamp, 0, 10);
echo "nextdate is " . $nextdate . "<br>\n";
echo "<a href=\"http://localhost:8080/CNTRAK_230513/query_train_between_cities.php?user=".$user."&start_city=".$end_city."&end_city=".$start_city."&date=".$nextdate."&time=00:00\">Click here to query the return trains.</a><br>\n";

//todo: direct query
//query station in start_city
$start_city_stations_res = pg_query($conn, "select sid, station from all_stations where station_city = '" . $start_city . "'")or die("Could not query! " . pg_last_error());
echo "Stations in start city found!<br>\n";
$start_city_stations_res_num = pg_num_rows($start_city_stations_res);
echo "There are " . $start_city_stations_res_num . " stations in start city.<br>\n";
//query station in end_city
$end_city_stations_res = pg_query($conn, "select sid, station from all_stations where station_city = '" . $end_city . "'")or die("Could not query! " . pg_last_error());
echo "Stations in end city found!<br>\n";
$end_city_stations_res_num = pg_num_rows($end_city_stations_res);
echo "There are " . $end_city_stations_res_num . " stations in end city.<br>\n";

//output the route between two cities in a table
echo "<br>\n";
echo "The direct route between " . $start_city . " and " . $end_city . " are as follows:<br>\n";
echo "<table border=\"1\">";
echo "<tr>";
echo "<td>train_num</td><td>start_station_id</td><td>start_station</td><td>dep_time</td><td>end_station_id</td><td>end_station</td><td>arr_time</td><td>remain_ticket</td><td>seaty</td><td>seatr</td><td>sleeperys</td><td>sleeperyz</td><td>sleeperyx</td><td>sleeperrs</td><td>sleeperrx</td>";
echo "</tr>";
for($i=0;$i<$start_city_stations_res_num;$i++){
	$start_station_id = pg_fetch_result($start_city_stations_res, $i, 0);
	$start_station = pg_fetch_result($start_city_stations_res, $i, 1);
	for($j=0;$j<$end_city_stations_res_num;$j++){
		$end_station_id = pg_fetch_result($end_city_stations_res, $j, 0);
		$end_station = pg_fetch_result($end_city_stations_res, $j, 1);
		$query_direct_route_res = pg_query($conn, "select t1.train_num, t1.train_station_id, t1.station, t1.dep_time, t2.train_station_id, t2.station, t2.arr_time, (t2.seaty_p - t1.seaty_p) as seaty_p, (t2.seatr_p - t1.seatr_p) as seatr_p, (t2.sleeperys_p - t1.sleeperys_p) as sleeperys_p, (t2.sleeperyz_p - t1.sleeperyz_p) as sleeperyz_p, (t2.sleeperyx_p - t1.sleeperyx_p) as sleeperyx_p, (t2.sleeperrs_p - t1.sleeperrs_p) as sleeperrs_p, (t2.sleeperrx_p - t1.sleeperrx_p) as sleeperrx_p, t1.station_valid, t2.station_valid from trains as t1, trains as t2 where t1.dep_time >= '".$time."' and t1.train_id = t2.train_id and t1.station_id = ".$start_station_id." and t2.station_id = ".$end_station_id." and t1.train_station_id < t2.train_station_id order by seaty_p asc, seatr_p asc, sleeperys_p asc, sleeperrs_p asc, (t2.running_time - t1.running_time) asc, (date '2023-05-13' + t1.dep_time) asc limit 20")or die("Could not search! " . pg_last_error());
		$query_direct_route_res_num = pg_num_rows($query_direct_route_res);

		//output the train_num, start_station_id, start_station, end_station_id, end_station
		/*
		while($line=pg_fetch_array($query_direct_route_res, null, PGSQL_NUM)){
			echo "<tr>";
			foreach($line as $col_value){
				echo "<td>".$col_value."</td>";
			}
			echo "</tr>";
		}
		*/
		for($k=0;$k<$query_direct_route_res_num;$k++){
			$res_train_num = pg_fetch_result($query_direct_route_res, $k, 0);
			$res_t1_station_id = pg_fetch_result($query_direct_route_res, $k, 1);
			$res_t1_station = pg_fetch_result($query_direct_route_res, $k, 2);
			$res_dep_time = pg_fetch_result($query_direct_route_res, $k, 3);
			$res_t2_station_id = pg_fetch_result($query_direct_route_res, $k, 4);
			$res_t2_station = pg_fetch_result($query_direct_route_res, $k, 5);
			$res_arr_time = pg_fetch_result($query_direct_route_res, $k, 6);
			$res_seaty_p = pg_fetch_result($query_direct_route_res, $k, 7);
			$res_seatr_p = pg_fetch_result($query_direct_route_res, $k, 8);
			$res_sleeperys_p = pg_fetch_result($query_direct_route_res, $k, 9);
			$res_sleeperyz_p = pg_fetch_result($query_direct_route_res, $k, 10);
			$res_sleeperyx_p = pg_fetch_result($query_direct_route_res, $k, 11);
			$res_sleeperrs_p = pg_fetch_result($query_direct_route_res, $k, 12);
			$res_sleeperrx_p = pg_fetch_result($query_direct_route_res, $k, 13);
			$res_t1_station_valid = pg_fetch_result($query_direct_route_res, $k, 14);
			$res_t2_station_valid = pg_fetch_result($query_direct_route_res, $k, 15);
			//show the remain ticket num using date, train_num, and t1_station_id, and t2_station_id
			$query_ticket_res = pg_query($conn, "select * from ticket".$res_train_num." where ticket_date='".$date."' and ticket_start_station_id <= ".$res_t1_station_id." and ticket_end_station_id >= ".$res_t2_station_id)or die("Could not find tickets! " . pg_last_error());
			$query_ticket_res_num = pg_num_rows($query_ticket_res);
			//maybe more than one row
			if($query_ticket_res_num==0){
				$remain_ticket=0;
			}else{
				if($query_ticket_res_num==1){
					//just one line
					$seaty_num = pg_fetch_result($query_ticket_res, 0, 3);
					$seatr_num = pg_fetch_result($query_ticket_res, 0, 4);
					$sleeperys_num = pg_fetch_result($query_ticket_res, 0, 5);
					$sleeperyz_num = pg_fetch_result($query_ticket_res, 0, 6);
					$sleeperyx_num = pg_fetch_result($query_ticket_res, 0, 7);
					$sleeperrs_num = pg_fetch_result($query_ticket_res, 0, 8);
					$sleeperrx_num = pg_fetch_result($query_ticket_res, 0, 9);
					
					$remain_ticket = $seaty_num + $seatr_num + $sleeperys_num + $sleeperyz_num + $sleeperyx_num + $sleeperrs_num + $sleeperrx_num;
					//echo "remain_ticket: " . $remain_ticket."<br>\n";
				}else{
					//more than one line
					$seaty_num = 0;
					$seatr_num = 0;
					$sleeperys_num = 0;
					$sleeperyz_num = 0;
					$sleeperyx_num = 0;
					$sleeperrs_num = 0;
					$sleeperrx_num = 0;
					for($h=0;$h<$query_ticket_res_num;$h++){
						$seaty_num += pg_fetch_result($query_ticket_res, $h, 3);
						//echo $seaty_num . "x<br>\n";
						//echo pg_fetch_result($query_ticket_res, $h, 3) . "<br>\n";
						
						$seatr_num += pg_fetch_result($query_ticket_res, $h, 4);
						//echo $seatr_num . "x<br>\n";
						//echo pg_fetch_result($query_ticket_res, $h, 4) . "<br>\n";
						
						$sleeperys_num += pg_fetch_result($query_ticket_res, $h, 5);
						//echo $sleeperys_num . "x<br>\n";
						//echo pg_fetch_result($query_ticket_res, $h, 5) . "<br>\n";
						
						$sleeperyz_num += pg_fetch_result($query_ticket_res, $h, 6);
						//echo $sleeperyz_num . "x<br>\n";
						//echo pg_fetch_result($query_ticket_res, $h, 6) . "<br>\n";
						
						$sleeperyx_num += pg_fetch_result($query_ticket_res, $h, 7);
						//echo $sleeperyx_num . "x<br>\n";
						//echo pg_fetch_result($query_ticket_res, $h, 7) . "<br>\n";
						
						$sleeperrs_num += pg_fetch_result($query_ticket_res, $h, 8);
						//echo $sleeperrs_num . "x<br>\n";
						//echo pg_fetch_result($query_ticket_res, $h, 8) . "<br>\n";
						
						$sleeperrx_num += pg_fetch_result($query_ticket_res, $h, 9);
						//echo $sleeperrx_num . "x<br>\n";
						//echo pg_fetch_result($query_ticket_res, $h, 9) . "<br>\n";
						//echo "next!<br>\n";
					}
					$remain_ticket = $seaty_num + $seatr_num + $sleeperys_num + $sleeperyz_num + $sleeperyx_num + $sleeperrs_num + $sleeperrx_num;
					echo "remain_ticket: " . $remain_ticket."<br>\n";
				}
			}
			if($res_t1_station_valid==0 ||$res_t2_station_valid==0){
				$remain_ticket=0;
			}
			if($remain_ticket!=0){
				$real_remain_ticket="<a href=\"http://localhost:8080/CNTRAK_230513/order_ticket.php?user=".$user."&train_num=".$res_train_num."&date=".$date."&start_station_id=".$res_t1_station_id."&end_station_id=".$res_t2_station_id."\">".$remain_ticket."</a>";
			}else{
				$real_remain_ticket=$remain_ticket;
			}
			echo "<tr>";
			echo "<td>".$res_train_num."</td><td>".$res_t1_station_id."</td><td>".$res_t1_station."</td><td>".$res_dep_time."</td><td>".$res_t2_station_id."</td><td>".$res_t2_station."</td><td>".$res_arr_time."</td><td>".$real_remain_ticket."</td><td>".$res_seaty_p."</td><td>".$res_seatr_p."</td><td>".$res_sleeperys_p."</td><td>".$res_sleeperyz_p."</td><td>".$res_sleeperyx_p."</td><td>".$res_sleeperrs_p."</td><td>".$res_sleeperrx_p."</td>";
			echo "</tr>";
			pg_free_result($query_ticket_res);
		}
		pg_free_result($query_direct_route_res);
	}
}
echo "</table>";

//todo: transfer query
echo "<br>\n";
echo "The indirect routes between " . $start_city ." and " . $end_city . " are as follows:<br>\n";
//output
echo "<table border=\"1\">";
echo "<tr>";
echo "<td>t1_train_num</td><td>t1_start_station</td><td>t1_dep_time</td><td>t1_end_station</td><td>t1_arr_time</td>";
echo "<td>t3_train_num</td><td>t3_start_station</td><td>t3_dep_time</td><td>t3_end_station</td><td>t3_arr_time</td>";
echo "<td>remain ticket</td>";
echo "</tr>";
$query_indirect_route_res = pg_query($conn, "(select t1.train_num, t1.train_station_id, t1.station, t1.dep_time, t2.train_station_id, t2.station, t2.arr_time, t3.train_num, t3.train_station_id, t3.station, t3.dep_time, t4.train_station_id, t4.station, t4.arr_time, t1.running_time, t2.running_time, t3.running_time, t4.running_time, ((t2.seaty_p - t1.seaty_p)+(t4.seaty_p - t3.seaty_p)) as seaty_p, ((t2.seatr_p - t1.seatr_p)+(t4.seatr_p - t3.seatr_p)) as seatr_p, ((t2.sleeperys_p - t1.sleeperys_p)+(t4.sleeperys_p - t3.sleeperys_p)) as sleeperys_p, ((t4.sleeperrs_p - t3.sleeperrs_p)+(t2.sleeperrs_p - t1.sleeperrs_p)) as sleeperrs_p, t1.station_valid, t2.station_valid, t3.station_valid, t4.station_valid, t1.stop_time, t3.stop_time from trains as t1, trains as t2, trains as t3, trains as t4 where t1.dep_time >= '".$time."' and t1.train_id = t2.train_id and t3.train_id = t4.train_id and t1.station_city = '".$start_city."' and t4.station_city = '".$end_city."' and t1.train_station_id < t2.train_station_id and t3.train_station_id < t4.train_station_id and t2.station_id = t3.station_id and (t3.dep_time - t2.arr_time) >= interval '1 hour' and (t3.dep_time - t2.arr_time) <= interval '4 hours' order by seaty_p asc, seatr_p asc, sleeperys_p asc, sleeperrs_p asc, ((t2.running_time - t1.running_time) + (t3.dep_time - t2.arr_time) + (t4.running_time - t3.running_time)) asc, (date '".$date."' + t1.dep_time) asc limit 20) union (select t1.train_num, t1.train_station_id, t1.station, t1.dep_time, t2.train_station_id, t2.station, t2.arr_time, t3.train_num, t3.train_station_id, t3.station, t3.dep_time, t4.train_station_id, t4.station, t4.arr_time, t1.running_time, t2.running_time, t3.running_time, t4.running_time, ((t2.seaty_p - t1.seaty_p)+(t4.seaty_p - t3.seaty_p)) as seaty_p, ((t2.seatr_p - t1.seatr_p)+(t4.seatr_p - t3.seatr_p)) as seatr_p, ((t2.sleeperys_p - t1.sleeperys_p)+(t4.sleeperys_p - t3.sleeperys_p)) as sleeperys_p, ((t4.sleeperrs_p - t3.sleeperrs_p)+(t2.sleeperrs_p - t1.sleeperrs_p)) as sleeperrs_p, t1.station_valid, t2.station_valid, t3.station_valid, t4.station_valid, t1.stop_time, t3.stop_time from trains as t1, trains as t2, trains as t3, trains as t4 where t1.dep_time >= '".$time."' and t1.train_id = t2.train_id and t3.train_id = t4.train_id and t1.station_city = '".$start_city."' and t4.station_city = '".$end_city."' and t1.train_station_id < t2.train_station_id and t3.train_station_id < t4.train_station_id and t2.station_id != t3.station_id and t2.station_city = t3.station_city and (t3.dep_time - t2.arr_time) >= interval '2 hours' and (t3.dep_time - t2.arr_time) <= interval '4 hours' order by seaty_p asc, seatr_p asc, sleeperys_p asc, sleeperrs_p asc, ((t2.running_time - t1.running_time) + (t3.dep_time - t2.arr_time) + (t4.running_time - t3.running_time)) asc, (date '".$date."' + t1.dep_time) asc ) limit 20")or die("Could not query direct trains! " . pg_last_error());
$query_indirect_route_res_num = pg_num_rows($query_indirect_route_res);

//output the train_num, start_station_id, start_station, end_station_id, end_station... for every train
for($k=0;$k<$query_indirect_route_res_num;$k++){
	$res_t1_train_num = pg_fetch_result($query_indirect_route_res, $k, 0);

	$res_t1_station_id = pg_fetch_result($query_indirect_route_res, $k, 1);

        $res_t1_station = pg_fetch_result($query_indirect_route_res, $k, 2);
	$res_t1_dep_time = pg_fetch_result($query_indirect_route_res, $k, 3);

        $res_t2_station_id = pg_fetch_result($query_indirect_route_res, $k, 4);
	
	$res_t2_station = pg_fetch_result($query_indirect_route_res, $k, 5);
	$res_t2_arr_time = pg_fetch_result($query_indirect_route_res, $k, 6);
	//echo "res_t2_arr_time: " . $res_t2_arr_time . "<br>\n";
	$res_t3_train_num = pg_fetch_result($query_indirect_route_res, $k, 7);
	
	$res_t3_station_id = pg_fetch_result($query_indirect_route_res, $k, 8);
	
	$res_t3_station = pg_fetch_result($query_indirect_route_res, $k, 9);
        $res_t3_dep_time = pg_fetch_result($query_indirect_route_res, $k, 10);
	//echo "res_t3_dep_time: " . $res_t3_dep_time . "<br>\n";
	$res_t4_station_id = pg_fetch_result($query_indirect_route_res, $k, 11);
	
	$res_t4_station = pg_fetch_result($query_indirect_route_res, $k, 12);
	$res_t4_arr_time = pg_fetch_result($query_indirect_route_res, $k, 13);
	$res_t1_running_time = pg_fetch_result($query_indirect_route_res, $k, 14);
	$res_t2_running_time = pg_fetch_result($query_indirect_route_res, $k, 15);
	$res_t3_running_time = pg_fetch_result($query_indirect_route_res, $k, 16);
	$res_t4_running_time = pg_fetch_result($query_indirect_route_res, $k, 17);
	$res_t1_station_valid = pg_fetch_result($query_indirect_route_res, $k, 22);
	$res_t2_station_valid = pg_fetch_result($query_indirect_route_res, $k, 23);
	$res_t3_station_valid = pg_fetch_result($query_indirect_route_res, $k, 24);
	$res_t4_station_valid = pg_fetch_result($query_indirect_route_res, $k, 25);
	$res_t1_stop_time = pg_fetch_result($query_indirect_route_res, $k, 26);
	$res_t3_stop_time = pg_fetch_result($query_indirect_route_res, $k, 27);
	$query_ticket_t1_res = pg_query($conn, "select * from ticket".$res_t1_train_num." where ticket_date='".$date."' and ticket_start_station_id <= ".$res_t1_station_id." and ticket_end_station_id >= ".$res_t2_station_id)or die("Could not search ticket of the first train! " . pg_last_error());
	$query_ticket_t3_res = pg_query($conn, "select * from ticket".$res_t3_train_num." where ticket_date='".$date."' and ticket_start_station_id <= ".$res_t3_station_id." and ticket_end_station_id >= ".$res_t4_station_id)or die("Could not search ticket of the first train! " . pg_last_error());
	$query_ticket_t1_res_num = pg_num_rows($query_ticket_t1_res);
	$query_ticket_t3_res_num = pg_num_rows($query_ticket_t3_res);
        //maybe more than one row
        if($query_ticket_t1_res_num==0){
        	$remain_ticket_t1=0;
        }else{
                if($query_ticket_t1_res_num==1){
                	//just one line
                        $seaty_num_t1 = pg_fetch_result($query_ticket_t1_res, 0, 3);
                        $seatr_num_t1 = pg_fetch_result($query_ticket_t1_res, 0, 4);
                        $sleeperys_num_t1 = pg_fetch_result($query_ticket_t1_res, 0, 5);
                        $sleeperyz_num_t1 = pg_fetch_result($query_ticket_t1_res, 0, 6);
                        $sleeperyx_num_t1 = pg_fetch_result($query_ticket_t1_res, 0, 7);
                        $sleeperrs_num_t1 = pg_fetch_result($query_ticket_t1_res, 0, 8);
                        $sleeperrx_num_t1 = pg_fetch_result($query_ticket_t1_res, 0, 9);
			$remain_ticket_t1 = $seaty_num_t1 + $seatr_num_t1 + $sleeperys_num_t1 + $sleeperyz_num_t1 + $sleeperyx_num_t1 + $sleeperrs_num_t1 + $sleeperrx_num_t1;
			if($res_t1_station_valid==0||$res_t2_station_valid==0){
                                $remain_ticket_t1 = 0;
                        }
		}else{
                        //more than one line
                        $seaty_num_t1 = 0;
                        $seatr_num_t1 = 0;
                        $sleeperys_num_t1 = 0;
                        $sleeperyz_num_t1 = 0;
                        $sleeperyx_num_t1 = 0;
                        $sleeperrs_num_t1 = 0;
                        $sleeeprrx_num_t1 = 0;
                        for($h=0;$h<$query_ticket_t1_res_num;$h++){
                        	$seaty_num_t1 += pg_fetch_result($query_ticket_t1_res, $h, 3);
                                $seatr_num_t1 += pg_fetch_result($query_ticket_t1_res, $h, 4);
                                $sleeperys_num_t1 += pg_fetch_result($query_ticket_t1_res, $h, 5);
                                $sleeperyz_num_t1 += pg_fetch_result($query_ticket_t1_res, $h, 6);
                                $sleeperyx_num_t1 += pg_fetch_result($query_ticket_t1_res, $h, 7);
                                $sleeperrs_num_t1 += pg_fetch_result($query_ticket_t1_res, $h, 8);
                                $sleeperrx_num_t1 += pg_fetch_result($query_ticket_t1_res, $h, 9);
                        }
			$remain_ticket_t1 = $seaty_num_t1 + $seatr_num_t1 + $sleeperys_num_t1 + $sleeperyz_num_t1 + $sleeperyx_num_t1 + $sleeperrs_num_t1 + $sleeperrx_num_t1;
			if($res_t1_station_valid==0||$res_t2_station_valid==0){
				$remain_ticket_t1 = 0;
			}
                }
	}
	if($query_ticket_t3_res_num==0){
                $remain_ticket_t3=0;
        }else{
                if($query_ticket_t3_res_num==1){
                        //just one line
                        $seaty_num_t3 = pg_fetch_result($query_ticket_t3_res, 0, 3);
                        $seatr_num_t3 = pg_fetch_result($query_ticket_t3_res, 0, 4);
                        $sleeperys_num_t3 = pg_fetch_result($query_ticket_t3_res, 0, 5);
                        $sleeperyz_num_t3 = pg_fetch_result($query_ticket_t3_res, 0, 6);
                        $sleeperyx_num_t3 = pg_fetch_result($query_ticket_t3_res, 0, 7);
                        $sleeperrs_num_t3 = pg_fetch_result($query_ticket_t3_res, 0, 8);
                        $sleeperrx_num_t3 = pg_fetch_result($query_ticket_t3_res, 0, 9);
			$remain_ticket_t3 = $seaty_num_t3 + $seatr_num_t3 + $sleeperys_num_t3 + $sleeperyz_num_t3 + $sleeperyx_num_t3 + $sleeperrs_num_t3 + $sleeperrx_num_t3;
			if($res_t3_station_valid==0||$res_t4_station_valid==0){
                                $remain_ticket_t3 = 0;
                        }
                }else{
                        //more than one line
                        $seaty_num_t3 = 0;
                        $seatr_num_t3 = 0;
                        $sleeperys_num_t3 = 0;
                        $sleeperyz_num_t3 = 0;
                        $sleeperyx_num_t3 = 0;
                        $sleeperrs_num_t3 = 0;
                        $sleeeprrx_num_t3 = 0;
                        for($h=0;$h<$query_ticket_t3_res_num;$h++){
                                $seaty_num_t3 += pg_fetch_result($query_ticket_t3_res, $h, 3);
                                $seatr_num_t3 += pg_fetch_result($query_ticket_t3_res, $h, 4);
                                $sleeperys_num_t3 += pg_fetch_result($query_ticket_t3_res, $h, 5);
                                $sleeperyz_num_t3 += pg_fetch_result($query_ticket_t3_res, $h, 6);
                                $sleeperyx_num_t3 += pg_fetch_result($query_ticket_t3_res, $h, 7);
                                $sleeperrs_num_t3 += pg_fetch_result($query_ticket_t3_res, $h, 8);
                                $sleeperrx_num_t3 += pg_fetch_result($query_ticket_t3_res, $h, 9);
                        }
			$remain_ticket_t3 = $seaty_num_t3 + $seatr_num_t3 + $sleeperys_num_t3 + $sleeperyz_num_t3 + $sleeperyx_num_t3 + $sleeperrs_num_t3 + $sleeperrx_num_t3;
			if($res_t3_station_valid==0||$res_t4_station_valid==0){
				$remain_ticket_t3 = 0;
			}
                }
	}
	$remain_ticket = min($remain_ticket_t1, $remain_ticket_t3);
	//echo "remain_ticket is " . $remain_ticket . "<br>\n";

	$date1 = $date;
	$t1_t2_running_time_res = pg_query($conn, "select interval '".$res_t2_running_time."' - interval '".$res_t1_running_time."' - interval '".$res_t1_stop_time."'")or die("Could not sub! " . pg_last_error());
	$t1_t2_running_time = pg_fetch_result($t1_t2_running_time_res, 0, 0);
	pg_free_result($t1_t2_running_time_res);

	$t3_t4_running_time_res = pg_query($conn, "select interval '".$res_t4_running_time."' - interval '".$res_t3_running_time."' - interval '".$res_t3_stop_time."'")or die("Could not sub! " . pg_last_error());
        $t3_t4_running_time = pg_fetch_result($t3_t4_running_time_res, 0, 0);
	pg_free_result($t3_t4_running_time_res);

	$t2_t3_running_time_res = pg_query($conn, "select interval '".$res_t3_dep_time."' - interval '".$res_t2_arr_time."'")or die("Could not sub! " . pg_last_error());
        $t2_t3_running_time = pg_fetch_result($t2_t3_running_time_res, 0, 0);
        pg_free_result($t2_t3_running_time_res);

	//use query to get the timestamp, then extract date2
	$date2_res = pg_query($conn, "select date '".$date1."' + interval '".$res_t1_dep_time."' + interval '".$t1_t2_running_time."' + interval '".$t2_t3_running_time."'")or die("Could not calculate date2 res! " . pg_last_error());
	$date2_timestamp = pg_fetch_result($date2_res, 0, 0);
	//echo "date2_timestamp is " . $date2_timestamp . "<br>\n";
	$date2 = substr($date2_timestamp, 0, 10);
	pg_free_result($date2_res);
	//echo "date2 is " . $date2 . "<br>\n";
	
        if($remain_ticket!=0){
        	$real_remain_ticket="<a href=\"http://localhost:8080/CNTRAK_230513/order_ticket_mul.php?user=".$user."&train_num1=".$res_t1_train_num."&date1=".$date1."&start_station1_id=".$res_t1_station_id."&end_station1_id=".$res_t2_station_id."&train_num2=".$res_t3_train_num."&date2=".$date2."&start_station2_id=".$res_t3_station_id."&end_station2_id=".$res_t4_station_id."\">".$remain_ticket."</a>";
        }else{
                $real_remain_ticket=$remain_ticket;
        }

        echo "<tr>";
        echo "<td>".$res_t1_train_num."</td><td>".$res_t1_station."</td><td>".$res_t1_dep_time."</td><td>".$res_t2_station."</td><td>".$res_t2_arr_time."</td>";
	echo "<td>".$res_t3_train_num."</td><td>".$res_t3_station."</td><td>".$res_t3_dep_time."</td><td>".$res_t4_station."</td><td>".$res_t4_arr_time."</td>";
	echo "<td>".$real_remain_ticket."</td>";
        echo "</tr>";
}
pg_free_result($query_direct_route_res);
echo "</table>";
?>
</body>
</html>
