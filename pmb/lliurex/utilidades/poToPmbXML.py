#!/usr/bin/python

# Utility to convert po files to PMB XML language files format
# 2013 - Jaime Munyoz, Vicente Balaguer

# Source po file before each translation must have a comment with the code required by PMB

import sys
reload(sys)
sys.setdefaultencoding("utf-8")

def checkData(data):
	type=0
	value=''
	if len(data.strip())<>0:
		data=data.rstrip('\n')
		if data.startswith( '#: '):
			type = 1
			value= data [3:]
		if data.startswith ( 'msgstr'):
			type = 2
			value= data [8: -1]
			if value.count('<') > 0:
				value= '<![CDATA['+value+']]>'
	return (type,value)

reload(sys)
sys.setdefaultencoding("utf-8")

if len(sys.argv) < 2:
	print "usage: poToPmbXML.py poFile.po"
	sys.exit(0)

ins = open( sys.argv[1], "r" )

print "<?xml version=\"1.0\" encoding=\"utf-8\"?>"
print "<!DOCTYPE XMLlist SYSTEM \"../XMLlist.dtd\" [<!ENTITY nbsp \"&amp;nbsp;\">]>"+"\n"
print "<XMLlist>"
code=''
data = ins.readline()
while data:
	#transform multiline po translation in a single line with <br />
	if data.startswith ( 'msgstr'):
		value= data [7: -1]
		if len(value) == 2:
			value='"'
			mData = True
			while mData:
				data = ins.readline()
				if data.startswith( '"' ):
					value = value +''+ data [1: -2]
				else:
					mData = False
			value = value + '"'
		data= 'msgstr ' + value

	type, value=checkData(data)
	if type == 1:
		code = value
	else :
		if type == 2 and code !='' :
			print "<entry code=\""+code+"\">"+value+"</entry>"
	data=ins.readline()
print "</XMLlist>"
