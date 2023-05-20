<?php
//mul_order_process.php
$user=$_GET["user"];
$train_num1=$_GET["train_num1"];
$train_num2=$_GET["train_num2"];
$date1=$_GET["date1"];
$date2=$_GET["date2"];
$start_train_station1_id=$_GET["start_station1_id"];
$start_train_station2_id=$_GET["start_station2_id"];
$end_train_station1_id=$_GET["end_station1_id"];
$end_train_station2_id=$_GET["end_station2_id"];
$seat_type1=$_GET["seat_type1"];
$seat_type2=$_GET["seat_type2"];
$price1=$_GET["price1"];
$price2=$_GET["price2"];
echo "user is " . $user . "<br>\n";
echo "train_num1 is " . $train_num1 . "<br>\n";
echo "date1 is " . $date1 . "<br>\n";
echo "start_train_station1_id is " . $start_train_station1_id . "<br>\n";
echo "end_train_station1_id is " . $end_train_station1_id . "<br>\n";
echo "seat_type1 is " . $seat_type1 . "<br>\n";
echo "price1 is " . $price1 . "<br>\n";
echo "<br>\n";
echo "train_num2 is " . $train_num2 . "<br>\n";
echo "date2 is " . $date2 . "<br>\n";
echo "start_train_station2_id is " . $start_train_station2_id . "<br>\n";
echo "end_train_station2_id is " . $end_train_station2_id . "<br>\n";
echo "seat_type2 is " . $seat_type2 . "<br>\n";
echo "price2 is " . $price2 . "<br>\n";

echo "Connecting to the database...<br>\n";
$conn = pg_connect("host=localhost dbname=postgres user=postgres password=123")or die("Could not connect! " . pg_last_error());
echo "Connection succeed!<br>\n";

