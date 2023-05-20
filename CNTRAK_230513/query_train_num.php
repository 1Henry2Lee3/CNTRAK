<html>
<head>
<meta charset="utf-8">
<title>Query train_number</title>
</head>
<body>
<?php
$user = $_GET["user"];
echo "user is " . $user . "<br>\n";
$train_num = $_POST["train_num"];
$date = $_POST["date"];
echo "train_num is " . $train_num . ", date is " . $date . ".<br>\n";
echo "<a href=\"http://localhost:8080/CNTRAK_230513/home_page.php?user=$user\">Return to home page</a><br>";
echo "<h3>Please enter train_number and date.</h3>";
?>
<form action="" method="post">
train number: <input type="text" name="train_num">
<br>
date: <input type="text" name="date" value="<?php echo date('Y-m-d', strtotime('+1 days')) ?>">
<input type="submit" value="OK">
</form>
<?php
//connect the database
echo "Connecting the database...<br>\n";
$conn = pg_connect("host=localhost dbname=postgres user=postgres password=123")or die("Could not connect! " . pg_last_error());
echo "Successfully connected!<br>\n";
//search the train information using $train_num
$train_num_res = pg_query($conn, "select train_station_id, station, arr_time, dep_time, stop_time, running_time, mileage, seat, sleepery, sleeperr, station_valid from trains where train_num = '" . $train_num . "' order by train_station_id asc")or die("Could not query! " . pg_last_error());
$train_num_res_row_num = pg_num_rows($train_num_res);
if($train_num!="" && $date!=""){
	if($train_num_res_row_num==0){
		//no result! re-enter
		echo "No result! Please re-enter.<br>\n";
	}else{
		echo "Data found! The information of train " . $train_num . ".<br>\n";
?>
		<table border="1">
		<tr>
		<td>id</td><td>station</td><td>arr_time</td><td>dep_time</td><td>stop_time</td><td>running_time</td><td>mileage</td><td>seat</td><td>sleepery</td><td>sleeperr</td><td>station_valid</td>
		</tr>
<?php
		//output the train_info as a table
		while($line=pg_fetch_array($train_num_res, null, PGSQL_NUM)){
			echo "<tr>";
			foreach($line as $col_value){
				echo "<td>";
				echo $col_value;
				echo "</td>";
			}
			echo "</tr>";
		}
		echo "</table>";

		//todo: make ticketxxxx first.---making
		//todo: table ticketxxx:
		//date ticket_start_station_id, ticket_end_station_id, seaty, seatr, sleeperys, sleeperyz, sleeperyx, sleeperrs, sleeperrx
		//todo: from start station to end station---ticket
		echo "querying ticket of " . $train_num . " on " . $date . "<br>\n";
		//todo: from start station to end station, if station_valid, then query ticketxxx for and date
		//todo: where date=$date, ticket_start_id<=start_id, ticket_end_id>=end_id
		//$train_num_res_row_num is the last station id
		//start station: first station in this case
		$start_station_id = pg_fetch_result($train_num_res, 0, 0);
		$start_station = pg_fetch_result($train_num_res, 0, 1);
		//end station: from the 2nd station to the last one
?>
		<table border="1">
		<tr>
		<td>station</td><td>seaty</td><td>seatr</td><td>sleeperys</td><td>sleeperyz</td><td>sleeperyx</td><td>sleeperrs</td><td>sleeperrx</td>
		</tr>
<?php
		for($i=1;$i<$train_num_res_row_num;$i++){
			$station_id = pg_fetch_result($train_num_res, $i, 0);
			//echo "Station id: " . $station_id;
			$station = pg_fetch_result($train_num_res, $i, 1);
			$station_valid = pg_fetch_result($train_num_res, $i, 10);
			//ticketxxx:
			//ticket_date, ticket_start_station_id, ticket_end_station_id, seaty, seatr, sleeperys, sleeperyz, sleeperyx, sleeperrs, sleeperrx
			$query_ticket_res = pg_query($conn, "select * from ticket" . $train_num . " where ticket_date='" . $date . "' and ticket_start_station_id <= " . $start_station_id . " and ticket_end_station_id >= " . $station_id) or die("Could not query tickets! " . pg_last_error());
			//REMEMBER: the result may be more than 1---so add tickets
			$query_ticket_res_num = pg_num_rows($query_ticket_res);
			//echo "There are " . $query_ticket_res_num . " kinds of tickets available.<br>\n";

			if($query_ticket_res_num!=0 && $station_valid==1){
				//echo "The tickets from start station to every station are as follows:<br>\n";
				//more than 1 result, add tickets
				if($query_ticket_res_num>1){
					$seatysum = 0;
					$seatrsum = 0;
					$sleeperyssum = 0;
					$sleeperyzsum = 0;
					$sleeperyxsum = 0;
					$sleeperrssum = 0;
					$sleeperrxsum = 0;
					for($j=0;$j<$query_ticket_res_num;$j++){
						$seatysum += pg_fetch_result($query_ticket_res, $j, 3);
						$seatrsum += pg_fetch_result($query_ticket_res, $j, 4);
						$sleeperyssum += pg_fetch_result($query_ticket_res, $j, 5);
						$sleeperyzsum += pg_fetch_result($query_ticket_res, $j, 6);
						$sleeperyxsum += pg_fetch_result($query_ticket_res, $j, 7);
						$sleeperrssum += pg_fetch_result($query_ticket_res, $j, 8);
						$sleeperrxsum += pg_fetch_result($query_ticket_res, $j, 9);
					}
					$seatyoutput = ($seatysum)?("<a href=\"http://localhost:8080/CNTRAK_230513/order_ticket.php?user=".$user."&train_num=".$train_num."&date=".$date."&start_station_id=".$start_station_id."&end_station_id=".$station_id."\">".$seatysum."</a>"):($seatysum);
					$seatroutput = ($seatrsum)?("<a href=\"http://localhost:8080/CNTRAK_230513/order_ticket.php?user=".$user."&train_num=".$train_num."&date=".$date."&start_station_id=".$start_station_id."&end_station_id=".$station_id."\">".$seatrsum."</a>"):($seatrsum);
					$sleeperysoutput = ($sleeperyssum)?("<a href=\"http://localhost:8080/CNTRAK_230513/order_ticket.php?user=".$user."&train_num=".$train_num."&date=".$date."&start_station_id=".$start_station_id."&end_station_id=".$station_id."\">".$sleeperyssum."</a>"):($sleeperyssum);
					$sleeperyzoutput = ($sleeperyzsum)?("<a href=\"http://localhost:8080/CNTRAK_230513/order_ticket.php?user=".$user."&train_num=".$train_num."&date=".$date."&start_station_id=".$start_station_id."&end_station_id=".$station_id."\">".$sleeperyzsum."</a>"):($sleeperyzsum);
					$sleeperyxoutput = ($sleeperyxsum)?("<a href=\"http://localhost:8080/CNTRAK_230513/order_ticket.php?user=".$user."&train_num=".$train_num."&date=".$date."&start_station_id=".$start_station_id."&end_station_id=".$station_id."\">".$sleeperyxsum."</a>"):($sleeperyxsum);
					$sleeperrsoutput = ($sleeperrssum)?("<a href=\"http://localhost:8080/CNTRAK_230513/order_ticket.php?user=".$user."&train_num=".$train_num."&date=".$date."&start_station_id=".$start_station_id."&end_station_id=".$station_id."\">".$sleeperrssum."</a>"):($sleeperrssum);
					$sleeperrxoutput = ($sleeperrxsum)?("<a href=\"http://localhost:8080/CNTRAK_230513/order_ticket.php?user=".$user."&train_num=".$train_num."&date=".$date."&start_station_id=".$start_station_id."&end_station_id=".$station_id."\">".$sleeperrxsum."</a>"):($sleeperrxsum);
					//then output the ticket
					echo "<tr>";
					echo "<td>".$station."</td><td>".$seatyoutput."</td><td>".$seatroutput."</td><td>".$sleeperysoutput."</td><td>".$sleeperyzoutput."</td><td>".$sleeperyxoutput."</td><td>".$sleeperrsoutput."</td><td>".$sleeperrxoutput."</td>";
					echo "</tr>";
				}else{
					//directly output the ticket
					$seatynum = pg_fetch_result($query_ticket_res, 0, 3);
					$seatrnum = pg_fetch_result($query_ticket_res, 0, 4);
                                        $sleeperysnum = pg_fetch_result($query_ticket_res, 0, 5);
                                        $sleeperyznum = pg_fetch_result($query_ticket_res, 0, 6);
                                        $sleeperyxnum = pg_fetch_result($query_ticket_res, 0, 7);
                                        $sleeperrsnum = pg_fetch_result($query_ticket_res, 0, 8);
					$sleeperrxnum = pg_fetch_result($query_ticket_res, 0, 9);
					$seatyoutput = ($seatynum)?("<a href=\"http://localhost:8080/CNTRAK_230513/order_ticket.php?user=".$user."&train_num=".$train_num."&date=".$date."&start_station_id=".$start_station_id."&end_station_id=".$station_id."\">".$seatynum."</a>"):($seatynum);
                                        $seatroutput = ($seatrnum)?("<a href=\"http://localhost:8080/CNTRAK_230513/order_ticket.php?user=".$user."&train_num=".$train_num."&date=".$date."&start_station_id=".$start_station_id."&end_station_id=".$station_id."\">".$seatrnum."</a>"):($seatrnum);
                                        $sleeperysoutput = ($sleeperysnum)?("<a href=\"http://localhost:8080/CNTRAK_230513/order_ticket.php?user=".$user."&train_num=".$train_num."&date=".$date."&start_station_id=".$start_station_id."&end_station_id=".$station_id."\">".$sleeperysnum."</a>"):($sleeperysnum);
                                        $sleeperyzoutput = ($sleeperyznum)?("<a href=\"http://localhost:8080/CNTRAK_230513/order_ticket.php?user=".$user."&train_num=".$train_num."&date=".$date."&start_station_id=".$start_station_id."&end_station_id=".$station_id."\">".$sleeperyznum."</a>"):($sleeperyznum);
                                        $sleeperyxoutput = ($sleeperyxnum)?("<a href=\"http://localhost:8080/CNTRAK_230513/order_ticket.php?user=".$user."&train_num=".$train_num."&date=".$date."&start_station_id=".$start_station_id."&end_station_id=".$station_id."\">".$sleeperyxnum."</a>"):($sleeperyxnum);
                                        $sleeperrsoutput = ($sleeperrsnum)?("<a href=\"http://localhost:8080/CNTRAK_230513/order_ticket.php?user=".$user."&train_num=".$train_num."&date=".$date."&start_station_id=".$start_station_id."&end_station_id=".$station_id."\">".$sleeperrsnum."</a>"):($sleeperrsnum);
                                        $sleeperrxoutput = ($sleeperrxnum)?("<a href=\"http://localhost:8080/CNTRAK_230513/order_ticket.php?user=".$user."&train_num=".$train_num."&date=".$date."&start_station_id=".$start_station_id."&end_station_id=".$station_id."\">".$sleeperrxnum."</a>"):($sleeperrxnum);
					//echo "Sleeperrxnum is :" . $sleeperrxnum . "<br>\n";
					//output the ticket
					echo "<tr>";
					echo "<td>".$station."</td><td>".$seatyoutput."</td><td>".$seatroutput."</td><td>".$sleeperysoutput."</td><td>".$sleeperyzoutput."</td><td>".$sleeperyxoutput."</td><td>".$sleeperrsoutput."</td><td>".$sleeperrxoutput."</td>";
					echo "</tr>";
				}
			}else{
				//result row num==0:found no ticket between two stations, or station not valid---output  zero
				echo "<tr>";
				echo "<td>".$station."</td><td>0</td><td>0</td><td>0</td><td>0</td><td>0</td><td>0</td><td>0</td>";
				echo "</tr>";
			}
		}//endfor every station
		echo "</table>";
	}
}else{
	echo "Please re-enter.<br>\n";
}
?>
</body>
</html>
