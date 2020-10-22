import urllib.request
import xml.etree.ElementTree as xml

"""
        <datum>2018-01-04T22:40:00+01:00</datum>
        <tempmed>-4.94</tempmed>
        <fukt>99</fukt>
        <tryck>997</tryck>
        <vindh>4.825</vindh>
        <vindr>23</vindr>
        <vindmax>0</vindmax>
        <regn>0</regn>
        <sol>0.025</sol>
        """


def fetch_tfe_data(debug=False):
	if debug:
		print("DEBUG")
		tree = xml.parse('GetData.xml')
		root = tree.getroot()
		return root
	data = urllib.request.urlopen("http://www8.tfe.umu.se/vadertjanst/service1.asmx/GetData").read()	
	return xml.fromstring(data)

def get_temp(data,elem=1):
	temp = data.findall('.//tempmed')
	if elem==1:
		return float(temp[0].text)
	temps = []
	for i in range(0,elem):
		temps.append(float(temp[i].text))
	return temps

def get_wind(data,elem=1):
	hast = data.findall('.//vindh')
	rikt = data.findall('.//vindr')
	if elem==1:
		return float(hast[0].text), int(rikt[0].text)
	h = []
	r = []
	for i in range(0,elem):
		h.append(float(hast[i].text))
		r.append(int(rikt[i].text))
	return h,r

def wind_direction(deg):
	DIR = ["NNO","NO","ONO","O","OSO","SO","SSO","S","SSV","SV","VSV","V","VNV","NV","NNV","N"]
	Sector = (360//len(DIR))
	if not isinstance(deg,list):
		Index = (deg-Sector//2)//(Sector)
		return DIR[Index]
	dirs = []
	for d in deg:
		Index = (d-Sector//2)//(Sector)
		dirs.append(DIR[Index])
	return dirs

def get_date(data,elem=1):
	date = data.findall('.//datum')
	if elem==1:
		return date[0].text
	dates = []
	for i in range(0,elem):
		dates.append(date[i].text)
	return dates

def get_moisture(data,elem=1):
	moist = data.findall('.//fukt')
	if elem==1:
		return float(moist[0].text)
	mois = []
	for i in range(0,elem):
		mois.append(float(moist[i].text))
	return mois

def get_pressure(data,elem=1):
	pr = data.findall('.//tryck')
	if elem==1:
		return int(pr[0].text)
	press = []
	for i in range(0,elem):
		press.append(int(pr[i].text))
	return press

def get_rain(data,elem=1):
	rain = data.findall('.//regn')
	if elem==1:
		return float(rain[0].text)
	rains = []
	for i in range(0,elem):
		rains.append(float(rain[i].text))
	return rains

def get_sun(data,elem=1):
	sun = data.findall('.//sol')
	if elem==1:
		return float(sun[0].text)
	suns = []
	for i in range(0,elem):
		suns.append(float(sun[i].text))
	return suns

def generate_html(filename="../html/index.html", debug=False):
	file = open(filename,"w")
	data = fetch_tfe_data(debug)
	vel,rikt = get_wind(data)
	temp = get_temp(data)
	html = """
<!DOCTYPE html>
<!-- 
This document was created by a python script
Data is from TFE @ Umea University: http://www8.tfe.umu.se/weather-new/
-->
<html>
<head>
<meta charset="UTF-8">
<title>Foxberry weather</title>
  <meta charset="UTF-8">
  <style>
  :root {{
  --wind-dir: rotate({}deg);
	}}
	</style>
  <link rel="stylesheet" href="css/app.css">
</head>
<body>
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
	  <p class="compass__wind">{} m/s</p>
  </div>
  <svg class="compass__pointer" viewBox="0 0 130 130" version="1.1" xmlns="http://www.w3.org/2000/svg">
    <polyline points="60,60  70,60  65,15" fill="#b60000"/>
    <polyline points="60,70  70,70  65,115" fill="white"/>
    <circle cx="65" cy="65" r="7" stroke="#b60000" stroke-width="7" fill="none" />
  </svg>
	<p class="compass__temp">{}°C</p>
</div>
</body>
</html> 
""".format(rikt+180, round(vel,1), round(temp,1))
	if debug:
		print(html)
	file.write(html)
	file.close()

generate_html()