//train_num1
echo "select from ticketxxxx...<br>\n";
$ticket_res1 = pg_query($conn, "select * from ticket".$train_num1." where ticket_date='".$date1."' and ticket_start_station_id<=".$start_train_station1_id." and ticket_end_station_id>=".$end_train_station1_id." and ".$seat_type1."!=0")or die("Could not search such ticket! " . pg_last_error());
//ticket_res: ticket_date, t_start_s_id, t_end_s_id, seaty, seayr,...
$ticket_res_num1 = pg_num_rows($ticket_res1);
echo "There are ".$ticket_res_num1." infos.<br>\n";
if($ticket_res_num1!=0){
	//
	//found ticket! now processing...
        echo "ticket found, processing...<br>\n";
        //how to process:
        //1. update---minus 1
        echo "update ticketxxxx set seat_type1 num -1.<br>\n";
        $ticket_start_station1_id = pg_fetch_result($ticket_res1, 0, 1);
        $ticket_end_station1_id = pg_fetch_result($ticket_res1, 0, 2);
        $seat_type1_num = pg_fetch_result($ticket_res1, 0, "\"".$seat_type1."\"");
        echo "seat_type1 is: ".$seat_type1.", seat_type1_num is: ".$seat_type1_num."<br>\n";
        $new_seat_type1_num = $seat_type1_num - 1;
        //REMEMBER: same date, same start\end id will be put into only one data
        //so use t_start_s_id and t_end_s_id, train_num, date, seat_type to update
        echo "update ticket...<br>\n";
        
        $update_ticket_res1 = pg_query($conn, "update ticket".$train_num1." set ".$seat_type1."=".$new_seat_type1_num." where ticket_date='".$date1."' and ticket_start_station_id=".$ticket_start_station1_id." and ticket_end_station_id=".$ticket_end_station1_id) or die("Could not update ticketxxxx! " . pg_last_error());
        pg_free_result($update_ticket_res1);
        
        //2. generate 0-1-2 ticket(s)
        echo "generating remaing ticket(s)...<br>\n";
        switch($seat_type1){
        case"seaty":$output1="1, 0, 0, 0, 0, 0, 0";break;
        case"seatr":$output1="0, 1, 0, 0, 0, 0, 0";break;
        case"sleeperys":$output1="0, 0, 1, 0, 0, 0, 0";break;
        case"sleeperyz":$output1="0, 0, 0, 1, 0, 0, 0";break;
        case"sleeperyx":$output1="0, 0, 0, 0, 1, 0, 0";break;
        case"sleeperrs":$output1="0, 0, 0, 0, 0, 1, 0";break;
	case"sleeperrx":$output1="0, 0, 0, 0, 0, 0, 1";break;
	}
	if($ticket_start_station1_id==$start_train_station1_id){
		if($ticket_end_station1_id==$end_train_station1_id){
                        //all same! no output
                        echo "all same! no output.<br>\n";
                }else{
                        //$ticket_end_station_id > $end_train_station_id
                        //output ticket []
                        echo "end_train_station1_id < ticket_end_station1_id<br>\n";
                        
                        $insert_trainend_ticketend_res1 = pg_query($conn, "insert into ticket".$train_num1." values ( '".$date1."', ".$end_train_station1_id.", ".$ticket_end_station1_id.", ".$output1.")")or die("Could not insert new ticket! " . pg_last_error());
                        pg_free_result($insert_trainend_ticketend_res1);
                        
                }
	}else{
		if($ticket_end_station1_id==$end_train_station1_id){
                        //$ticket_start_station1_id < $start_train_station1_id
                        //output ticket []
                        echo "ticket_start_station1_id < start_train_station1_id<br>\n";
                        
                        $insert_ticketstart_trainstart_res1 = pg_query($conn, "insert into ticket".$train_num1." value ( '".$date1."', ".$ticket_start_station1_id.", ".$start_train_station1_id.", ".$output1.")")or die("Could not insert new ticket! " . pg_last_error());
                        pg_free_result($insert_ticketstart_trainstart_res1);
                        
                }else{
                        //output tickets [] and []
                        echo "end_train_station1_id < ticket_end_station1_id<br>\n";
                        echo "ticket_start_station1_id < start_train_station1_id<br>\n";
                        
                        $insert_trainend_ticketend_res1 = pg_query($conn, "insert into ticket".$train_num1." values ( '".$date1."', ".$end_train_station1_id.", ".$ticket_end_station1_id.", ".$output1.")")or die("Could not insert new ticket! " . pg_last_error());
                        pg_free_result($insert_trainend_ticketend_res1);
                        $insert_ticketstart_trainstart_res1 = pg_query($conn, "insert into ticket".$train_num1." value ( '".$date1."', ".$ticket_start_station1_id.", ".$start_train_station1_id.", ".$output1.")")or die("Could not insert new ticket! " . pg_last_error());
                        pg_free_result($insert_ticketstart_trainstart_res1);
                        
                }
	}
	echo "generating remaing ticket(s) complete!<br>\n";
        //3. insert into table orders
        echo "Inserting in table orders...<br>\n";
        $orders_num_res1 = pg_query($conn, "select * from orders")or die("Could not query orders! " . pg_last_error());
        $orders_num1 = pg_num_rows($orders_num_res1);
        $oid1 = $orders_num1;
        echo "oid1 is " . $oid1 . "<br>\n";

        $start_station_id_res1 = pg_query($conn, "select station_id from trains where train_num='".$train_num1."' and train_station_id=".$start_train_station1_id)or die("Could not select! " . pg_last_error());
        $start_station1_id = pg_fetch_result($start_station_id_res1, 0, 0);
        echo "start_station1_id is " . $start_station1_id . "<br>\n";
        pg_free_result($start_station_id_res1);

        $end_station_id_res1 = pg_query($conn, "select station_id from trains where train_num='".$train_num1."' and train_station_id=".$end_train_station1_id)or die("Could not select! " . pg_last_error());
        $end_station1_id = pg_fetch_result($end_station_id_res1, 0, 0);
        echo "end_station1_id is " . $end_station1_id . "<br>\n";
        pg_free_result($end_station_id_res1);

        $current_date1 = date("Y-m-d");
        $current_time1 = date("h:i:s");
        $order_time1 = $current_date1." ".$current_time1;
        echo "order_time1 is " . $order_time1 . "<br>\n";

        echo "inserting into orders...<br>\n";
        
        $insert_orders_res1 = pg_query($conn, "insert into orders values ( ".$oid1.", '".$user."', '".$train_num1."', '".$date1."', ".$start_station1_id.", ".$start_train_station1_id.", ".$end_station1_id.", ".$end_train_station1_id.", '".$seat_type1."', ".$price1.", '".$order_time1."', 1)")or die("Could not insert into table orders! " . pg_last_error());
        pg_free_result($insert_orders_res1);
        
	echo "Insert complete!<br>\n";

	echo "update table order_trains.<br>\n";
        //using train_num to query order_trains: train_num, order_num
        $query_order_num_res1 = pg_query($conn, "select * from order_trains where train_num = '".$train_num1."'") or die("Could not query! " . pg_last_error());
        $query_order_num_res_num1 = pg_num_rows($query_order_num_res1);
        if($query_order_num_res_num1==0){
                //no such train! insert new
                echo "No such train! Insert new data...<br>\n";
                $insert_train_res1 = pg_query($conn, "insert into order_trains values ( '".$train_num1."', 1)")or die("Could not insert! " . pg_last_error());
                pg_free_result($insert_train_res1);
                echo "train data inserted!<br>\n";
        }else{
                //have found such train! update
                echo "Have found such data! Updating...<br>\n";
                $train_order_num1 = pg_fetch_result($query_order_num_res1, 0, 1);
                echo "The train has been ordered " . $train_order_num1 . " times before.<br>\n";
                $new_train_order_num1 = $train_order_num1 + 1;
                $update_train_res1 = pg_query($conn, "update order_trains set order_num = ".$new_train_order_num1." where train_num = '".$train_num1."'")or die("Could not update! " . pg_last_error());
                pg_free_result($update_train_res1);
                echo "train data updated!<br>\n";
        }
        echo "Update complete!<br>\n";
}else{
	echo "No such ticket found!<br>\n";
}


