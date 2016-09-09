<?php

	$CSV_FILE = "count.csv"; # csv file to use
	$host_url = "http://example.org/"; # the trailing / is important
	$file_download; # will hold the full url to the file

	# 
	if (isset($_GET['file']) && !empty($_GET['file']) && strpos($_GET['file'], "/files/") !== false && strpos($_GET['song'], "../") !== true strpos($_GET['song'], "%2E%2E%2F") !== true) {
		$filename_url_encoded = $_GET['file'];
		$filename_url_decoded = urldecode($filename_url_encoded);
		$file_download = $host_url . $filename_url_encoded;

		header('Content-Description: File Transfer');
	    header('Content-Type: application/octet-stream');
	    header('Content-Disposition: attachment; filename="'.basename($filename_url_encoded).'"');
	    header('Content-Length: ' . filesize(ltrim($filename_url_encoded, "/")));
	    header('Expires: 0');
	    header('Cache-Control: must-revalidate');
	    header('Pragma: public');

	    echo file_get_contents(ltrim($filename_url_encoded, "/"));
	    stuff($CSV_FILE, $file_download);
	} else {
		$filename_url_encoded = $_GET['file'];
		$filename_url_decoded = urldecode($filename_url_encoded);
		$file_download = $host_url . $filename_url_decoded;
		echo "error downloading $file_download.<br><br>\n\n";
	}


	function stuff($fileString, $file_download) {

		$fileRegistered = false;
		$handle = fopen($fileString, "r");

		if ($handle) {
			$data = csvToArray($handle);
			foreach ($data as $key => $value) {
				if (strcmp($value[0], $file_download) == 0) {
					$fileRegistered = true;
					$data[$key][1]++;
					break;
				}
			}

			fclose($handle);
			$handle = fopen($fileString, "w");

			if (!$fileRegistered) {
				array_push($data, array($file_download, 1));
			}

			foreach ($data as $key => $value) {
				fputcsv($handle, $value);
			}

			fclose($handle);
		} else {
			echo "error opening $handle";
		}
	}

	function csvToArray($handle) {
	    while (($line = fgets($handle)) !== false) {
		    $data[] = str_getcsv($line);
		}
	    return $data;
	}
?> 
