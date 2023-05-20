<?php
//todo: connect all_trains first,
//from first train to the last:
//connect trains where trainid==...
//create ticketxxxx: date ticket_start_station_id, ticket_end_station_id, seaty, seatr, sleeperys, sleeperyz, sleeperyx, sleeperrs, sleeperrx
//analyse the trains where trainid==...

//connect the database
$conn = pg_connect("host=localhost dbname=postgres user=postgres password=123") or die("Could not connect! " . pg_last_error());

//query all_trains;
$all_trains_res = pg_query($conn, "select * from all_trains") or die("Could not query all_trains! " . pg_last_error());
$all_trains_res_num = pg_num_rows($all_trains_res);
echo "There are " . $all_trains_res_num . " trains in China.<br>\n";

//add a column to trains: station_valid integer
$add_trains_station_valid_res = pg_query($conn, "alter table trains add column station_valid integer") or die("Could not add station_valid! " . pg_last_error());
echo "Add column station_valid success!<br>\n";
pg_free_result($add_trains_station_valid_res);
//now the structure of trains:
//train_id integer, train_num varchar(10), train_type char(1), train_station_id integer
//station varchar(20), arr_time interval, dep_time interval, stop_time interval
//running_time interval, mileage integer, seat, sleepery, sleeperr, station_id, station_city, station_valid

//add columns to all_trains: seaty_en, seatr_en, sleeperys_en, sleeperyz_en, sleeperyx_en, sleeperrs_en, sleeperrx_en
$add_columns_to_all_trains_res1 = pg_query($conn, "alter table all_trains add column seaty_en integer")or die("Couldn't add column seaty_en! " . pg_last_error());
pg_free_result($add_columns_to_all_trains_res1);
$add_columns_to_all_trains_res2 = pg_query($conn, "alter table all_trains add column seatr_en integer")or die("Couldn't add column seatr_en! " . pg_last_error());
pg_free_result($add_columns_to_all_trains_res2);
$add_columns_to_all_trains_res3 = pg_query($conn, "alter table all_trains add column sleeperys_en integer")or die("Couldn't add column sleeperys_en! " . pg_last_error());
pg_free_result($add_columns_to_all_trains_res3);
$add_columns_to_all_trains_res4 = pg_query($conn, "alter table all_trains add column sleeperyz_en integer")or die("Couldn't add column sleeperyz_en! " . pg_last_error());
pg_free_result($add_columns_to_all_trains_res4);
$add_columns_to_all_trains_res5 = pg_query($conn, "alter table all_trains add column sleeperyx_en integer")or die("Couldn't add column sleeperyx_en! " . pg_last_error());
pg_free_result($add_columns_to_all_trains_res5);
$add_columns_to_all_trains_res6 = pg_query($conn, "alter table all_trains add column sleeperrs_en integer")or die("Couldn't add column sleeperrs_en! " . pg_last_error());
pg_free_result($add_columns_to_all_trains_res6);
$add_columns_to_all_trains_res7 = pg_query($conn, "alter table all_trains add column sleeperrx_en integer")or die("Couldn't add column sleeperrx_en! " . pg_last_error());
pg_free_result($add_columns_to_all_trains_res7);
echo "add column seat_type_en to all_trains success!<br>\n";

//add columns to trains: seaty, seatr, sleeperys, sleeperyz, sleeperyx, sleeperrs, sleeperrx
/**/
$add_columns_to_trains_res1 = pg_query($conn, "alter table trains add column seaty_p decimal")or die("Couldn't add column seaty_p! " . pg_last_error());
pg_free_result($add_columns_to_trains_res1);
$add_columns_to_trains_res2 = pg_query($conn, "alter table trains add column seatr_p decimal")or die("Couldn't add column seatr_p! " . pg_last_error());
pg_free_result($add_columns_to_trains_res2);
$add_columns_to_trains_res3 = pg_query($conn, "alter table trains add column sleeperys_p decimal")or die("Couldn't add column sleeperys_p! " . pg_last_error());
pg_free_result($add_columns_to_trains_res3);
$add_columns_to_trains_res4 = pg_query($conn, "alter table trains add column sleeperyz_p decimal")or die("Couldn't add column sleeperyz_p! " . pg_last_error());
pg_free_result($add_columns_to_trains_res4);
$add_columns_to_trains_res5 = pg_query($conn, "alter table trains add column sleeperyx_p decimal")or die("Couldn't add column sleeperyx_p! " . pg_last_error());
pg_free_result($add_columns_to_trains_res5);
$add_columns_to_trains_res6 = pg_query($conn, "alter table trains add column sleeperrs_p decimal")or die("Couldn't add column sleeperrs_p! " . pg_last_error());
pg_free_result($add_columns_to_trains_res6);
$add_columns_to_trains_res7 = pg_query($conn, "alter table trains add column sleeperrx_p decimal")or die("Couldn't add column sleeperrx_p! " . pg_last_error());
pg_free_result($add_columns_to_trains_res7);
echo "Add column seat_p to trains complete!<br>\n";
/**/

