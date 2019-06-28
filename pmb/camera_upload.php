<?php
// +-------------------------------------------------+
// © 2002-2004 PMB Services / www.sigb.net pmb@sigb.net et contributeurs (voir www.sigb.net)
// +-------------------------------------------------+
// $Id: camera_upload.php,v 1.1 2016-08-19 12:36:41 ngantier Exp $

$upload_filename = $_POST['upload_filename'];
$rawData = $_POST['imgBase64'];

$filteredData = explode(',', $rawData);
$unencoded = base64_decode($filteredData[1]);

$fp = fopen($upload_filename, 'w');
fwrite($fp, $unencoded);
fclose($fp);

