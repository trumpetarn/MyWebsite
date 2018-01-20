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
  echo "Temp: ",$temp,"</br>\n";
  echo "Wind: ",$wind,"</br>\n";
  echo "Dir: ",$csv[2],"</br>\n";
  echo "WeatherData was last updated: " . date ("F d Y H:i:s.", filemtime($filename)), "\n";
} else {
  $xml_temp=simplexml_load_file("http://www8.tfe.umu.se/vadertjanst/service1.asmx/Temp") or die("Error: Cannot reach Temp");
  $xml_wind=simplexml_load_file("http://www8.tfe.umu.se/vadertjanst/service1.asmx/Vindhastighet") or die("Error: Cannot reach Wind");
  $xml_dir=simplexml_load_file("http://www8.tfe.umu.se/vadertjanst/service1.asmx/Vindriktning") or die("Error: Cannot reach Dir");
  $temp = number_format(round(floatval($xml_temp),1),1);
  $wind = number_format(round(floatval($xml_wind),1),1);
  echo "Temp: ",$temp,"</br>\n";
  echo "Wind: ",$wind,"</br>\n";
  echo "Dir: ",$xml_dir,"</br>\n";
  
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