//train_num2
//select from ticketxxxx
echo "select from ticketxxxx...<br>\n";
$ticket_res2 = pg_query($conn, "select * from ticket".$train_num2." where ticket_date='".$date2."' and ticket_start_station_id<=".$start_train_station2_id." and ticket_end_station_id>=".$end_train_station2_id." and ".$seat_type2."!=0")or die("Could not search such ticket! " . pg_last_error());
//ticket_res: ticket_date, t_start_s_id, t_end_s_id, seaty, seayr,...
$ticket_res_num2 = pg_num_rows($ticket_res2);
echo "There are ".$ticket_res_num2." infos.<br>\n";
if($ticket_res_num2!=0){
	//found ticket! now processing...
        echo "ticket found, processing...<br>\n";
        //how to process:
        //1. update---minus 1
        echo "update ticketxxxx set seat_type2 num -1.<br>\n";
        $ticket_start_station2_id = pg_fetch_result($ticket_res2, 0, 1);
        $ticket_end_station2_id = pg_fetch_result($ticket_res2, 0, 2);
        $seat_type2_num = pg_fetch_result($ticket_res2, 0, "\"".$seat_type2."\"");
        echo "seat_type2 is: ".$seat_type2.", seat_type2_num is: ".$seat_type2_num."<br>\n";
        $new_seat_type2_num = $seat_type2_num - 1;
        //REMEMBER: same date, same start\end id will be put into only one data
        //so use t_start_s_id and t_end_s_id, train_num, date, seat_type to update
        echo "update ticket...<br>\n";
        
        $update_ticket_res2 = pg_query($conn, "update ticket".$train_num2." set ".$seat_type2."=".$new_seat_type2_num." where ticket_date='".$date2."' and ticket_start_station_id=".$ticket_start_station2_id." and ticket_end_station_id=".$ticket_end_station2_id) or die("Could not update ticketxxxx! " . pg_last_error());
        pg_free_result($update_ticket_res2);
        
        //2. generate 0-1-2 ticket(s)
        echo "generating remaing ticket(s)...<br>\n";
        switch($seat_type2){
        case"seaty":$output2="1, 0, 0, 0, 0, 0, 0";break;
        case"seatr":$output2="0, 1, 0, 0, 0, 0, 0";break;
        case"sleeperys":$output2="0, 0, 1, 0, 0, 0, 0";break;
        case"sleeperyz":$output2="0, 0, 0, 1, 0, 0, 0";break;
        case"sleeperyx":$output2="0, 0, 0, 0, 1, 0, 0";break;
        case"sleeperrs":$output2="0, 0, 0, 0, 0, 1, 0";break;
	case"sleeperrx":$output2="0, 0, 0, 0, 0, 0, 1";break;
	}
	if($ticket_start_station2_id==$start_train_station2_id){
		if($ticket_end_station2_id==$end_train_station2_id){
                        //all same! no output
                        echo "all same! no output.<br>\n";
                }else{
                        //$ticket_end_station2_id > $end_train_station2_id
                        //output ticket []
                        echo "end_train_station2_id < ticket_end_station2_id<br>\n";
                        
                        $insert_trainend_ticketend_res2 = pg_query($conn, "insert into ticket".$train_num2." values ( '".$date2."', ".$end_train_station2_id.", ".$ticket_end_station2_id.", ".$output2.")")or die("Could not insert new ticket! " . pg_last_error());
                        pg_free_result($insert_trainend_ticketend_res2);
                        
                }
	}else{
		if($ticket_end_station2_id==$end_train_station2_id){
                        //$ticket_start_station2_id < $start_train_station2_id
                        //output ticket []
                        echo "ticket_start_station2_id < start_train_station2_id<br>\n";
                        
                        $insert_ticketstart_trainstart_res2 = pg_query($conn, "insert into ticket".$train_num2." values ( '".$date2."', ".$ticket_start_station2_id.", ".$start_train_station2_id.", ".$output2.")")or die("Could not insert new ticket! " . pg_last_error());
                        pg_free_result($insert_ticketstart_trainstart_res2);
                        
                }else{
                        //output tickets [] and []
                        echo "end_train_station2_id < ticket_end_station2_id<br>\n";
                        echo "ticket_start_station2_id < start_train_station2_id<br>\n";
                        
                        $insert_trainend_ticketend_res2 = pg_query($conn, "insert into ticket".$train_num2." values ( '".$date2."', ".$end_train_station2_id.", ".$ticket_end_station2_id.", ".$output2.")")or die("Could not insert new ticket! " . pg_last_error());
                        pg_free_result($insert_trainend_ticketend_res2);
                        $insert_ticketstart_trainstart_res2 = pg_query($conn, "insert into ticket".$train_num2." values ( '".$date2."', ".$ticket_start_station2_id.", ".$start_train_station2_id.", ".$output2.")")or die("Could not insert new ticket! " . pg_last_error());
                        pg_free_result($insert_ticketstart_trainstart_res2);
                        
                }
	}
	echo "generating remaing ticket(s) complete!<br>\n";
        //3. insert into table orders
        echo "Inserting in table orders...<br>\n";
        $orders_num_res2 = pg_query($conn, "select * from orders")or die("Could not query orders! " . pg_last_error());
        $orders_num2 = pg_num_rows($orders_num_res2);
        $oid2 = $orders_num2;
        echo "oid2 is " . $oid2 . "<br>\n";

        $start_station_id_res2 = pg_query($conn, "select station_id from trains where train_num='".$train_num2."' and train_station_id=".$start_train_station2_id)or die("Could not select! " . pg_last_error());
        $start_station2_id = pg_fetch_result($start_station_id_res2, 0, 0);
        echo "start_station2_id is " . $start_station2_id . "<br>\n";
        pg_free_result($start_station_id_res2);

        $end_station_id_res2 = pg_query($conn, "select station_id from trains where train_num='".$train_num2."' and train_station_id=".$end_train_station2_id)or die("Could not select! " . pg_last_error());
        $end_station2_id = pg_fetch_result($end_station_id_res2, 0, 0);
        echo "end_station2_id is " . $end_station2_id . "<br>\n";
        pg_free_result($end_station_id_res2);

        $current_date2 = date("Y-m-d");
        $current_time2 = date("h:i:s");
        $order_time2 = $current_date2." ".$current_time2;
        echo "order_time2 is " . $order_time2 . "<br>\n";

        echo "inserting into orders...<br>\n";
        
        $insert_orders_res2 = pg_query($conn, "insert into orders values ( ".$oid2.", '".$user."', '".$train_num2."', '".$date2."', ".$start_station2_id.", ".$start_train_station2_id.", ".$end_station2_id.", ".$end_train_station2_id.", '".$seat_type2."', ".$price2.", '".$order_time2."', 1)")or die("Could not insert into table orders! " . pg_last_error());
        pg_free_result($insert_orders_res2);
        
	echo "Insert complete!<br>\n";
	echo "update table order_trains.<br>\n";
        //using train_num to query order_trains: train_num, order_num
        $query_order_num_res2 = pg_query($conn, "select * from order_trains where train_num = '".$train_num2."'") or die("Could not query! " . pg_last_error());
        $query_order_num_res_num2 = pg_num_rows($query_order_num_res2);
        if($query_order_num_res_num2==0){
                //no such train! insert new
                echo "No such train! Insert new data...<br>\n";
                $insert_train_res2 = pg_query($conn, "insert into order_trains values ( '".$train_num2."', 1)")or die("Could not insert! " . pg_last_error());
                pg_free_result($insert_train_res2);
                echo "train data inserted!<br>\n";
        }else{
                //have found such train! update
                echo "Have found such data! Updating...<br>\n";
                $train_order_num2 = pg_fetch_result($query_order_num_res2, 0, 1);
                echo "The train has been ordered " . $train_order_num2 . " times before.<br>\n";
                $new_train_order_num2 = $train_order_num2 + 1;
                $update_train_res2 = pg_query($conn, "update order_trains set order_num = ".$new_train_order_num2." where train_num = '".$train_num2."'")or die("Could not update! " . pg_last_error());
                pg_free_result($update_train_res2);
                echo "train data updated!<br>\n";
        }
        echo "Update complete!<br>\n";
}else{
	echo "No such ticket found!<br>\n";
}
?>
