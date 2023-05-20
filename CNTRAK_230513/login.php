<html>
<head>
<meta charset="utf-8">
<title>CNTRAK</title>
</head>
<body>
<h3>Welcome to CNTRAK</h3>
please login<br>
<form action="" method="post">
name: <input type="text" name="username">
<br>
password: <input type="password" name="password">
<br>
<input type="submit" value="OK">
</form>
<form action="register.php" method="post">
New user? Register now! <input type="submit" value="Register">
</form>
<?php
$name = $_POST['username'];
$password = $_POST['password'];
echo "Name is " . $name . "<br>\n";
echo "Password is " . $password . "<br>\n";
//examine the name and the password in table users:uid, name, password
echo "Connecting to database...<br>\n";
$conn = pg_connect("host=localhost dbname=postgres user=postgres password=123")or die("Could not connect! " . pg_last_error());
echo "Connect success!<br>\n";
echo "Querying...<br>\n";
$user_res = pg_query($conn, "select * from users where username='" . $name . "' and userpassword='" . $password . "'")or die("Could not connect! " . pg_last_error());
$res_num = pg_num_rows($user_res);
if($name!="" && $password!=""){
	if($res_num==0){
		//no user, register
		echo "No user and password, if you are new, Please register...<br>\n";
	}else{
		//user found, goto pages
		echo "Welcome user: " . $name . "<br>\n";
		//if user not admin, to admin_home_page
		//else to home_page
		if($name=="admin"){
			//goto admin_home_page
?>
			<script language="javascript">
				location.replace("http://localhost:8080/CNTRAK_230513/admin_home_page.php");
			</script>
<?php
		}else{
			//goto home_page
?>
			<script language="javascript">
				location.replace("http://localhost:8080/CNTRAK_230513/home_page.php?user=<?php echo $name?>");
			</script>
<?php
		}
	}
}else{
	echo "Please re-enter.<br>\n";
}
?>
</body>
</html>
