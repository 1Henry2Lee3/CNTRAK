<html>
<head>
<meta charset="utf-8">
<title>Register page</title>
</head>
<body>
<h3>Welcome to register page!</h3>
<a href="http://localhost:8080/CNTRAK_230513/login.php">Return to login</a><br>
Please input following information:<br>
<form action="" method="post">
xingming: <input type="text" name="xingming"><br>
phone: <input type="text" name="phone"><br>
username: <input type="text" name="username"><br>
password: <input type="password" name="userpassword"><br>
<input type="submit" name="OK">
</form>
<?php
$xingming = $_POST["xingming"];
$phone = $_POST["phone"];
$username = $_POST["username"];
$userpassword = $_POST["userpassword"];
echo "Xingming: " . $xingming . ", ";
echo "Phone: " . $phone . ", ";
echo "Username: " . $username . ", ";
echo "Password: " . $userpassword . "<br>\n";

echo "Connecting to the database...<br>\n";
$conn = pg_connect("host=localhost dbname=postgres user=postgres password=123")or die("Could not connect! " . pg_last_error());
echo "Connect success!<br>\n";
echo "Querying...<br>\n";
$user_res = pg_query($conn, "select * from users where username='" . $username . "' and userpassword='" . $userpassword . "'")or die("Could not connect! " . pg_last_error());
$res_num = pg_num_rows($user_res);
if($username!="" && $userpassword!=""){
	if($res_num==0){
		$user_num_res = pg_query($conn, "select * from users") or die("Could not query! " . pg_last_error());
                $user_num = pg_num_rows($user_num_res);
                $user_id = $user_num;
		$register_res = pg_query($conn, "insert into users values (" . $user_id . ", '" . $xingming . "', '" . $phone . "', '" . $username . "', '" . $userpassword . "')")or die("Could not insert into users! " . pg_last_error());
		echo "User registered! Goto home page<br>\n";
		//goto home_page
?>
		<script language="javascript">
                        location.replace("http://localhost:8080/CNTRAK_230513/home_page.php?user=<?php echo $username;?>");
		</script>
<?php
	}else{
		//user have found, re-enter
		echo "You have registered!<br>\n";
	}
}else{
	echo "Please re-enter.<br>\n";
}
?>
</body>
</html>
