<?php
function list_file($data){
        $temp = scandir($data);
        foreach($temp as $v){
                $a = $data . '/' . $v;
                if(is_dir($a)){
                        if($v=='.' || $v=='..'){
                                continue;
                        }
                        list_file($a);
                }else{
			echo "Processing...<br>\n";
                        //todo your code
                }
        }
}
//WHAT THIS PHP DO:
//CONNECT THE DATABASE
//CREATE TABLE TRAINS: TO COPY FROM CSV
//CREATE TABLE ALL_TRAINS: TID, TRAIN_NUM, TRAIN_TYPE.
//SCAN THE TRAIN-2016-10 TO:
////COPY CSV TO TABLE TRAINS
////INSERT BASIC TRAIN INFO INTO ALL_TRAINS
//
echo "Connecting...<br>\n";
$conn = pg_connect("host=localhost dbname=postgres user=postgres password=123") or die("Could not connect! " . pg_last_error());
echo "Connect success!<br>\n";

//create table all_trains: tid, train_num, train_type
echo "Create table all_trains...<br>\n";
$create_all_trains_res = pg_query($conn, "create table all_trains ( tid integer not null, train_num varchar(10) not null, train_type char(1) not null)") or die("Could not create all_trains... " . pg_last_error());
echo "Create table all_trains complete!<br>\n";

//then scan the file to insert into all_trains, and create table trainxxxx, and copy into the table trainxxxx.
//using $count to generate tid in all_trains
$file = '/var/www/html/test/train-2016-10/';
$type = array("c", "d", "g", "k", "t", "z", "y", "0");
$count = 1;
for($i=0;$i<8;$i++){
	$location = $file . $type[$i];
	//$type[$i]==train_type
	$temp = scandir($location);
	foreach($temp as $v){
		$a = $location . '/' . $v;
		//$v==xxxx.csv
		if(is_dir($a)){
			if($v=='.' || $v=='..'){
				continue;
			}
			list_file($a);
		}else{
			$train_num = substr($v, 0, -4);
			//$train_num==xxxx
			//$count==tid
			echo "processing file=" . $file . " type=" . $type[$i] . " v=" . $v . "...<br>\n";
			//todo: create table trainxxxx
			$create_trainxxxx_res = pg_query($conn, "create table train" . $train_num . " ( tsid integer not null, station varchar(20) not null, arr_time varchar(10) not null, dep_time varchar(10) not null, stop_time varchar(10) not null, running_time varchar(10) not null, mileage varchar(10) not null, seat varchar(20) not null, sleepery varchar(20) not null, sleeperr varchar(20) not null);") or die("Could not create table trainxxxx. " .pg_last_error());
			echo "Create table train" . $train_num . " success!<br>\n";
			//todo: copy from csv to table trainxxxx: 
			$copy_from_csv_to_trainxxxx_res = pg_query($conn, "copy train" . $train_num . " from '" . $a . "' with csv delimiter ',' header;")or die("Could not copy! " . pg_last_error());
			echo "Copy from csv success!<br>\n";
			//insert into table all_trains: tid, train_num, train_type
			$insert_all_trains_res = pg_query($conn, "insert into all_trains values (" . $count . ", '" . $train_num . "', '" . $type[$i] . "');") or die("Could not connect. " . pg_last_error());
			echo "Insert all_trains success!<br>\n";
			$count+=1;
			pg_free_result($insert_all_trains_res);
			pg_free_result($copy_from_csv_to_trainxxxx_res);
			pg_free_result($create_trainxxxx_res);
		}
	}
}

//create table all_stations: station_id, station, station_city
echo "Create table all_stations...<br>\n";
$create_all_stations_res = pg_query($conn, "create table all_stations ( sid integer not null, station varchar(20) not null, station_city varchar(20) not null);") or die("Could not create all_stations. " . pg_last_error());
echo "Create all_stations complete!<br>\n";
pg_free_result($create_all_stations_res);

//copy from all-stations.txt
echo "Copy from all-stations.txt...<br>\n";
$copy_from_all_station_txt_res = pg_query($conn, "copy all_stations from '/var/www/html/test/train-2016-10/all-stations.txt' with csv delimiter ',';") or die("Could not copy! " . pg_last_error());
pg_free_result($copy_from_all_station_txt_res);
echo "Copy from all-stations complete!<br>\n";

//free result
pg_free_result($create_all_trains_res);
pg_close($conn);
echo "Complete!<br>\n";
?>
