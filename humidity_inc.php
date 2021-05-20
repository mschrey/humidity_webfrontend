<?php // content="text/plain; charset=utf-8"
require_once ('jpgraph/jpgraph.php');
require_once ('jpgraph/jpgraph_line.php');
require_once ('jpgraph/jpgraph_date.php');

class data_from_db
{
    public $id;
    public $humidity;
    public $temperature;
    public $datetime;   
    public $battvoltage; 
}



$id2room = array(
    0 => "draussen",
    1 => "Schlafzimmer",
//    2 => "Arbeitszimmer", 
    3 => "Badezimmer",
//    4 => "Kinderzimmer", 
    5 => "rosa Bad", 
    6 => "Küche", 
    7 => "Wohnzimmer",
);
    


function connect_to_mysql()
{
    $servername = "localhost";
    $username = "MYSQL_USERNAME";
    $password = "MYSQL_PASSWORD";
    $dbname = "DATABASE";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    return $conn;
}


function get_latest_entry($id)
{
    $out = new data_from_db();  
    $conn = connect_to_mysql();
    
//    $sql = "SELECT * FROM `RawData` WHERE id = $id AND datetime=(SELECT MAX(datetime) FROM `RawData`)";
    $sql = "SELECT * FROM `RawData` WHERE id = $id ORDER BY datetime DESC LIMIT 1"; 
    //printf("$sql\n");
    
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
//            echo "id: " . $row["id"]. " - datetime: " . $row["datetime"]. " humidity: " . $row["humidity"]. "<br>";
            $datetime[]    = $row["datetime"];
            $humidity[]    = $row["humidity"];
            $temperature[] = $row["temperature"];
            $battvoltage[] = $row["voltage"];
        }

        $out->humidity    = $humidity[0];  //this is a one-element array, return only its single entry
        $out->datetime    = $datetime[0];
        $out->temperature = $temperature[0];
        $out->battvoltage = $battvoltage[0];        
    } else {
        echo "<!--sql query returned 0 results-->";
        $out->humidity    = 0;  //this is a one-element array, return only its single entry
        $out->datetime    = 0;
        $out->temperature = 0;
        $out->battvoltage = 0;        
    }
    $conn->close();
    
    return $out;
}



function print_header()
{
    header("Refresh:600; url=index.php");
    echo "<!DOCTYPE html>\n";
    echo "<head>\n";
    echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\">\n";
    echo "    <title>Humidity Data</title>\n";    
    echo "<meta http-equiv=\"Cache-Control\" content=\"no-cache, no-store, must-revalidate\" />\n";
    echo "<meta http-equiv=\"Pragma\" content=\"no-cache\" />\n";
    echo "<meta http-equiv=\"Expires\" content=\"0\" />\n";    
    echo "<meta http-equiv=\"Pragma-directive: no-cache\" />\n";
    echo "<meta http-equiv=\"Cache-directive: no-cache\" />\n";
    echo "    <script src=\"gauge.min.js\"></script>\n";
    printf("%s", print_css());
    echo "</head>\n";
    echo "\n";
    echo "<body>\n";
};


function print_css()
{
    $out = "";
    $out = $out . "<style>\n";
    $out = $out . "html, body {\n";
    $out = $out . "    height: 100%;\n";
    $out = $out . "    margin: 0;\n";
    $out = $out . "    padding: 0;\n";
    $out = $out . "}\n";
    $out = $out . "\n";
    $out = $out . "img {\n";
    $out = $out . "    padding: 0;\n";
    $out = $out . "    display: block;\n";
    $out = $out . "    margin: 0 auto;\n";
    $out = $out . "    max-height: 100%;\n";
    $out = $out . "    max-width: 100%;\n";
    $out = $out . "}\n";
    $out = $out . "</style>\n";
    return $out;
}


function print_batt_status($battvoltage)
{
    $battvoltagelimits = array(
        0 => 4000, 
        1 => 3660, 
        2 => 3330, 
        3 => 3000,
    );  
    
    if ($battvoltage > $battvoltagelimits[0]) {
        $out = "<img src=\"pics/batt0.png\">";
    } else if (($battvoltage < $battvoltagelimits[0]) and ($battvoltage > $battvoltagelimits[1])) {
        $out = "<img src=\"pics/batt1.png\">";
    } else if (($battvoltage < $battvoltagelimits[1]) and ($battvoltage > $battvoltagelimits[2])) {
        $out = "<img src=\"pics/batt2.png\">";        
    } else if (($battvoltage < $battvoltagelimits[2]) and ($battvoltage > $battvoltagelimits[3])) {
        $out = "<img src=\"pics/batt3.png\">";        
    } else if ($battvoltage < $battvoltagelimits[3]) {
        $out = "<img src=\"pics/batt4.png\">";        
    } else {
        $out = "error";
    }
    return $out;
}



