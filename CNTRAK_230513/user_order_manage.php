<html>
<head>
<meta charset="utf-8">
<title>User Order Manage</title>
</head>
<body>
<h3>Welcome user. Manage your orders here</h3>
<?php
$user = $_GET["user"];
echo "The user is " . $user . "<br>\n";

echo "<a href=\"http://localhost:8080/CNTRAK_230513/home_page.php?user=".$user."\">return to home page</a><br>\n";
echo "Connecting to database...<br>\n";
$conn = pg_connect("host=localhost dbname=postgres user=postgres password=123")or die("Could not connect! " . pg_last_error());
echo "Connect success!<br>\n";

//todo: querying the orders
$query_user_orders = pg_query($conn, "select * from orders where username='" . $user . "'")or die("Could not query orders! " . pg_last_error());
$query_user_orders_num = pg_num_rows($query_user_orders);
echo "There are " . $query_user_orders_num . " orders.<br>\n";
if($query_user_orders_num==0){
	//no result
	echo "No orders found!<br>\n";
}else{
	//fond result, output
	echo "Orders found!<br>\n";
	//output as a table
	echo "<table border=\"1\">";
	echo "<tr>";
	echo "<td>order_id</td><td>train_num</td><td>train_date</td><td>start_station</td><td>end_station</td><td>seat_type</td><td>price</td><td>order_time</td><td>status</td><td>cancel here</td>";
	echo "</tr>";
	//todo: click to cancel orders: use order_cancel.php?order_id=??? to process
	for($i=0;$i<$query_user_orders_num;$i++){
		//output every order
		$order_id = pg_fetch_result($query_user_orders, $i, 0);
		$train_num = pg_fetch_result($query_user_orders, $i, 2);
		$train_date = pg_fetch_result($query_user_orders, $i, 3);
		$start_station_id = pg_fetch_result($query_user_orders, $i, 4);
		$start_train_station_id = pg_fetch_result($query_user_orders, $i, 5);
		$end_station_id = pg_fetch_result($query_user_orders, $i, 6);
		$end_train_station_id = pg_fetch_result($query_user_orders, $i, 7);
		//find station in all_stations using station_id 
		$start_station_res = pg_query($conn, "select * from all_stations where sid = " . $start_station_id);
		$end_station_res = pg_query($conn, "select * from all_stations where sid = " . $end_station_id);
		$start_station = pg_fetch_result($start_station_res, 0, 1);
		$end_station = pg_fetch_result($end_station_res, 0, 1);
		pg_free_result($start_station_res);
		pg_free_result($end_station_res);

		$seat_type = pg_fetch_result($query_user_orders, $i, 8);
		$price = pg_fetch_result($query_user_orders, $i, 9);
		$order_time = pg_fetch_result($query_user_orders, $i, 10);
		$status = pg_fetch_result($query_user_orders, $i, 11);
		echo "<tr>";
		echo "<td>".$order_id."</td><td>".$train_num."</td><td>".$train_date."</td><td>".$start_station."</td><td>".$end_station."</td><td>".$seat_type."</td><td>".$price."</td><td>".$order_time."</td><td>".$status."</td>";
		if($status==1){
			echo "<td>"."<a href=\"http://localhost:8080/CNTRAK_230513/process/cancel_order.php?user=".$user."&order_id=".$order_id."\">Cancel order</a>"."</td>";
		}else{
			echo "<td>Canceled</td>";
		}
		echo "</tr>";
	}
	echo "</table>";
}
?>
</body>
</html>
