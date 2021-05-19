<?php 
include('humidity_inc.php'); 
include('humidity_header.php');





    $day = date("d", time());
    $month= date("m", time());
    $year= date("y", time());
    $lastmidnight = mktime(0, 0, 0, $month, $day, $year);  //unix timestamp most most recent midnight
    $onedayinseconds = 24*3600;
if (!isset($_GET['start'])){
    //$stop = $lastmidnight;
    //$start = $lastmidnight - $onedayinseconds;
    $start = $lastmidnight;
    $stop = $lastmidnight + $onedayinseconds;
} else {
    $start = $_GET['start'];
    $stop = $_GET['stop'];
}


if (!isset($_GET['id'])){
    $id = 1;
} else {
    $id = $_GET['id'];
}

$mydatafromdb = get_data_from_mysql($id, $start, $stop);

printf("<h3>%s</h3>\n", $id2room[$id]);

printf("<table align=\"center\"><tr><td>\n");
printf("  <a href=\"humiditydata.php?id=%d&start=%d&stop=%d\"><img src=\"pics/rew2.png\" width=80></a>", $id, $start-(24*7*3600), $stop-(24*3600));
printf("</td><td>\n");
printf("  <a href=\"humiditydata.php?id=%d&start=%d&stop=%d\"><img src=\"pics/rew.png\" width=80></a>", $id, $start-(24*3600), $stop-(24*3600));
printf("</td><td>\n");
printf("  <a href=\"humiditydata.php?id=%d&start=%d&stop=%d\"><img src=\"pics/today.png\" width=80></a>", $id, $lastmidnight, $lastmidnight+(24*3600));
printf("</td><td>\n");
printf("  <a href=\"humiditydata.php?id=%d&start=%d&stop=%d\"><img src=\"pics/fwd.png\" width=80></a>", $id, $start+(24*3600), $stop+(24*3600));
printf("</td><td>\n");
printf("  <a href=\"humiditydata.php?id=%d&start=%d&stop=%d\"><img src=\"pics/fwd2.png\" width=80></a>", $id, $start+(24*7*3600), $stop+(24*3600));
printf("</td></tr>\n</table>\n");

if ($mydatafromdb != NULL) {
    generate_graph($mydatafromdb);
    printf("<img src=\"pics/humiditygraph.png?nocache=" . time() . "\" width=\"100%%\" border=\"0\"><br>\n");
} else {
    printf("<br>Error! Insufficient data!\n");
}
printf("</body>\n</html>\n");
?>
