<?php
$conn = pg_connect("host=localhost dbname=postgres user=postgres password=123")or die("Could not connect! " . pg_last_error());
echo "Connect complete!<br>\n";

$create_order_trains_res = pg_query($conn, "create table order_trains ( train_num varchar(10), order_num integer)")or die("Could not create! " . pg_last_error());
echo "Create order_trains complete!<br>\n";
pg_free_result($create_order_trains_res);
pg_close($conn);
?>
