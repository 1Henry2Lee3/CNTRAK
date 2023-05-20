<?php
//connect the database
echo "Connecting...<br>\n";
$conn = pg_connect("host=localhost dbname=postgres user=postgres password=123") or die("Could not connect! " . pg_last_error());
echo "Connection successful!<br>\n";

//query the table all_trains to scan
echo "Querying the table all_trains.<br>\n";
$all_trains_res = pg_query($conn, "select * from all_trains")or die("Could not query all_trains. " . pg_last_error());
echo "Query all_trains complete.<br>\n";
$trains_num = pg_num_rows($all_trains_res);
echo "There are " . $trains_num . " trains in China.<br>\n";

//create table trains: train_id, train_num, train_type, train_station_id, station, arr_time, dep_time, stop_time, running_time, mileage, seat, sleepery, sleeperr
echo "Creating table trains...<br>\n";
$create_trains_res = pg_query($conn, "create table trains ( train_id integer not null, train_num varchar(10) not null, train_type char(1) not null, train_station_id integer not null, station varchar(20) not null, arr_time varchar(10) not null, dep_time varchar(10) not null, stop_time varchar(10) not null, running_time varchar(10) not null, mileage integer not null, seat varchar(20) not null, sleepery varchar(20) not null, sleeperr varchar(20) not null);") or die("Could not create trains! " . pg_last_error());
echo "Create table trains complete.<br>\n";
pg_free_result($create_trains_res);

//when creating tickets, using date + time = timestamp??? date + interval = timestamp, and add running_time on each station to generate timestamp for all stations.
//dep_time and arr_time sometimes are '-' in trainxxxx, so let dep_time = arr_time and arr_time = dep_time;
//stop_time and running_time sometimes is '-', let them be '0 min';
//if stop_time is 'xxx分', delete the last char, and turn it into 'xxx min';
//MAKE TIME VARCHAR FIRST, BUT MAKE SURE IN INTERVAL FORMAT. THEN USING TRIM, THEN CHANGE TYPE INTO INTERVAL.
//MAKE MILEAGE VALCHAR FIRST, THEN TRIM, THEN USE INTVAL???
/*
echo "Creating table trains...<br>\n";
$create_trains_res = pg_query($conn, "create table trains ( train_id integer not null, train_num varchar(10) not null, train_type char(1) not null, train_station_id integer not null, station varchar(20) not null, arr_time interval not null, dep_time interval not null, stop_time interval, running_time interval, mileage integer not null, seat varchar(20) not null, sleepery varchar(20) not null, sleeperr varchar(20) not null);") or die("Could not create table trains! " . pg_last_error());
echo "Creating trains complete!<br>\n";
pg_free_result($create_trains_res);
*/

