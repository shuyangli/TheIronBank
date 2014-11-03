<?php
//connecting safely

$host = 'localhost';
$username = 'sli8';
$password = 'fmdbMysql';
$database = 'sli8';

$link = mysqli_connect($host, $username, $password, $database);
if($link->connect_errno) {
	echo "failed connection".$link->connect_errno.":".$link->connect_error;
}

echo mysql_get_server_info() . "\n"; 

//prepare statement
// $stmt = $link->prepare("insert into FM_Person (age) values (?);");

// //bind parameters
// $stmt->bind_param("i", $_GET['age']);

// $stmt->execute();

// //query
// $query = 'SELECT * from user_age;';
// $result = $link->query($query) or die("Query Failed");

// echo '<table border="1">';
// while($tuple = mysqli_fetch_array($result, MYSQL_ASSOC)) {
//         echo '<tr>';
//         foreach($tuple as $colval) {
//                 echo '<td>'.$colval.'</td>';
//         }
//         echo '</tr>';
// }
// echo '</table>';

mysqli_close($link);

?>