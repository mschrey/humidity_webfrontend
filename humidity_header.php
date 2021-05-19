<?php
print_header();
printf("<a href=\"index.php\">[Status]</a> \n");

foreach($id2room as $myid => $roomname) {
    $mydata = get_latest_entry($myid);
    if($mydata->datetime > 0) {
       printf("<a href=\"humiditydata.php?id=%d\">[%s]</a> \n", $myid, $roomname);
    }
}
printf("<br>\n\n\n");
?>
