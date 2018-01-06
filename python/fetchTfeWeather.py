import urllib.request
import xml.etree.ElementTree as xml

def fetch_tfe_data(debug=False):
	if debug:
		print("DEBUG")
		tree = xml.parse('GetData.xml')
		root = tree.getroot()
		return root
	data = urllib.request.urlopen("http://www8.tfe.umu.se/vadertjanst/service1.asmx/GetData").read()	
	return xml.fromstring(data)

def get_temp(data):
	temp = data.find('.//tempmed')
	return float(temp.text)

def get_wind(data):
	hast = data.find('.//vindh')
	rikt = data.find('.//vindr')
	print(data.find('.//vindmax').text)
	return float(hast.text),int(rikt.text)

def wind_direction(deg):
	DIR = ["NNO","NO","ONO","O","OSO","SO","SSO","S","SSV","SV","VSV","V","VNV","NV","NNV","N"]
	N = len(DIR)
	V = (360//N)//2
	return DIR[(deg-V)//(2*V)]

data = fetch_tfe_data(True)
print(get_temp(data), '*C')
v,d = get_wind(data)
print(v,d)
print(wind_direction(d))