echo "Creating ticketxxxx...<br>\n";
for($i=0;$i<$all_trains_res_num;$i++){
	$train_num = pg_fetch_result($all_trains_res, $i, 1);
	echo "The train_num is " . $train_num . ", ";
	//create table ticketxxxx
	$create_ticketxxxx_res = pg_query($conn, "create table ticket" . $train_num . " ( ticket_date date, ticket_start_station_id integer, ticket_end_station_id integer, seaty integer, seayr integer, sleeperys integer, sleeperyz integer, sleeperyx integer, sleeperrs integer, sleeperrx integer)") or die("Could not create ticket" . $train_num . ". " . pg_last_error());
	echo "Created ticket" . $train_num . ", ";
	pg_free_result($create_ticketxxxx_res);
}
echo "Create all ticketxxxx complete!<br>\n";


for($i=0;$i<$all_trains_res_num;$i++){
	$train_num = pg_fetch_result($all_trains_res, $i, 1);
	//gather result from trains where train_num = train_num
	$trains_res = pg_query($conn, "select * from trains where train_num = '" . $train_num . "' order by train_station_id asc") or die("Could not query trains. " . pg_last_error());
	$trains_res_num = pg_num_rows($trains_res);
	//$train_res_num is the num of station of one train
	echo "There are " . $trains_res_num . " info in this train.<br>\n";
	//set start_station_id and end_station_id for ticketxxxx
	$start_station_id = 1;
	$end_station_id = $trains_res_num;

	//set array from trains_res, used for _en and station_valid.
	//todo: and used for prices of trains
	echo "Set array from trains_res...<br>\n";
	//1.seaty, seatr
	$seatyarr = array();
	$seatrarr = array();
	for($j=0;$j<$trains_res_num;$j++){
		$seat = pg_fetch_result($trains_res, $j, 10);
		//echo "The seat is " . $seat . ", ";
		//devide the seat using explode()
		//if "-", don't use
		if($seat=="-"){
			$seatyarr[$j] = "-";
			$seatrarr[$j] = "-";
		}else{
			$seatyarr[$j] = explode('/', $seat, 2)[0];
			$seatrarr[$j] = explode('/', $seat, 2)[1];
		}
		//echo "seatyarr[" . $j . "] is " . $seatyarr[$j] . "<br>\n";
		//echo "seatrarr[" . $j . "] is " . $seatrarr[$j] . "<br>\n";
	}
	//2.sleeperys, sleeperyz, sleeperyx
	$sleeperysarr = array();
	$sleeperyzarr = array();
	$sleeperyxarr = array();
	for($j=0;$j<$trains_res_num;$j++){
        	$sleepery = pg_fetch_result($trains_res, $j, 11);
        	//echo "The sleepery is " . $sleepery . ", ";
        	//devide the seat using explode()
        	//if "-", don't use
        	if($sleepery=="-"){
            		$sleeperysarr[$j] = "-";
			$sleeperyzarr[$j] = "-";
			$sleeperyxarr[$j] = "-";
        	}else{
            		$sleeperysarr[$j] = explode('/', $sleepery, 3)[0];
			$sleeperyzarr[$j] = explode('/', $sleepery, 3)[1];
			$sleeperyxarr[$j] = explode('/', $sleepery, 3)[2];
        	}
        	//echo "sleeperysarr[" . $j . "] is " . $sleeperysarr[$j] . "<br>\n";
		//echo "sleeperyzarr[" . $j . "] is " . $sleeperyzarr[$j] . "<br>\n";
		//echo "sleeperyxarr[" . $j . "] is " . $sleeperyxarr[$j] . "<br>\n";
    	}
	//3.sleeperrs, sleeperrx
	$sleeperrsarr = array();
	$sleeperrxarr = array();
	for($j=0;$j<$trains_res_num;$j++){
        	$sleeperr = pg_fetch_result($trains_res, $j, 12);
        	//echo "The sleeperr is " . $sleeperr . "<br>\n";
        	//devide the seat using explode()
        	//if "-", don't use
        	if($sleeperr=="-"){
            		$sleeperrsarr[$j] = "-";
            		$sleeperrxarr[$j] = "-";
        	}else{
            		$sleeperrsarr[$j] = explode('/', $sleeperr, 2)[0];
            		$sleeperrxarr[$j] = explode('/', $sleeperr, 2)[1];
    		}
        	//echo "sleeperrsarr[" . $j . "] is " . $sleeperrsarr[$j] . "<br>\n";
        	//echo "sleeperrxarr[" . $j . "] is " . $sleeperrxarr[$j] . "<br>\n";
	}
	echo "generate arr complete!, ";

	//have made arrays(), now analyze arrays
	//0--$train_res_num-1
	//1. generate seaty_en, ...
	echo "Generate seaty_en for all_trains using arr...<br>\n";
	$seaty_en = 0;
	$seatr_en = 0;
	$sleeperys_en = 0;
	$sleeperyz_en = 0;
	$sleeperyx_en = 0;
	$sleeperrs_en = 0;
	$sleeperrx_en = 0;
	for($j=0;$j<$trains_res_num;$j++){
		//if found not null, set en=1
		if($seatyarr[$j]!="-" && $seatyarr[$j]!="0"){
			$seaty_en = 1;
		}
		if($seatrarr[$j]!="-" && $seatrarr[$j]!="0"){
			$seatr_en = 1;
		}
		if($sleeperysarr[$j]!="-" && $sleeperysarr[$j]!="0"){
			$sleeperys_en = 1;
		}
		if($sleeperyzarr[$j]!="-" && $sleeperyzarr[$j]!="0"){
			$sleeperyz_en = 1;
		}
		if($sleeperyxarr[$j]!="-" && $sleeperyxarr[$j]!="0"){
			$sleeperyx_en = 1;
		}
		if($sleeperrsarr[$j]!="-" && $sleeperrsarr[$j]!="0"){
			$sleeperrs_en = 1;
		}
		if($sleeperrxarr[$j]!="-" && $sleeperrxarr[$j]!="0"){
			$sleeperrx_en = 1;
		}
	}
	//echo "seaty_en: " . $seaty_en . ", seatr_en: " . $seatr_en . ", sleeperys_en: " . $sleeperys_en . ", sleeperyz_en: " . $sleeperyz_en . ", sleeperyx_en: " . $sleeperyx_en . ", sleeperrs_en: " . $sleeperrs_en . ", sleeperrx_en: " . $sleeperrx_en . "<br>\n";
	echo "generate _en complete! ";

	//2. generate station_valid_arr
	echo "Generating station_valid for trains using arr...<br>\n";
	$station_valid_arr = array();
	//from 0 to $train_res_num-1
	for($j=0;$j<$trains_res_num;$j++){
		//if found not null, set valid = 1
		//else
		//if first station, set valid = 1
		//else set valid = 0
		//OR BETTER:
		//if first_station, set valid = 1
		//else
		//if found not null, set valid = 1
		//else set valid = 0
		if(($seatyarr[$j]!="-" && $seatyarr[$j]!="0") || ($seatrarr[$j]!="-" && $seatrarr[$j]!="0") || ($sleeperysarr[$j]!="-" && $sleeperysarr[$j]!="0") || ($sleeperyzarr[$j]!="-" && $sleeperyzarr[$j]!="0") || ($sleeperyxarr[$j]!="-" && $sleeperyxarr[$j]!="0") || ($sleeperrsarr[$j]!="-" && $sleeperrsarr[$j]!="0") || ($sleeperrxarr[$j]!="-" && $sleeperrxarr[$j]!="0")){
			//if price of station not all null, set this station_valid = 1
			$station_valid_arr[$j] = 1;
		}else{
			if($j==0){
				//the first station
				$station_valid_arr[$j] = 1;
			}else{
				$station_valid_arr[$j] = 0;
			}
		}
	}
	echo "Generate valid complete! ";
	//now array(), en and valid are all gathered

	echo "Now insert ticket_num into ticketxxxx using _en.<br>\n";
	//todo: insert information to ticketxxxx: date, start_station_id, end_station_id, seaty, seayr, sleeperys, sleeperyz, sleeperyx, sleeperrs, sleeperrx use _en
	$seaty_num = ($seaty_en)?5:0;
	$seatr_num = ($seatr_en)?5:0;
	$sleeperys_num = ($sleeperys_en)?5:0;
	$sleeperyz_num = ($sleeperyz_en)?5:0;
	$sleeperyx_num = ($sleeperyx_en)?5:0;
	$sleeperrs_num = ($sleeperrs_en)?5:0;
	$sleeperrx_num = ($sleeperrx_en)?5:0;
	for($days=1;$days<=15;$days++){
		//echo "Inserting...";
		//from tomorrow to 15 days later
		$ticket_date = date('Y/m/d', strtotime("+".$days." days"));
		//echo var_dump($ticket_date) . "<br>\n";
		$insert_ticket_res = pg_query($conn, "insert into ticket" . $train_num . " values ('" . $ticket_date . "', " . $start_station_id . ", " . $end_station_id . ", " . $seaty_num . ", " . $seatr_num . ", " . $sleeperys_num . ", " . $sleeperyz_num . ", " . $sleeperyx_num . ", " . $sleeperrs_num . ", " . $sleeperrx_num . ")") or die("Could not insert ticket" . $train_num . " on date " . date('Y-m-d', strtotime("+".$days." days")) . "! " . pg_last_error());
		pg_free_result($insert_ticket_res);
	}
	echo "Insert date complete for train " . $train_num . "<br>\n";

	//todo: send price array to trains
	//seatyarr[0--$trains-res-num-1]: seaty_p in train_station_id
	//but seatyarr is a varchar, seaty_p is decimal---USING number_format($seatyarr[$i], 1, '.', '')
	
	echo "update price for trains " . $train_num . "using arr...<br>\n";
	//echo "update seaty_p to trains...<br>\n";
	//echo var_dump($seatyarr) . "<br>\n";
	for($j=0;$j<$trains_res_num;$j++){
		$seaty_price = $seatyarr[$j];
		//echo "seaty_price is " . $seaty_price . "<br>\n";
		if($seaty_price=="-" || $seaty_price=="0"){
			$real_seaty_price = 0.0;
		}else{
			//abc.d---use number_format
			$real_seaty_price = number_format($seaty_price, 1, '.', '');
		}
		//echo "Now real_seaty_price is " . $real_seaty_price . "<br>\n";
		//$update_seaty_p_res = pg_query($conn, "update trains set seaty_p = " . $real_seaty_price . " where train_num = '" . $train_num . "' and train_station_id = " . ($j+1))or die("Could not update seaty_p! " . pg_last_error());
		//pg_free_result($update_seaty_p_res);

		$seatr_price = $seatrarr[$j];
                if($seatr_price=="-" || $seatr_price=="0"){
                        $real_seatr_price = 0.0;
                }else{
                        $real_seatr_price = number_format($seatr_price, 1, '.', '');
                }
                //$update_seatr_p_res = pg_query($conn, "update trains set seatr_p = " . $real_seatr_price . " where train_num = '" . $train_num . "' and train_station_id = " . ($j+1))or die("Could not update seatr_p! " . pg_last_error());
		//pg_free_result($update_seatr_p_res);

		$sleeperys_price = $sleeperysarr[$j];
                if($sleeperys_price=="-" || $sleeperys_price=="0"){
                        $real_sleeperys_price = 0.0;
                }else{
                        $real_sleeperys_price = number_format($sleeperys_price, 1, '.', '');
                }
                //$update_sleeperys_p_res = pg_query($conn, "update trains set sleeperys_p = " . $real_sleeperys_price . " where train_num = '" . $train_num . "' and train_station_id = " . ($j+1))or die("Could not update sleeperys_p! " . pg_last_error());
		//pg_free_result($update_sleeperys_p_res);

		$sleeperyz_price = $sleeperyzarr[$j];
                if($sleeperyz_price=="-" || $sleeperyz_price=="0"){
                        $real_sleeperyz_price = 0.0;
                }else{
                        $real_sleeperyz_price = number_format($sleeperyz_price, 1, '.', '');
                }
                //$update_sleeperyz_p_res = pg_query($conn, "update trains set sleeperyz_p = " . $real_sleeperyz_price . " where train_num = '" . $train_num . "' and train_station_id = " . ($j+1))or die("Could not update sleeperyz_p! " . pg_last_error());
		//pg_free_result($update_sleeperyz_p_res);

		$sleeperyx_price = $sleeperyxarr[$j];
                if($sleeperyx_price=="-" || $sleeperyx_price=="0"){
                        $real_sleeperyx_price = 0.0;
                }else{
                        $real_sleeperyx_price = number_format($sleeperyx_price, 1, '.', '');
                }
                //$update_sleeperyx_p_res = pg_query($conn, "update trains set sleeperyx_p = " . $real_sleeperyx_price . " where train_num = '" . $train_num . "' and train_station_id = " . ($j+1))or die("Could not update sleeperyx_p! " . pg_last_error());
		//pg_free_result($update_sleeperyx_p_res);

		$sleeperrs_price = $sleeperrsarr[$j];
                if($sleeperrs_price=="-" || $sleeperrs_price=="0"){
                        $real_sleeperrs_price = 0.0;
                }else{
                        $real_sleeperrs_price = number_format($sleeperrs_price, 1, '.', '');
                }
                //$update_sleeperrs_p_res = pg_query($conn, "update trains set sleeperrs_p = " . $real_sleeperrs_price . " where train_num = '" . $train_num . "' and train_station_id = " . ($j+1))or die("Could not update sleeperrs_p! " . pg_last_error());
		//pg_free_result($update_sleeperrs_p_res);

		$sleeperrx_price = $sleeperrxarr[$j];
                if($sleeperrx_price=="-" || $sleeperrx_price=="0"){
                        $real_sleeperrx_price = 0.0;
                }else{
                        $real_sleeperrx_price = number_format($sleeperrx_price, 1, '.', '');
                }
		//$update_sleeperrx_p_res = pg_query($conn, "update trains set sleeperrx_p = " . $real_sleeperrx_price . " where train_num = '" . $train_num . "' and train_station_id = " . ($j+1))or die("Could not update sleeperrx_p! " . pg_last_error());
		$update_p_res = pg_query($conn, "update trains set seaty_p = " . $real_seaty_price . ", seatr_p = " . $real_seatr_price . ", sleeperys_p = " . $real_sleeperys_price . ", sleeperyz_p = " . $real_sleeperyz_price . ", sleeperyx_p = " . $real_sleeperyx_price . ", sleeperrs_p = " . $real_sleeperrs_price . ", sleeperrx_p = " . $real_sleeperrx_price . " where train_num = '" . $train_num . "' and train_station_id = " . ($j+1))or die("Could not update price! " . pg_last_error());
		//pg_free_result($update_sleeperrx_p_res);
		pg_free_result($update_p_res);
	}
	echo "Update prices for train " . $train_num . "complete!<br>\n";

	//send valid to trains using train_num and train_station_id
	echo "update valid to trains...<br>\n";
	for($j=0;$j<$trains_res_num;$j++){
		$update_station_valid_res = pg_query($conn, "update trains set station_valid = " . $station_valid_arr[$j] . " where train_num = '" . $train_num . "' and train_station_id = " . ($j+1)) or die("Could not update trains! " . pg_last_error());
		pg_free_result($update_station_valid_res);
	}

	//send en to all_trains;
	echo "update _en to all_trains...<br>\n";
	$send_en_all_trains_res = pg_query($conn, "update all_trains set seaty_en = " . $seaty_en . ", seatr_en = " . $seatr_en . ", sleeperys_en = " . $sleeperys_en . ", sleeperyz_en = " . $sleeperyz_en . ", sleeperyx_en = " . $sleeperyx_en . ", sleeperrs_en = " . $sleeperrs_en . ", sleeperrx_en = " . $sleeperrx_en . " where train_num = '" . $train_num . "'") or die("Could not update en! " . pg_last_error());
	pg_free_result($send_en_all_trains_res);

	//free result
	pg_free_result($trains_res);
}
//delete columns seat, sleepery, sleeperr in trains???

pg_free_result($all_trains_res);
pg_close($conn);
echo "Complete: add columns to all_trains and trains, create table trainxxxx and fill it, update en to all_trains and station_valid and trains.<br>\n";
?>
