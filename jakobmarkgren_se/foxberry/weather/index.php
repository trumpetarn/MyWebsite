
<!DOCTYPE html>
<html>
<head>
<?php
echo "<!-- \n";
$filename = "weatherdata.txt";
// Check if we fetchd data in the last 20 minutes
if (file_exists($filename) and ((time()-filemtime($filename)) < (20*60))) {
  $myfile = fopen($filename, "r") or die("Unable to open file!");
  $line = fgets($myfile);
  fclose($myfile);
  $csv = str_getcsv($line);
  $temp = number_format(round(floatval($csv[0]),1),1);
  $wind = number_format(round(floatval($csv[1]),1),1);
  $dir = intval($csv[2])+180;
  echo "Temp: ",$temp,"</br>\n";
  echo "Wind: ",$wind,"</br>\n";
  echo "Dir: ",$dir,"</br>\n";
  echo "WeatherData was last updated: " . date ("F d Y H:i:s.", filemtime($filename)), "\n";
} else {
  $xml_temp=simplexml_load_file("http://www8.tfe.umu.se/vadertjanst/service1.asmx/Temp") or die("Error: Cannot reach Temp");
  $xml_wind=simplexml_load_file("http://www8.tfe.umu.se/vadertjanst/service1.asmx/Vindhastighet") or die("Error: Cannot reach Wind");
  $xml_dir=simplexml_load_file("http://www8.tfe.umu.se/vadertjanst/service1.asmx/Vindriktning") or die("Error: Cannot reach Dir");
  $temp = number_format(round(floatval($xml_temp),1),1);
  $wind = number_format(round(floatval($xml_wind),1),1);
  $dir = intval($xml_dir)+180;
  echo "Temp: ",$temp,"</br>\n";
  echo "Wind: ",$wind,"</br>\n";
  echo "Dir: ",$dir,"</br>\n";
  
  $del = ',';
  $myfile = fopen($filename, "w") or die("Unable to open file!");
  fwrite($myfile, $xml_temp);
  fwrite($myfile, $del);
  fwrite($myfile, $xml_wind);
  fwrite($myfile, $del);
  fwrite($myfile, $xml_dir);
  fwrite($myfile, $del);
  fclose($myfile);
  echo "WeatherData was last updated: " . date ("F d Y H:i:s.", time($filename)), "\n";
}
echo "-->";
?>
<meta charset="UTF-8">
<title>Foxberry weather</title>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <script type="text/javascript" src="jquery.min.js"></script>
  <script type="text/javascript" src="jquery-ui.min.js"></script>
  <style>
  #draggable { width: 10px; height: 10px; background-color: rgba(255, 0, 0, 0); border-style: hidden; position: absolute; top: 10px; left: 10px;}
  #draggable p { cursor: move; font-size: 12px;background-color: rgba(255, 0, 0, 0); }
  </style>
  <link rel="stylesheet" href="css/app.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css">
  <script type="text/javascript">
    $( function() {
    $( "#draggable" ).draggable({ handle: "p" });
    $( "div, p" ).disableSelection();
  } );
  </script>
  <style>
  :root {
  --wind-dir: rotate(476deg);
  }
  </style>
<!-- Datetime start-->
<script type="text/javascript">
  // Returns the ISO week of the date.
  Date.prototype.getWeek = function() {
    var date = new Date(this.getTime());
    date.setHours(0, 0, 0, 0);
    // Thursday in current week decides the year.
    date.setDate(date.getDate() + 3 - (date.getDay() + 6) % 7);
    // January 4 is always in week 1.
    var week1 = new Date(date.getFullYear(), 0, 4);
    // Adjust to Thursday in week 1 and count number of weeks from date to week1.
    return 1 + Math.round(((date.getTime() - week1.getTime()) / 86400000
                          - 3 + (week1.getDay() + 6) % 7) / 7);
  }
  function addZero(n){
    if (n<10)
      return "0"+n;
    else
      return n;
  }
  function doDate()
  {
    var days = new Array("Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday");
    var months = new Array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
    var str = "";
    var clockStr = "";
    var weekStr = "";
    var d = new Date();

    str += days[d.getDay()] + " " + d.getDate() + " " + months[d.getMonth()];
    clockStr += addZero(d.getHours()) + ":" + addZero(d.getMinutes()) + ":" + addZero(d.getSeconds());
    weekStr += "Week " + addZero(d.getWeek());
    document.getElementById("timeDay").innerHTML = str;
    document.getElementById("timeWeek").innerHTML = weekStr;
    document.getElementById("timeClock").innerHTML = clockStr;
  }
  setInterval(doDate, 1000);
</script>
<!-- Datetime end -->
</head>
<body>
<div id="draggable" class="ui-widget-content">
  <p class="ui-widget-header">x</p>
  <div class="yr_script">
  <script src="https://www.yr.no/place/Sweden/Västerbotten/Umeå/external_box_three_days.js"></script><noscript><a href="https://www.yr.no/place/Sweden/Västerbotten/Umeå/">yr.no: Forecast for Umeå</a></noscript>
</div>
</div>
<div class="compass">
  <div id="rose" class="compass__rose">

	  <svg class="compass__rose__dial" viewBox="0 0 130 130" version="1.1" xmlns="http://www.w3.org/2000/svg">

		  <circle cx="65" cy="65" r="56" stroke="white" stroke-width="1" fill="none" />
		  <polyline points="63,9  67,9  65,13" fill="white"/>
		  <polyline points="121,63  121,67  119,65" fill="white"/>
		  <polyline points="63,121  67,121  65,119" fill="white"/>
		  <polyline points="9,63  9,67  11,65" fill="white"/>

		  <text x="65" y="20" font-size="10" text-anchor="middle" fill="white">N</text>
		  <text x="114" y="68" font-size="10" text-anchor="middle" fill="white">E</text>
		  <text x="65" y="118" font-size="10" text-anchor="middle" fill="white">S</text>
		  <text x="17" y="68" font-size="10" text-anchor="middle" fill="white">W</text>

	  </svg>
	  <p class="compass__wind"><?php echo $wind;?> m/s</p>
  </div>
  <svg class="compass__pointer" viewBox="0 0 130 130" version="1.1" xmlns="http://www.w3.org/2000/svg">
    <polyline points="60,60  70,60  65,15" fill="#b60000"/>
    <polyline points="60,70  70,70  65,115" fill="white"/>
    <circle cx="65" cy="65" r="7" stroke="#b60000" stroke-width="7" fill="none" />
  </svg>
	<p class="compass__temp"><?php echo $temp;?>°C</p>
  <div class="time__clock" id="timeClock"></div>
  <div class="time__date" id="timeDay"></div>
  <div class="time__date2" id="timeWeek"></div>
</div>
</body>
</html> 



