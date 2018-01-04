import urllib.request
import xml.etree.ElementTree as xml

def fetch_tfe_data(debug=False):
	if debug:
		print("DEBUG")
		tree = xml.parse('GetData.xml')
		root = tree.getroot()
		return root
	data = urllib.request.urlopen("http://www8.tfe.umu.se/vadertjanst/service1.asmx/GetData").read()	
	return data

def get_temp(data):
	temp = data.find('.//tempmed')
	return float(temp.text)

def get_wind(data):
	temp = data.find('.//tempmed')
	return float(temp.text)

data = fetch_tfe_data()
print(get_temp(data), '*C')
