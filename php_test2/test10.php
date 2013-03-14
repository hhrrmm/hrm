<?PHP

$file_handle = fopen("data/lastUpdate.txt", "rb");

while (!feof($file_handle) ) {
	$line_of_text = fgets($file_handle);	
	print $line_of_text;
}

fclose($file_handle);

?>