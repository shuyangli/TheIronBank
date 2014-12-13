<?php

include('partials/connect.php');

//parse input data
$db_directors = $_GET['director'];
$db_writersArray = explode(',',$_GET['writer']);
$db_distributor = $_GET['distributor'];
$db_rating = $_GET['rating'];
$db_genre = $_GET['genre'];
$db_releaseYear = $_GET['year'];
$db_relevantDecade = $db_releaseYear-10;
$db_actorsArray = explode(',',$_GET['actorList']);

//Perform Linear Regression to make predictions
$estimates = array(); //Will hold estimates for gross based upon each input

//--Distributor--
$query = "select Gross from FM_Film where Distributor=? and Gross!='null' and Release_Year>=? order by Release_Year desc limit 25;";

if ($stmt = $link->prepare($query)){
    $stmt->bind_param("si", $db_distributor, $db_relevantDecade);
    $stmt->execute();
    $stmt->store_result();
    $result = $stmt->bind_result($gross);

    $sum = 0;
    $count = 0;

    while($stmt->fetch()){ 
        if ($gross>0){
            $count = $count +1;
            $sum = $sum + $gross;
        }
    }

    if ($count>0){
        array_push($estimates, $sum/$count);
    }
    $stmt->free_result();
    $stmt->close();
}

//--Writers--
$sum = 0;
$count = 0;

for ($i = 0; $i < count($db_writersArray); ++$i){
    $writer = trim($db_writersArray[$i]);
    #print $writer . "<br>";
    $query = "select Person_ID from FM_Person where Person_Name=?;";
    if ($stmt = $link->prepare($query)){
        $stmt->bind_param("s", $writer);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($writer_id);
        while($stmt->fetch()){ 
            #print $writer_id . "<br>";
            if ($writer_id>0){
                //Get movie Id from writer table
                $query2 = "select IMDB_ID from FM_Wrote where Person_ID=?;";
                if ($stmt2 = $link->prepare($query2)){
                    $stmt2->bind_param("i", $writer_id);
                    $stmt2->execute();
                    $stmt2->store_result();
                    $stmt2->bind_result($movie_id);
                    while($stmt2->fetch()){ 
                        #print $movie_id . "<br>";
                        if ($movie_id!=null){
                            //Get gross from FM_film using movie_id
                            $query3 = "select Gross from FM_Film where IMDB_ID=? and Gross!='null' and Release_Year>=? order by Release_Year desc limit 25;";
                            if ($stmt3 = $link->prepare($query3)){
                                $stmt3->bind_param("si", $movie_id, $db_relevantDecade);
                                $stmt3->execute();
                                $stmt3->store_result();
                                $stmt3->bind_result($gross);

                                while($stmt3->fetch()){ 
                                    #print $gross . "<br>";
                                    if ($gross>0){
                                        $count = $count +1;
                                        $sum = $sum + $gross;
                                    }
                                }
                                $stmt3->free_result();
                                $stmt3->close();
                            }
                        }
                    }
                    $stmt2->free_result();
                    $stmt2->close();
                }
            }
        }
        $stmt->free_result();
        $stmt->close();
    }
}
if ($count>0){
    array_push($estimates, $sum/$count);
}

//Actors
$sum = 0;
$count = 0;

