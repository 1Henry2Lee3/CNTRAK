<?php
$conn = pg_connect("host=localhost dbname=postgres user=postgres password=123")or die("Could not connect! " . pg_last_error());
$create_orders_res = pg_query($conn, "create table orders ( oid integer, username varchar(20), train_num varchar(10), order_date date, start_station_id integer, start_train_station_id integer, end_station_id integer, end_train_station_id integer, seat_type varchar(10), price decimal, order_time timestamp, status integer)")or die("Could not create orders! " . pg_last_error());
echo "Complete!<br>\n";
pg_free_result($create_orders_res);
pg_close($conn);
?>
