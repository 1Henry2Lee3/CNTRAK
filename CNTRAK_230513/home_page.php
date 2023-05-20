<html>
<head>
<meta charset="utf-8">
<title>home page</title>
</head>
<body>
<a href="http://localhost:8080/CNTRAK_230513/login.php">Return to login</a><br>
<?php
$user = $_GET["user"];
//echo "user is " . $user . "<br>\n";

echo "<h3>Welcome! user " . $user . ".</h3>\n";
echo "Please choose what you want to do below.<br>\n";
echo "<a href=\"http://localhost:8080/CNTRAK_230513/query_train_num.php?user=" . $user . "\">Query train number</a><br>\n";
echo "<a href=\"http://localhost:8080/CNTRAK_230513/query_train_between_cities.php?user=" . $user . "\">Query train between two cities</a><br>\n";
echo "<a href=\"http://localhost:8080/CNTRAK_230513/user_order_manage.php?user=" . $user . "\">Manage your orders</a><br>\n";
?>
</body>
</html>
