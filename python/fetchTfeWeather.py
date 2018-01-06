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




data = fetch_tfe_data(True)
print(get_temp(data,3), '*C')
v,d = get_wind(data,10)
print(v,d)
print(wind_direction(d))
print(get_rain(data))
print(get_pressure(data))
print(get_date(data))
print(get_sun(data, 5))