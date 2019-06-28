#!/usr/bin/python
# Utility to convert categories cvs file to SQL for PMB
# 2013 - Jaime Munyoz, Vicente Balaguer

import sys

def escaperCharacter(msg):
	msg=msg.replace('\'','\'\'')
	return msg

reload(sys)
sys.setdefaultencoding("utf-8")

if len(sys.argv) < 2:
	print "usage: catergoriasToPmbCategories.py materias.csv \[num_first_element\]"
	sys.exit(0)

if len(sys.argv) == 3:
	i= int(sys.argv[2])
else:
	i=4

ins = open( sys.argv[1], "r" )

sql="INSERT INTO categories (num_thesaurus, num_noeud, langue, libelle_categorie, note_application, comment_public, comment_voir, index_categorie, path_word_categ, index_path_word_categ) VALUES "
for line in ins:
	line=line.rstrip('\n')
	sql = sql + "(1,"+ str(i) +", \'es_ES\',\'" + escaperCharacter(line) + "\', NULL, NULL, NULL,  \'descriptores no clasificados\' ,NULL, NULL),\n"
	i+=1
sql=sql.rstrip('\n')
sql=sql.rstrip(',')+';'
print sql

