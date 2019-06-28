#!/usr/bin/python

# Utility to convert PMB XML language files to standard po files format
# 2013 - Jaime Munyoz, Vicente Balaguer

# Construct a po base structure with the content of all files given as param 
# and include only translations given in the last param.
import xml.etree.ElementTree as ET
import sys
reload(sys)
sys.setdefaultencoding("utf-8")

def escaperCharacter(msg):
	msg=msg.replace('\\','')
	msg=msg.replace('"','\\\"')
	#msg=msg.replace('\'','\\\'')
	return msg
	
def search(msg,lstMsg):
	while msg in lstMsg:
		msg = msg+" "
	return msg

class langs:
	def __init__(self, original, translated):
		self.o = original
		self.t = translated

if len(sys.argv) < 3:
	print "usage: pmbXMLtoPo.py XML_source XML_translated1 XML_translated2 ..."
	sys.exit(0)

traduccions = {}
lstMsg = {}
tree = ET.parse(sys.argv[1]) 
root = tree.getroot()
for entry in root:
	cod=entry.attrib["code"] 
	msg=escaperCharacter(str(entry.text))
	msg=search(msg,lstMsg)
	lstMsg[msg]=''
	lang1=langs(msg,'')
	traduccions [cod]= lang1

for i in range(2, len(sys.argv)):
	tree = ET.parse(sys.argv[i]) 
	root = tree.getroot()
	for entry in root:
		cod=entry.attrib["code"] 
		val2= escaperCharacter(str(entry.text))
		if cod in traduccions:
			if val2<>traduccions [cod].o:
				traduccions [cod].t=val2
		else:
			lang1=langs(val2,val2)
			traduccions [cod]= lang1

print "msgid \"\""
print "msgstr \"\""
print "\"Content-Type: text/plain; charset=UTF-8\\n\""
print "\"Content-Transfer-Encoding: 8bit\\n\"\n"

for codi,val1 in sorted(traduccions.items()):
	print "#: "+codi
	
	if val1.t<>"":
		print "#, fuzzy"
		print "msgid \""+val1.o+"\""
		print "msgstr \""+val1.t+"\""
	else:
		print "msgid \""+val1.o+"\""
		print "msgstr \"\""
	print	
	
