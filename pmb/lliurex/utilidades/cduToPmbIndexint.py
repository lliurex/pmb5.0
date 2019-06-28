#!/usr/bin/python
# Utility to convert CDU csv file to SQL for PMB
# 2013 - Jaime Munyoz, Vicente Balaguer

import sys

def escaperCharacter(msg):
	msg=msg.replace('\'','\'\'')
	return msg

def remplaceCharacter(msg):
	lista=['\'','.','\\','/','-','(',')']
	for chars in lista:
		msg=msg.replace(chars,' ')
	return msg
		
def checkData(data):
	if len(data.strip())<>0:
		data=data.rstrip('\n')
		pos=data.index('\t')
		if pos<>0:
			index = data[0:pos]
			data = data[pos+1:]
	return index, data

reload(sys)
sys.setdefaultencoding("utf-8")

if len(sys.argv) < 2:
	print "usage: cduToPmbIndexint.py cduFile.csv"
	sys.exit(0)

ins = open( sys.argv[1], "r" )

sql="INSERT INTO indexint(indexint_id,indexint_name,indexint_comment,index_indexint,num_pclass) VALUES "
for line in ins:
	index,data=checkData(line)
	sql=sql + "(NULL,\'"+escaperCharacter(index)+"\',\'"+data+"\',\'"+remplaceCharacter(index)+"\',\'1\'),\n"
sql=sql.rstrip('\n')
sql=sql.rstrip(',')+';'
print sql

