<html>
<head>
<title>Генератор паролей</title>
</head>
<style type = "text/css">
table, td {
#    border-style: solid;
    border-style: dashed;
    border-color: #AAAAAA;
    border-width: 1px;
    border-collapse: collapse;
    font-size: 22;
    height: 50px;
}
</style>
<body>

<?
$mysql_user = "clients";
$mysql_pass = "M6t6z";
$mysql_server = "localhost";
$mysql_dbname = "clients";

function pwgen() {
    $chars = array (
	'a', 'c', 'e', 'f', 'g', 'h', 'j',
	'k', 'm', 'n', 'p', 'q', 'r', 's',
	't', 'u', 'v', 'w', 'x', 'y', 'z'
    );

    for ($i = 0; $i < 6; $i++) {
	$pass .= $chars[rand() % sizeof($chars)];
    }

    return $pass;
}

if (! $link = mysql_connect($mysql_server, $mysql_user, $mysql_pass)) {
    die("Could not connect to mysql: " . mysql_error());
}

if (! mysql_select_db($mysql_dbname, $link)) {
    die("Could not select db: " . mysql_error());
}

if (isset($_POST['generate'])) {
    // зачем удаляем старые пароли - не понимаю
    if (! $result = mysql_query("TRUNCATE TABLE passwords")) {
	die("Invalid query: " . mysql_error());
    }

    echo "<table width='100%' cellpadding='0' cellspacing='0'>";

    $query = "INSERT INTO passwords (password) VALUES";

    for ($x = 0; $x < 19; $x++) {
	echo "<tr>";
	for ($y = 0; $y < 5; $y++) {
	    $pass = pwgen();
	    echo "<td align='center'>" . $pass . "</td>";
	    $query .= sprintf ("%s('%s')", ($x + $y) ? ", " : " ", $pass);
	}
	echo "</tr>\n";
    }

    echo "</table>\n";

    if (! $result = mysql_query($query)) {
	die("Invalid query: " . mysql_error());
    }
} elseif (isset($_POST['show_unused'])) {
    echo "<table width='100%' cellpadding='0' cellspacing='0'>";

    $query = "SELECT password, active FROM passwords";

    if (! $result = mysql_query($query)) {
	die("Invalid query: " . mysql_error());
    }

    for ($x = 0; $x < 19; $x++) {
	echo "<tr>";
	for ($y = 0; $y < 5; $y++) {
	    $row = mysql_fetch_array($result);
	    printf("<td align='center'>%s</td>", ($row && $row[1]) ? $row[0] : "&nbsp;");
	}
	echo "</tr>\n";
    }

    echo "</table>\n";

} else {
    echo "&nbsp;<center><form method='POST'><input type='submit' name='generate' value='Сгенерировать пароли'>\n";
    echo "<input type='submit' name='show_unused' value='Показать неиспользуемые'></form></center>\n";
}
?>
</body>
</html>
