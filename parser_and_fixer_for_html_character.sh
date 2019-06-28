#!/bin/sh

if [ -f $1 ]; then
	sed -i "s%&aacute;%á%g" $1
	sed -i "s%&eacute;%é%g" $1
	sed -i "s%&iacute;%í%g" $1
	sed -i "s%&oacute;%ó%g" $1
	sed -i "s%&uacute;%ú%g" $1
	sed -i "s%&agrave;%à%g" $1
	sed -i "s%&egrave;%è%g" $1
	sed -i "s%&igrave;%ì%g" $1
	sed -i "s%&ograve;%ò%g" $1
	sed -i "s%&ugrave;%ù%g" $1
	sed -i "s%&apos;%\'%g" $1
	sed -i "s%&middot;%·%g" $1
	sed -i "s%&iuml;%ï%g" $1
	sed -i "s%&uuml;%ü%g" $1
else
	echo "Not exists $1"
fi

