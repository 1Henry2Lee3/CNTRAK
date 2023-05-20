<?php
//order_process.php
//what this php do:
//1. select ticket with certain seat_type not 0, date, and the [] in ticketxxxx
//2. process the ticket a-b-c-d
//3. insert into orders
//4. update order_trains
echo "Connecting to the database...<br>\n";
$conn = pg_connect("host=localhost dbname=postgres user=postgres password=123")or die("Could not connect! " . pg_last_error());
echo "Connection succeed!<br>\n";

//get values from order_ticket.php
$user=$_GET["user"];
$train_num = $_GET["train_num"];
$date = $_GET["date"];
$start_train_station_id = $_GET["start_station_id"];
$end_train_station_id = $_GET["end_station_id"];
$seat_type = $_GET["seat_type"];
$price = $_GET["price"];
echo "user is " . $user . "<br>\n";
echo "train num is " . $train_num . "<br>\n";
echo "date is " . $date . "<br>\n";
echo "start_train_station_id is " . $start_train_station_id . "<br>\n";
echo "end_train_station_id is " . $end_train_station_id . "<br>\n";
echo "seat type is " . $seat_type . "<br>\n";
echo "price is " . $price . "<br>\n";
echo "<br>\n";

//select from ticketxxxx
echo "select from ticketxxxx...<br>\n";
$ticket_res = pg_query($conn, "select * from ticket".$train_num." where ticket_date='".$date."' and ticket_start_station_id<=".$start_train_station_id." and ticket_end_station_id>=".$end_train_station_id." and ".$seat_type."!=0")or die("Could not search such ticket! " . pg_last_error());
//ticket_res: ticket_date, t_start_s_id, t_end_s_id, seaty, seayr,...
$ticket_res_num = pg_num_rows($ticket_res);
echo "There are ".$ticket_res_num." infos.<br>\n";
if($ticket_res_num!=0){
	//found ticket! now processing...
        echo "ticket found, processing...<br>\n";
        //how to process:
        //1. update---minus 1
        echo "update ticketxxxx set seat_type num -1.<br>\n";
        $ticket_start_station_id = pg_fetch_result($ticket_res, 0, 1);
        $ticket_end_station_id = pg_fetch_result($ticket_res, 0, 2);
        $seat_type_num = pg_fetch_result($ticket_res, 0, "\"".$seat_type."\"");
        echo "seat_type is: ".$seat_type.", seat_type_num is: ".$seat_type_num."<br>\n";
        $new_seat_type_num = $seat_type_num - 1;
        //REMEMBER: same date, same start\end id will be put into only one data
        //so use t_start_s_id and t_end_s_id, train_num, date, seat_type to update
        echo "update ticket...<br>\n";
        
        $update_ticket_res = pg_query($conn, "update ticket".$train_num." set ".$seat_type."=".$new_seat_type_num." where ticket_date='".$date."' and ticket_start_station_id=".$ticket_start_station_id." and ticket_end_station_id=".$ticket_end_station_id) or die("Could not update ticketxxxx! " . pg_last_error());
        pg_free_result($update_ticket_res);
        
        //2. generate 0-1-2 ticket(s)
        echo "generating remaing ticket(s)...<br>\n";
        switch($seat_type){
        case"seaty":$output="1, 0, 0, 0, 0, 0, 0";break;
        case"seatr":$output="0, 1, 0, 0, 0, 0, 0";break;
        case"sleeperys":$output="0, 0, 1, 0, 0, 0, 0";break;
        case"sleeperyz":$output="0, 0, 0, 1, 0, 0, 0";break;
        case"sleeperyx":$output="0, 0, 0, 0, 1, 0, 0";break;
        case"sleeperrs":$output="0, 0, 0, 0, 0, 1, 0";break;
        case"sleeperrx":$output="0, 0, 0, 0, 0, 0, 1";break;
	}
	if($ticket_start_station_id==$start_train_station_id){
		if($ticket_end_station_id==$end_train_station_id){
                        //all same! no output
                        echo "all same! no output.<br>\n";
                }else{
                        //$ticket_end_station_id > $end_train_station_id
                        //output ticket []
                        echo "end_train_station_id < ticket_end_station_id<br>\n";
                        
                        $insert_trainend_ticketend_res = pg_query($conn, "insert into ticket".$train_num." values ( '".$date."', ".$end_train_station_id.", ".$ticket_end_station_id.", ".$output.")")or die("Could not insert new ticket! " . pg_last_error());
                        pg_free_result($insert_trainend_ticketend_res);
                }
	}else{
		if($ticket_end_station_id==$end_train_station_id){
                        //$ticket_start_station_id < $start_train_station_id
                        //output ticket []
                        echo "ticket_start_station_id < start_train_station_id<br>\n";
                        
                        $insert_ticketstart_trainstart_res = pg_query($conn, "insert into ticket".$train_num." value ( '".$date."', ".$ticket_start_station_id.", ".$start_train_station_id.", ".$output.")")or die("Could not insert new ticket! " . pg_last_error());
                        pg_free_result($insert_ticketstart_trainstart_res);
                }else{
                        //output tickets [] and []
                        echo "end_train_station_id < ticket_end_station_id<br>\n";
                        echo "ticket_start_station_id < start_train_station_id<br>\n";
                        
                        $insert_trainend_ticketend_res = pg_query($conn, "insert into ticket".$train_num." values ( '".$date."', ".$end_train_station_id.", ".$ticket_end_station_id.", ".$output.")")or die("Could not insert new ticket! " . pg_last_error());
                        pg_free_result($insert_trainend_ticketend_res);
                        $insert_ticketstart_trainstart_res = pg_query($conn, "insert into ticket".$train_num." value ( '".$date."', ".$ticket_start_station_id.", ".$start_train_station_id.", ".$output.")")or die("Could not insert new ticket! " . pg_last_error());
                        pg_free_result($insert_ticketstart_trainstart_res);
                }
	}
	echo "generating remaing ticket(s) complete!<br>\n";
        //3. insert into table orders
        echo "Inserting in table orders...<br>\n";
        $orders_num_res = pg_query($conn, "select * from orders")or die("Could not query orders! " . pg_last_error());
        $orders_num = pg_num_rows($orders_num_res);
        $oid = $orders_num;
        echo "oid is " . $oid . "<br>\n";

        $start_station_id_res = pg_query($conn, "select station_id from trains where train_num='".$train_num."' and train_station_id=".$start_train_station_id)or die("Could not select! " . pg_last_error());
        $start_station_id = pg_fetch_result($start_station_id_res, 0, 0);
        echo "start_station_id is " . $start_station_id . "<br>\n";
        pg_free_result($start_station_id_res);

        $end_station_id_res = pg_query($conn, "select station_id from trains where train_num='".$train_num."' and train_station_id=".$end_train_station_id)or die("Could not select! " . pg_last_error());
        $end_station_id = pg_fetch_result($end_station_id_res, 0, 0);
        echo "end_station_id is " . $end_station_id . "<br>\n";
        pg_free_result($end_station_id_res);

        $current_date = date("Y-m-d");
        $current_time = date("h:i:s");
        $order_time = $current_date." ".$current_time;
        echo "order_time is " . $order_time . "<br>\n";

        echo "inserting into orders...<br>\n";
        
        $insert_orders_res = pg_query($conn, "insert into orders values ( ".$oid.", '".$user."', '".$train_num."', '".$date."', ".$start_station_id.", ".$start_train_station_id.", ".$end_station_id.", ".$end_train_station_id.", '".$seat_type."', ".$price.", '".$order_time."', 1)")or die("Could not insert into table orders! " . pg_last_error());
        pg_free_result($insert_orders_res);

	echo "Insert complete!<br>\n";

	echo "update table order_trains.<br>\n";
	//using train_num to query order_trains: train_num, order_num
	$query_order_num_res = pg_query($conn, "select * from order_trains where train_num = '".$train_num."'") or die("Could not query! " . pg_last_error());
	$query_order_num_res_num = pg_num_rows($query_order_num_res);
	if($query_order_num_res_num==0){
		//no such train! insert new
		echo "No such train! Insert new data...<br>\n";
		$insert_train_res = pg_query($conn, "insert into order_trains values ( '".$train_num."', 1)")or die("Could not insert! " . pg_last_error());
		pg_free_result($insert_train_res);
		echo "train data inserted!<br>\n";
	}else{
		//have found such train! update
		echo "Have found such data! Updating...<br>\n";
		$train_order_num = pg_fetch_result($query_order_num_res, 0, 1);
		echo "The train has been ordered " . $train_order_num . " times before.<br>\n";
		$new_train_order_num = $train_order_num + 1;
		$update_train_res = pg_query($conn, "update order_trains set order_num = ".$new_train_order_num." where train_num = '".$train_num."'")or die("Could not update! " . pg_last_error());
		pg_free_result($update_train_res);
		echo "train data updated!<br>\n";
	}
	echo "Update complete!<br>\n";
}else{
	echo "No such ticket found!<br>\n";
}
?>