//insert datas into table trains: first from all_trains, then trainxxxx
for($i=0;$i<$trains_num;$i++){
        //scan the all_trains the 2nd line---train_num, from the first to the last
        $train_id = pg_fetch_result($all_trains_res, $i, 0);
        $train_num = pg_fetch_result($all_trains_res, $i, 1);
        $train_type = pg_fetch_result($all_trains_res, $i, 2);
        //echo "Train_id is " . $train_id . ", ";
        echo "Train_num is " . $train_num . ", ";
        //echo "Train_type is " . $train_type . "<br>\n";

        //remember: trainz99 and trainZ99 is the same

        //now query the trainxxxx
        $trainxxxx_res = pg_query($conn, "select * from train" . $train_num) or die("Could not query trainxxxx. " . pg_last_error());
        $trainxxxx_num = pg_num_rows($trainxxxx_res);
        //insert into trains
        for($j=0;$j<$trainxxxx_num;$j++){
                $train_station_id = pg_fetch_result($trainxxxx_res, $j, 0);
                //echo "train_station_id is " . $train_station_id . ", ";
                $station = pg_fetch_result($trainxxxx_res, $j, 1);
                //echo "station is " . $station . ", ";
                $arr_time = pg_fetch_result($trainxxxx_res, $j, 2);
                //echo "arr_time is " . $arr_time . ", ";
                $dep_time = pg_fetch_result($trainxxxx_res, $j, 3);
                //echo "dep_time is " . $dep_time . ", ";
                //before processing, delete all spaces first
                $arr_time = trim($arr_time);
                $dep_time = trim($dep_time);
                //make sure when '-', arr_time and dep_time is the same
                if($arr_time=='-'){
                        $arr_time = $dep_time;
                }
                if($dep_time=='-'){
                        $dep_time = $arr_time;
                }
                
                $stop_time = pg_fetch_result($trainxxxx_res, $j, 4);
                //echo "stop_time is " . $stop_time . ", ";
                $running_time = pg_fetch_result($trainxxxx_res, $j, 5);
                //echo "running_time is " . $running_time . ", ";
                //before processing, delete all spaces first
                $stop_time = trim($stop_time);
                $running_time = trim($running_time);
                //stop time: '-' or '' or 'xxx分'
                //running_time: '-' or 'xxx'
		if($stop_time=="-" || $stop_time==""){
			//echo "Found null!, changing...";
			$real_stop_time = "0 min";
			//echo "Now stop_time is " . $real_stop_time . ", ";
                }else{
                        $stop_time_len = strlen($stop_time);
                        $real_stop_time_arr = array();
                        for($k=0, $h=0;$k<$stop_time_len;$k++){
                                if($stop_time[$k]>='0' && $stop_time[$k]<='9'){
                                        $real_stop_time_arr[$h] = $stop_time[$k];
                                        $h++;
                                }
                        }
			$real_stop_time = implode($real_stop_time_arr);
			//echo "Before adding 'min', real_stop_time is " . $real_stop_time . ", ";
                        $real_stop_time .= " min";
		}

                if($running_time=="-"){
                        $real_running_time = "0 min";
                }else{
                        $real_running_time = $running_time . " min";
                }
		//echo "Now real_stop_time is: " . $real_stop_time . ", ";
		//echo "Now real_running_time is: " . $real_running_time . ", ";

                $mileage = pg_fetch_result($trainxxxx_res, $j, 6);
                //echo "mileage is " . $mileage . ", ";
                $mileage = trim($mileage);
                if($mileage=="-"){
                        $mileage = "0";
                }
                //change into integer
                $real_mileage = intval($mileage);
		//echo "Now real_mileage is " . $real_mileage . ", ";
                $seat = pg_fetch_result($trainxxxx_res, $j, 7);
                //echo "seat is " . $seat . ", ";
                $sleepery = pg_fetch_result($trainxxxx_res, $j, 8);
                //echo "sleepery is " . $sleepery . ", ";
                $sleeperr = pg_fetch_result($trainxxxx_res, $j, 9);
                //echo "sleeperr is " . $sleeperr . "<br>\n";

                //echo "inserting...<br>\n";
                $insert_train_res = pg_query($conn, "insert into trains values (" . $train_id . ", '" . $train_num . "', '" . $train_type . "', " . $train_station_id . ", '" . $station . "', '" . $arr_time . "', '" . $dep_time . "', '" . $real_stop_time . "', '" . $real_running_time . "', " . $real_mileage . ", '" . $seat . "', '" . $sleepery . "', '" . $sleeperr . "')") or die("Could not insert! " . pg_last_error());
                //echo "insert values (" . $train_id . ", '" . $train_num . "', '" . $train_type . "', " . $train_station_id . ", '" . $station . "', '" . $arr_time . "', '" . $dep_time . "', '" . $real_stop_time . "', '" . $real_running_time . "', " . $real_mileage . ", '" . $seat . "', '" . $sleepery . "', '" . $sleeperr . "') complete!<br>\n";
	}
	echo "insert train " . $train_num . " complete!<br>\n";
        pg_free_result($trainxxxx_res);
}
echo "All train data insert complete!<br>\n";

//add column station_id and station_city to trains
echo "Now adding two columns: station_id and station_city from table all_stations.<br>\n";
$add_station_id_res = pg_query($conn, "alter table trains add column station_id integer")or die("Could not add station_id! " . pg_last_error());
echo "add column station_id success!<br>\n";
pg_free_result($add_station_id_res);

$add_station_city_res = pg_query($conn, "alter table trains add column station_city varchar(20)")or die("Could not add station_city! " . pg_last_error());
echo "add column station_city success!<br>\n";
pg_free_result($add_station_city_res);

//todo: SEE THIS: before adding station_id and station_city
//todo: all the spaces must be deleted
//todo: DELETE SPACES USING TRIM
//1. trim() table all_trains: TID integer, TRAIN_NUM varchar(10), TRAIN_TYPE.
$update_all_trains_res = pg_query($conn, "update all_trains set train_num = trim(train_num)") or die("Could not update. " . pg_last_error());
echo "TRIM all_trains complete!<br>\n";
pg_free_result($update_all_trains_res);

//2. trim() table trains:  train_station_id, station varchar(20), arr_time varchar(10), dep_time varchar(10), stop_time varchar(10), running_time varchar(10), mileage varchar(10), seat varchar(20), sleepery varchar(20), sleeperr varchar(20)//train_id, train_num, train_type, train_station_id, station, arr_time, dep_time, stop_time, running_time, mileage, seat, sleepery, sleeperr
$update_trains_res0 = pg_query($conn, "update trains set train_num = trim(train_num)") or die("Could not update. " . pg_last_error());
pg_free_result($update_trains_res0);

$update_trains_res1 = pg_query($conn, "update trains set station = trim(station)") or die("Could not update. " . pg_last_error());
pg_free_result($update_trains_res1);

/*
$update_trains_res2 = pg_query($conn, "update trains set arr_time = trim(arr_time)") or die("Could not update. " . pg_last_error());
pg_free_result($update_trains_res2);

$update_trains_res3 = pg_query($conn, "update trains set dep_time = trim(dep_time)") or die("Could not update. " . pg_last_error());
pg_free_result($update_trains_res3);

$update_trains_res4 = pg_query($conn, "update trains set stop_time = trim(stop_time)") or die("Could not update. " . pg_last_error());
pg_free_result($update_trains_res4);

$update_trains_res5 = pg_query($conn, "update trains set running_time = trim(running_time)") or die("Could not update. " . pg_last_error());
pg_free_result($update_trains_res5);

$update_trains_res6 = pg_query($conn, "update trains set mileage = trim(mileage)") or die("Could not update. " . pg_last_error());
pg_free_result($update_trains_res6);
*/