function print_canvas_hum($value, $size)
{
//print canvas using the library and examples from https://canvas-gauges.com/documentation/examples/
    $output = "";
    $output = $output . "  <canvas data-type=\"radial-gauge\"\n";
    $output = $output . "  data-width=\"" . $size . "\"\n";
    $output = $output . "  data-height=\"" . $size . "\"\n";
    $output = $output . "  data-units=\"rel.Hum.\"\n";
    $output = $output . "  data-min-value=\"0\"\n";
    $output = $output . "  data-start-angle=\"90\"\n";
    $output = $output . "  data-ticks-angle=\"180\"\n";
    $output = $output . "  data-max-value=\"100\"\n";
    $output = $output . "  data-major-ticks=\"0,20,40,60,80,100\"\n";
    $output = $output . "  data-minor-ticks=\"2\"\n";
    $output = $output . "  data-stroke-ticks=\"true\"\n";
    $output = $output . "  data-highlights='[\n";
    $output = $output . "      {\"from\":  0, \"to\":  20, \"color\": \"rgba(200, 50, 50, .75)\"},    \n";
    $output = $output . "      {\"from\": 80, \"to\": 100, \"color\": \"rgba(200, 50, 50, .75)\"}\n";
    $output = $output . "  ]'\n";
    $output = $output . "  data-color-plate=\"#fff\"\n";
    $output = $output . "  data-border-shadow-width=\"0\"\n";
    $output = $output . "  data-borders=\"false\"\n";
    $output = $output . "  data-needle-type=\"arrow\"\n";
    $output = $output . "  data-needle-width=\"4\"\n";
    $output = $output . "  data-needle-circle-size=\"7\"\n";
    $output = $output . "  data-needle-circle-outer=\"true\"\n";
    $output = $output . "  data-needle-circle-inner=\"false\"\n";
    $output = $output . "  data-animation-duration=\"1500\"\n";
    $output = $output . "  data-animation-rule=\"linear\"\n";
    $output = $output . "  data-value-box=\"true\"\n";
    $output = $output . "  data-value=\"" . round($value) . "\"\n";
    $output = $output . "  ></canvas>";
    return $output;
}


function print_canvas_temp($value, $size, $in_out)
{
    $output = "";
    $output = $output . "<canvas data-type=\"linear-gauge\"\n";
    $output = $output . "    data-width=\"" . $size . "\"\n";
    $output = $output . "    data-height=\"" . 2*$size . "\"\n";
    $output = $output . "    data-units=\"°C\"\n";
    if(strcmp($in_out, "inside") == 0) {
        $output = $output . "    data-min-value=\"17\"\n";
        $output = $output . "    data-max-value=\"22\"\n";
        $output = $output . "    data-start-angle=\"90\"\n";
        $output = $output . "    data-ticks-angle=\"180\"\n";
        $output = $output . "    data-major-ticks=\"16,17,18,19,20,21,22,23\"\n";
        $output = $output . "    data-minor-ticks=\"0.5\"\n";
	$output = $output . "    data-highlights='[\n";
	$output = $output . "          {\"from\": 22, \"to\": 26, \"color\": \"rgba(200, 50, 50, .50)\"},   \n";
//	$output = $output . "          {\"from\": 26, \"to\": 30, \"color\": \"rgba(200, 50, 50, .75)\"}    \n";
	$output = $output . "        ]'\n";
    } else {
        $output = $output . "    data-min-value=\"-10\"\n";
        $output = $output . "    data-start-angle=\"90\"\n";
        $output = $output . "    data-ticks-angle=\"180\"\n";
        $output = $output . "    data-max-value=\"40\"\n";
        $output = $output . "    data-major-ticks=\"-10,-5,0,5,10,15,20,25,30,35,40\"\n";
	$output = $output . "    data-minor-ticks=\"5\"\n";
	$output = $output . "    data-highlights='[]'\n";
    }      
    
    $output = $output . "    data-value-box=\"false\"\n";
    $output = $output . "    data-stroke-ticks=\"true\"\n";
    $output = $output . "    data-color-plate=\"#fff\"\n";
    $output = $output . "    data-color-bar-progress=\"#c83232\"\n";
    $output = $output . "    data-color-bar=\"#ffffff\"\n";
    $output = $output . "    data-border-shadow-width=\"0\"\n";
    $output = $output . "    data-borders=\"false\"\n";
    $output = $output . "    data-needle-type=\"arrow\"\n";
    $output = $output . "    data-needle-width=\"4\"\n";
    $output = $output . "    data-needle-circle-size=\"7\"\n";
    $output = $output . "    data-needle-circle-outer=\"true\"\n";
    $output = $output . "    data-needle-circle-inner=\"true\"\n";
    $output = $output . "    data-animation-duration=\"1500\"\n";
    $output = $output . "    data-animation-rule=\"linear\"\n";
    $output = $output . "    data-bar-width=\"10\"\n";
    $output = $output . "    data-value=\"" . $value . "\"\n";
    $output = $output . "></canvas>";
    return $output;
}


