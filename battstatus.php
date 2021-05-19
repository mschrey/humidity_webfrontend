<?php 
include('humidity_inc.php'); 
include('humidity_header.php');


$day = date("d", time());
$month= date("m", time());
$year= date("y", time());
$lastmidnight = mktime(0, 0, 0, $month, $day, $year);  //unix timestamp most most recent midnight
$onedayinseconds = 24*3600;
$stop = $lastmidnight;
$start = $lastmidnight - (30 * $onedayinseconds);


if (!isset($_GET['id'])){
    $id = 1;
} else {
    $id = $_GET['id'];
}

printf("<h3>Battery Status $id2room[$id]</h3>");

$mydatafromdb = get_data_from_mysql($id, $start, $stop);


generate_battgraph($mydatafromdb);





printf("<img src=\"pics/battgraph.png\" border=\"0\"><br>\n");
?>
