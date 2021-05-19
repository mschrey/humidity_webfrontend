<?php
include('humidity_inc.php'); 

include('humidity_header.php');

//printf("<h3>Feuchtesensor Status</h3>\n");

$mydata = new data_from_db();









printf("<table border=\"3\" width=\"100%%\">\n");
printf("<tr>\n");
foreach($id2room as $myid => $roomname) {
    $mydata = get_latest_entry($myid);
    $datetime = $mydata->datetime;
    $temperature = $mydata->temperature;
    $humidity = $mydata->humidity;
    $battvoltage = $mydata->battvoltage;
    if($datetime > 0) {
        printf("<td align=\"center\"><b>%s</b><br>", $roomname);        
        if((time() - $datetime) > 3600) {
            printf("<img src=\"pics/offline.png\" width=\"20\" height=\"20\"><br>\n");
        } else {
            printf("<img src=\"pics/online.png\" width=\"20\" height=\"20\"><br>\n");    
        }
        printf("%s<br>\n", print_canvas_hum($humidity, 150));
        if(strcmp($roomname, "draussen") == 0) {        
            printf("%s<br>\n", print_canvas_temp($temperature, 100, "outside"));        
        } else {
            printf("%s<br>\n", print_canvas_temp($temperature, 100, "inside"));        
        }
        printf("<a href=\"battstatus.php?id=$myid\">%s (%5.3fV)</a><br>", print_batt_status($battvoltage), $battvoltage/1000);
        printf(" %4.1f%%rH, ", $humidity);
        printf(" %4.1f°C", $temperature);
        printf("</td>\n\n\n");
    }
}
printf("</tr>\n");
printf("</table>\n");
printf("Last loaded on %s\n", date("Y-m-d H:i:s")); 
printf("</body>\n");
printf("</html>\n");

?>