function get_data_from_mysql($id, $start, $stop)
{
    $conn = connect_to_mysql();

    $sql = "SELECT * FROM `RawData` WHERE (datetime < $stop) AND (datetime > $start) AND (id = $id)";

    $result = $conn->query($sql);
    if ($result->num_rows > 0) {

        while($row = $result->fetch_assoc()) {
            //echo "id: " . $row["id"]. " - datetime: " . $row["datetime"]. " humidity: " . $row["humidity"]. "<br>";
            $datetime[] = $row["datetime"];
            $humidity[] = $row["humidity"];
            $temperature[] = $row["temperature"];
            $battvoltage[] = $row["voltage"];
        }
    } else {
        echo "0 results<br>\n";
        return NULL;
    }
    $conn->close();
    
    $out = new data_from_db();
    $out->id       = $id;
    $out->humidity = $humidity;
    $out->datetime = $datetime;
    $out->temperature = $temperature;
    $out->battvoltage = $battvoltage;    
    return $out;
}



function generate_graph($my_data_from_db)
{
    $humidity = $my_data_from_db->humidity;
    $temperature = $my_data_from_db->temperature;
    $datetime = $my_data_from_db->datetime;
    $id = $my_data_from_db->id;
    
    $graph = new Graph(1200, 600);       // Create the new graph
    $graph->SetMargin(60, 40, 30, 240);   // Slightly larger than normal margins at the bottom to have room for the x-axis labels
    $graph->SetScale('datlin', 0, 100);  // Fix the Y-scale to go between [0,100] and use date for the x-axis
    if ($id == 0) {
        $max = round(max($temperature), 0, PHP_ROUND_HALF_UP);
        $min = round(min($temperature), 0, PHP_ROUND_HALF_DOWN);
        $graph->SetY2Scale("lin", $min, $max);
    } else {
        $graph->SetY2Scale("lin", 20, 30);
    }    
    $timespan = end($datetime) - $datetime[0];   
    if ($timespan > 24*3600) {
        $graph->title->Set(date("M-d", $datetime[0]+3) . " to " . date("M-d, Y", end($datetime)));
        $graph->xaxis->scale->ticks->Set(3600*24,3600*24);
        $graph->xaxis->scale->SetDateFormat('j.n.');
    } else {
        $graph->title->Set(date("F d, Y", $datetime[0]+3));  //print date as title
        $graph->xaxis->scale->ticks->Set(3600,1800);
    }
        
    
    $graph->title->SetFont(FF_DV_SANSSERIF, FS_NORMAL, 30);
    $graph->xaxis->SetLabelAngle(90);   // Set the angle for the labels to 90 degrees
    $graph->xaxis->SetFont(FF_DV_SANSSERIF, FS_NORMAL, 20);
     
    $line  = new LinePlot($humidity, $datetime);

    $line->SetLegend('Humidity');
    $graph->Add($line);
    
    $line2 = new LinePlot($temperature, $datetime);

    $line2->SetLegend('Temperature');
    $graph->AddY2($line2);

    $line->SetColor("blue");
    $line2->SetColor("orange");
    $graph->legend->Pos(0.4, 0.93);
    $graph->legend->SetFont(FF_DV_SANSSERIF, FS_NORMAL, 25);
    $graph->yaxis->SetColor("blue");
    $graph->yaxis->SetFont(FF_DV_SANSSERIF, FS_NORMAL, 20);
    $graph->y2axis->SetColor("orange");
    $graph->y2axis->SetFont(FF_DV_SANSSERIF, FS_NORMAL, 20);

    $gdImgHandler = $graph->Stroke(_IMG_HANDLER);
    $fileName = "pics/humiditygraph.png";
    $graph->img->Stream($fileName);
}





function generate_battgraph($my_data_from_db)
{
    $datetime = $my_data_from_db->datetime;
    $battvoltage = $my_data_from_db->battvoltage;
    printf("length: %d, %d<br>", count($datetime), count($battvoltage));

    $graph = new Graph(540, 300);       // Create the new graph
    $graph->SetMargin(40, 40, 30, 70);   // Slightly larger than normal margins at the bottom to have room for the x-axis labels
    $graph->SetScale('datlin', 3000, 4400);  // Fix the Y-scale to go between [0,100] and use date for the x-axis

//    $graph->title->Set(date("d.m.", $datetime[0])." - ".date("d.m.", end($datetime)));  //print date as title
//    $graph->xaxis->SetLabelAngle(90);   // Set the angle for the labels to 90 degrees
     
    $line  = new LinePlot($battvoltage, $datetime);

//    $line->SetLegend('Battery Voltage');
    $graph->Add($line);
    

//    $line->SetColor("blue");

//    $graph->legend->Pos(0.5, 0.93);
//    $graph->yaxis->SetColor("blue");


    $gdImgHandler = $graph->Stroke(_IMG_HANDLER);
    $fileName = "pics/battgraph.png";
    $graph->img->Stream($fileName);
}