for ($i = 0; $i < count($db_actorsArray); ++$i){
    $actor = trim($db_actorsArray[$i]);
    #print $actor . "<br>";
    $query = "select Person_ID from FM_Person where Person_Name=?;";
    if ($stmt = $link->prepare($query)){
        $stmt->bind_param("s", $actor);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($person_id);
        while($stmt->fetch()){ 
            #print $person_id . "<br>";
            if ($person_id>0){
                //Get movie Id from Acted_In table
                $query2 = "select IMDB_ID from FM_Acted_In where Person_ID=?;";
                if ($stmt2 = $link->prepare($query2)){
                    $stmt2->bind_param("i", $person_id);
                    $stmt2->execute();
                    $stmt2->store_result();
                    $stmt2->bind_result($movie_id);
                    while($stmt2->fetch()){ 
                        #print $movie_id . "<br>";
                        if ($movie_id!=null){
                            //Get gross from FM_film using movie_id
                            $query3 = "select Gross from FM_Film where IMDB_ID=? and Gross!='null' and Release_Year>=? order by Release_Year desc limit 25;";
                            if ($stmt3 = $link->prepare($query3)){
                                $stmt3->bind_param("si", $movie_id, $db_relevantDecade);
                                $stmt3->execute();
                                $stmt3->store_result();
                                $stmt3->bind_result($gross);

                                while($stmt3->fetch()){ 
                                    #print $gross . "<br>";
                                    if ($gross>0){
                                        $count = $count +1;
                                        $sum = $sum + $gross;
                                    }
                                }
                                $stmt3->free_result();
                                $stmt3->close();
                            }
                        }
                    }
                    $stmt2->free_result();
                    $stmt2->close();
                }
            }
        }
        $stmt->free_result();
        $stmt->close();
    }
}
if ($count>0){
    array_push($estimates, $sum/$count);
}

//MPAA_Rating
$query = "select Gross from FM_Film where MPAA_Rating=? and Gross!='null' and Release_Year>=? order by Release_Year desc limit 25;";

if ($stmt = $link->prepare($query)){
    $stmt->bind_param("si", $db_rating, $db_relevantDecade);
    $stmt->execute();
    $stmt->store_result();
    $result = $stmt->bind_result($gross);

    $sum = 0;
    $count = 0;

    while($stmt->fetch()){ 
        if ($gross>0){
            $count = $count +1;
            $sum = $sum + $gross;
        }
    }

    if ($count>0){
        array_push($estimates, $sum/$count);
    }
    $stmt->free_result();
    $stmt->close();
}

//Genre
$query = "select IMDB_ID from FM_Genre where Genre_Name=?;";

if ($stmt = $link->prepare($query)){
    $stmt->bind_param("s", $db_genre);
    $stmt->execute();
    $stmt->store_result();
    $result = $stmt->bind_result($movie_id);

    $sum = 0;
    $count = 0;

    while($stmt->fetch()){
        $query2 = "select Gross from FM_Film where IMDB_ID=? and Gross!='null' and Release_Year>=? limit 1;";

        if ($stmt2 = $link->prepare($query)){
            $stmt2->bind_param("si", $movie_id, $db_relevantDecade);
            $stmt2->execute();
            $stmt2->store_result();
            $result2 = $stmt2->bind_result($gross);

            while($stmt2->fetch()){ 
                if ($gross>0){
                    $count = $count +1;
                    $sum = $sum + $gross;
                }
            }

            $stmt2->free_result();
            $stmt2->close();
        }
    }

    if ($count>0){
        array_push($estimates, $sum/$count);
    }
    $stmt->free_result();
    $stmt->close();
}


//Calculate Results

$sum = array_sum($estimates);
$count = count($estimates);
$estimatedGross = round($sum/$count,-3);
#print "<br>This movie is estimated to Gross around $" . $estimatedGross . " domestically.<br>";
$json_array = [];
array_push($json_array, $estimatedGross);

//Find similar movies
#print "<br>The following recent movies performed simliarly to your estimated gross:<br>";
$query = "select Title,Gross from FM_Film where Gross>(?*.97) and Gross<(?*1.03) order by Release_Year desc limit 5;";

if ($stmt = $link->prepare($query)){
    $stmt->bind_param("ii", $estimatedGross, $estimatedGross);
    $stmt->execute();
    $stmt->store_result();
    $result = $stmt->bind_result($title,$gross);

    while($stmt->fetch()){ 
        #print $title . " grossed $" . $gross . " domestically.<br>";
        array_push($json_array, [$title, $gross]);
    }
    $stmt->free_result();
    $stmt->close();
}

$link->close();
echo json_encode($json_array);
?>