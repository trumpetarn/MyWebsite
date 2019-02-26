
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
  <style>
  :root {
  --wind-dir: rotate(<?php echo $dir,"deg";?>);
	}
	</style>
  <link rel="stylesheet" href="css/app.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css">
  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
  <script type="text/javascript">
    $( function() {
      $( "#draggable" ).draggable();
    } );
  </script>
</head>
<body>
<div id="draggable" class="yr_script">
  <script src="https://www.yr.no/place/Sweden/Västerbotten/Umeå/external_box_three_days.js"></script><noscript><a href="https://www.yr.no/place/Sweden/Västerbotten/Umeå/">yr.no: Forecast for Umeå</a></noscript>
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
</div>
</body>
</html> 



