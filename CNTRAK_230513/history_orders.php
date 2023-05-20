<html>
<head>
<meta charset="utf-8">
<title>History Orders</title>
</head>
<body>
<?php
$user = $_GET["user"];
echo "user is " . $user . "<br>\n";

echo "<a href=\"http://localhost:8080/CNTRAK_230513/admin_home_page.php\">return to home_page</a><br>";
$conn = pg_connect("host=localhost dbname=postgres user=postgres password=123")or die("Could not connect! " . pg_last_error());

echo "<br><br>";
echo "The historical order of the user are as follows:<br>\n";
$query_order_res = pg_query($conn, "select * from orders where username='".$user."'")or die("Could not connect! " . pg_last_error());
$query_order_res_num = pg_num_rows($query_order_res);
if($query_order_res_num==0){
	echo "No orders of the user!<br>\n";
}else{
	//get the start_station and end_station from all_stations
	echo "<table border=\"1\">";
	echo "<tr><td>oid</td><td>train_num</td><td>order_date</td><td>start_station</td><td>end_station</td><td>seat_type</td><td>price</td><td>order_time</td><td>status</td></tr>";
	for($i=0;$i<$query_order_res_num;$i++){
		$start_station_id = pg_fetch_result($query_order_res, $i, 4);
		$end_station_id = pg_fetch_result($query_order_res, $i, 6);
		$start_station_res = pg_query($conn, "select station from all_stations where sid=".$start_station_id)or die("Could not query start_station! " . pg_last_error());
		$start_station_res = pg_query($conn, "select station from all_stations where sid=".$end_station_id)or die("Could not query end_station! " . pg_last_error());
		$start_station = pg_fetch_result($start_station_res, 0, 0);
		$end_station = pg_fetch_result($end_station_res, 0, 0);

		$oid = pg_fetch_result($query_order_res, $i, 0);
		$train_num = pg_fetch_result($query_order_res, $i, 2);
		$order_date = pg_fetch_result($query_order_res, $i, 3);
		$seat_type = pg_fetch_result($query_order_res, $i, 8);
		$price = pg_fetch_result($query_order_res, $i, 9);
		$order_time = pg_fetch_result($query_order_res, $i, 10);
		$status = pg_fetch_result($query_order_res, $i, 11);
		echo "<tr><td>".$oid."</td><td>".$train_num."</td><td>".$order_date."</td><td>".$start_station."</td><td>".$end_station."</td><td>".$seat_type."</td><td>".$price."</td><td>".$order_time."</td><td>".$status."</td></tr>";
	}
}
?>
</body>
</html>
