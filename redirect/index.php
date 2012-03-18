<html>
<head>
<title>Введите пожалуйста пароль</title>
</head>
<body>
<table align='center'>

<?php
$mysql_user = "clients";
$mysql_pass = "M6t6z";
$mysql_server = "localhost";
$mysql_dbname = "clients";

if (isset($_POST['password'])) {
    if (! $link = mysql_connect($mysql_server, $mysql_user, $mysql_pass)) {
	die("Could not connect to mysql: " . mysql_error());
    }

    if (! mysql_select_db($mysql_dbname, $link)) {
	die("Could not select db: " . mysql_error());
    }

    if (! $result = mysql_query("SELECT * FROM passwords WHERE password = '" . $_POST['password'] . "' AND active = '1'")) {
	die("Invalid query: " . mysql_error());
    }

    if (mysql_num_rows($result) > 0) {
	$arp = exec("/sbin/arp -n -a " . $_SERVER['REMOTE_ADDR']);

	// если в строке есть mac адрес
	if (preg_match("/([A-F0-9]{2}\:){5}[A-F0-9]{2}/", $arp, $mac)) {
	    // разрешаем доступ с данного mac адреса
	    exec("sudo /sbin/iptables -t nat -A CLIENTS -m mac --mac-source ".$mac[0]." -m comment --comment \"".$_SERVER['REQUEST_TIME']."\" -j ACCEPT");
	    exec("sudo /sbin/iptables -t filter -A CLIENTS -m mac --mac-source ".$mac[0]." -m comment --comment \"".$_SERVER['REQUEST_TIME']."\" -j ACCEPT");
	    // помечаем пароль как использованный
	    if (! $result = mysql_query("UPDATE passwords SET active = '0' WHERE password = '".$_POST['password']."' AND active = '1' LIMIT 1")) {
	        die("Invalid query: " . mysql_error());
	    }
	    // перенаправляем клиента туда, куда он и хотел
	    header("Location: http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REDIRECT_URL']);
	} else {
	    echo "<center>ERROR: невозможно получить MAC адрес</center>";
	}
	
	mysql_close($link);
	exit;
    } else {
	echo "<center><font color='red' size='2'>Введённый пароль не действителен. Повторите попытку.</font></center>\n";
    }

    mysql_close($link);
}
?>

<form method='POST'><tr><th><h2>Введите пароль для доступа в сеть Интернет:</h2></th></tr>
<tr><td align='center'><input type='text' name='password'></td></tr>
<tr><td align='center'><input type='submit' value='Отправить'></td></tr></form>
</table>
</body>
</html>
