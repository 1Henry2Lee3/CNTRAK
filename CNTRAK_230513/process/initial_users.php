<?php
echo "Initial table users...<br>\n";
$conn = pg_connect("host=localhost dbname=postgres user=postgres password=123")or die("Could not connect! " . pg_last_error());
$initial_users_res = pg_query($conn, "create table users ( uid integer, xingming varchar(20), phone char(11), username varchar(20), userpassword varchar(20));") or die("Could not create users. " . pg_last_error());
echo "Create users complete! Now adding 3 users...<br>\n";
pg_free_result($initial_users_res);
//adding 3 users
$insert_user1_res = pg_query($conn, "insert into users values ( 0, 'admin', '10101010101', 'admin', 'admin')")or die("Could not add user! " . pg_last_error());
pg_free_result($insert_user1_res);
$insert_user2_res = pg_query($conn, "insert into users values ( 1, 'user1', '12121212121', 'user1', 'password1')")or die("Could not add user! " . pg_last_error());
pg_free_result($insert_user2_res);
$insert_user3_res = pg_query($conn, "insert into users values ( 2, 'user2', '13131313131', 'user2', 'password2')")or die("Could not add user! " . pg_last_error());
pg_free_result($insert_user3_res);
echo "Insert users complete!<br>\n";
pg_close($conn);
?>
