<?php

//connecting safely

$link = mysqli_connect('localhost', 'sli8', 'fmdbMysql', 'sli8');
if($link->connect_errno) {
	echo "failed connection".$link->connect_errno.":".$link->connect_error;
}

// //prepare statement
// $stmt = $link->prepare("insert into user_age (age) values (?);");

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