<?php

	include('session.php');

	$response = array();

	if($_SERVER["REQUEST_METHOD"] == "POST") {
		$response['error'] = 'false';
		exec("nohup php dumpRelationsAsXMLs.php " . $_SESSION['login_user'] . " &");
	}
	else{
		$response['error'] = 'true';
		$response['message'] = 'Invalid Request';
	}

	echo json_encode($response);

?>