$update_trains_res7 = pg_query($conn, "update trains set seat = trim(seat)") or die("Could not update. " . pg_last_error());
pg_free_result($update_trains_res7);

$update_trains_res8 = pg_query($conn, "update trains set sleepery = trim(sleepery)") or die("Could not update. " . pg_last_error());
pg_free_result($update_trains_res8);

$update_trains_res9 = pg_query($conn, "update trains set sleeperr = trim(sleeperr)") or die("Could not update. " . pg_last_error());
pg_free_result($update_trains_res9);
echo "TRIM trains complete!<br>\n";

//3. trim() table all_stations: sid integer, station varchar(20), station_city varchar(20)
$update_all_stations_res1 = pg_query($conn, "update all_stations set station = trim(station)") or die("Could not update. " . pg_last_error());
pg_free_result($update_all_stations_res1);

$update_all_stations_res2 = pg_query($conn, "update all_stations set station_city = trim(station_city)") or die("Could not update. " . pg_last_error());
echo "TRIM all_stations complete!<br>\n";
pg_free_result($update_all_stations_res2);

//now update station_id, station_city from all_stations to trains
//first scan the station in trains
//then find it in all_stations, add 2 values
//all_stations: sid, station, station_city
/*
echo "Now update station_id and station_city in table trains!<br>\n";
$trains_res = pg_query($conn, "select * from trains")or die("Could not query! " . pg_last_error());
$trains_num = pg_num_rows($trains_res);
for($i=0;$i<$trains_num;$i++){
        $station_name = pg_fetch_result($trains_res, $i, 4);
        //echo "Station_name is " . $station_name . "<br>\n";
        //echo var_dump($station_name) . "<br>\n";
        $find_station_res = pg_query($conn, "select * from all_stations where station='" . $station_name . "'")or die("Could not find station! " . pg_last_error());
        //echo "Successfully find station data! ";

        $station_id = pg_fetch_result($find_station_res, 0, 0);
        //echo "Station_id is " . $station_id . ", ";
        $station_city = pg_fetch_result($find_station_res, 0, 2);
        //echo "Station_city is " . $station_city . "<br>\n";
        //update trains
        $update_id_and_city_res = pg_query($conn, "update trains set station_id = " . $station_id . ", station_city = '" . $station_city . "' where station='" . $station_name . "'")or die("Could not update! " . pg_last_error());
        //echo "update sucess!<br>\n";
        pg_free_result($update_id_and_city_res);
        pg_free_result($find_station_res);
}
echo "Update station_id and station_city complete!<br>\n";
*/

echo "Now update station_id and station_city in table trains!<br>\n";
$all_stations_res = pg_query($conn, "select * from all_stations order by sid asc")or die("Could not query all_stations! " . pg_last_error());
$all_stations_res_num = pg_num_rows($all_stations_res);
for($i=0;$i<$all_stations_res_num;$i++){
	$station_name = pg_fetch_result($all_stations_res, $i, 1);
	$station_id = pg_fetch_result($all_stations_res, $i, 0);
        $station_city = pg_fetch_result($all_stations_res, $i, 2);
	//update trains where station = $station_name
	$update_id_and_city_res = pg_query($conn, "update trains set station_id = " . $station_id . ", station_city = '" . $station_city . "' where station = '" . $station_name . "'")or die("Could not update! " . pg_last_error());
}
echo "Update all station info complete!<br>\n";
pg_free_result($all_stations_res);

//now change arr_time, dep_time, stop_time, running_time into interval
echo "Now changing time from varchar to interval<br>\n";
$change_time_into_interval_res1 = pg_query($conn, "alter table trains alter column arr_time type interval using arr_time::interval")or die("Could not change arr_time datatype! " . pg_last_error());
pg_free_result($change_time_into_interval_res1);
$change_time_into_interval_res2 = pg_query($conn, "alter table trains alter column dep_time type interval using dep_time::interval")or die("Could not change dep_time datatype! " . pg_last_error());
pg_free_result($change_time_into_interval_res2);
$change_time_into_interval_res3 = pg_query($conn, "alter table trains alter column stop_time type interval using stop_time::interval")or die("Could not change stop_time datatype! " . pg_last_error());
pg_free_result($change_time_into_interval_res3);
$change_time_into_interval_res4 = pg_query($conn, "alter table trains alter column running_time type interval using running_time::interval")or die("Could not change running_time datatype! " . pg_last_error());
echo "Change time from varchar to interval complete!<br>\n";
pg_free_result($change_time_into_interval_res4);

//free result and close connection
pg_free_result($trains_res);
pg_close($conn);
echo "All complete!<br>\n";
?>
