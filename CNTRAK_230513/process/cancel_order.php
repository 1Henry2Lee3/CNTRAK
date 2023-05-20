<?php
//cancel_order.php

$user = $_GET["user"];
$oid = $_GET["order_id"];
echo "user is " . $user . "<br>\n";
echo "oid is " . $oid . "<br>\n";

echo "Connecting...<br>\n";
$conn = pg_connect("host=localhost dbname=postgres user=postgres password=123")or die("Could not connect! " . pg_last_error());
echo "Connection successful!<br>\n";

//update in orders---set status 0
echo "Update order status...<br>\n";

$cancel_orders_res = pg_query($conn, "update orders set status = 0 where oid =".$oid)or die("Could not cancel order! " . pg_last_error());
pg_free_result($cancel_orders_res);

//update in order_trains
echo "Update order_trains...<br>\n";
//get the train_num from orders
$query_train_num_res = pg_query($conn, "select * from orders where oid = ".$oid) or die("Could not query! " . pg_last_error());
$train_num = pg_fetch_result($query_train_num_res, 0, 2);
$order_date = pg_fetch_result($query_train_num_res, 0, 3);
$seat_type = pg_fetch_result($query_train_num_res, 0, 8);
$start_train_station_id = pg_fetch_result($query_train_num_res, 0, 5);
$end_train_station_id = pg_fetch_result($query_train_num_res, 0, 7);
$query_order_trains_res = pg_query($conn, "select * from order_trains where train_num = '".$train_num."'")or die("Could not query! " . pg_last_error());
$order_num = pg_fetch_result($query_order_trains_res, 0, 1);
$new_order_num = $order_num - 1;
$update_order_trains_res = pg_query($conn, "update order_trains set order_num = ".$new_order_num." where train_num = '".$train_num . "'")or die("Could not update! " . pg_last_error());
//todo: update in ticketxxxx---connect the ticketxxxx, insert a new one
switch($seat_type){
case "seaty":$seat_output="1, 0, 0, 0, 0, 0, 0";break;
case "seatr":$seat_output="0, 1, 0, 0, 0, 0, 0";break;
case "sleeperys":$seat_output="0, 0, 1, 0, 0, 0, 0";break;
case "sleeperyz":$seat_output="0, 0, 0, 1, 0, 0, 0";break;
case "sleeperyx":$seat_output="0, 0, 0, 0, 1, 0, 0";break;
case "sleeperrs":$seat_output="0, 0, 0, 0, 0, 1, 0";break;
case "sleeperrx":$seat_output="0, 0, 0, 0, 0, 0, 1";break;
}
//todo:
$update_new_res = pg_query($conn, "insert into ticket".$train_num." values ( '".$order_date."', ".$start_train_station_id.", ".$end_train_station_id.", ".$seat_output.")")or die("Could not insert new! " . pg_last_error());
echo "update in ticketxxxx success!<br>\n";
pg_free_result($update_new_res);

echo "The order has been canceled!<br>\n";
echo "<br>\n";
echo "<a href=\"http://localhost:8080/CNTRAK_230513/user_order_manage.php?user=".$user."\">return to order manage page</a>";
?>
