<html>
<head>
<meta charset="utf-8">
<title>Admin Home Page</title>
</head>
<body>
hello world.<br>
<h3>Welcome admin</h3>
Select what you want to do below.<br>
<a href="http://localhost:8080/CNTRAK_230513/login.php">return to login</a><br>
<?php
//use orders table to return the total_ticket_num and total_price (not cancelled)
echo "Connect to the database.<br>\n";
$conn = pg_connect("host=localhost dbname=postgres user=postgres password=123")or die("Could not connect! " . pg_last_error());
echo "Connect successful!<br>\n";
echo "<br><br>";
//todo: table orders: oid, userid, username, train_id, train_num, start_station_id, end_station_id, seat_type, price, status
$query_orders_res = pg_query($conn, "select * from orders where status = 1")or die("Could not query orders! " . pg_last_error());
$query_orders_res_num = pg_num_rows($query_orders_res);
echo "There are " . $query_orders_res_num . " orders.<br>\n";
$sum_price = 0.0;
for($i=0;$i<$query_orders_res_num;$i++){
	$sum_price += pg_fetch_result($query_orders_res, $i, 9);
}
echo "Total price is " . $sum_price . ".<br>\n";
echo "<br><br>";
//todo: output the hot 10 trains;
//todo: in order_trains table, stores the train_num, and order_num;
echo "The hot 10 trains...<br>\n";
$query_hot_10_trains_res = pg_query($conn, "select * from order_trains order by order_num desc limit 10")or die("Could not query hot trains! " . pg_last_error());
$query_hot_10_trains_res_num = pg_num_rows($query_hot_10_trains_res);
//output the hot 10 trains
echo "<table border=\"1\">";
echo "<tr><td>train_num</td><td>order_num</td></tr>";
for($i=0;$i<$query_hot_10_trains_res_num;$i++){
	$train_num = pg_fetch_result($query_hot_10_trains_res, $i, 0);
	$order_num = pg_fetch_result($query_hot_10_trains_res, $i, 1);
	echo "<tr>";
	echo "<td>".$train_num."</td><td>".$order_num."</td>";
	echo "</tr>";
}
echo "</table>";
//show all the users
//todo: click the user the visit user_info.php?user=user
echo "<br><br>";
$user_res = pg_query($conn, "select uid, xingming, phone, username from users");
$user_res_num = pg_num_rows($user_res);
//output them as a table

echo "All the registered users are as follows:<br>\n";
echo "<table border=\"1\">";
echo "<tr>";
echo "<td>user_id</td><td>xingming</td><td>phone</td><td>name</td>";
echo "</tr>\n";
for($i=0;$i<$user_res_num;$i++){
	echo "<tr>";
	$user_id = pg_fetch_result($user_res, $i, 0);
	$user_xingming = pg_fetch_result($user_res, $i, 1);
	$user_phone = pg_fetch_result($user_res, $i, 2);
	$user_name = pg_fetch_result($user_res, $i, 3);
	$real_user_name = "<a href=\"http://localhost:8080/CNTRAK_230513/history_orders.php?user=".$user_name."\">".$user_name."</a>";
	if($user_name=="admin"){
		$real_user_name = $user_name;
	}
	echo "<td>" . $user_id . "</td><td>" . $user_xingming . "</td><td>" . $user_phone . "</td><td>" . $real_user_name . "</td>";
	echo "</tr>\n";
}
echo "</table>";

?>
</body>
</html>
