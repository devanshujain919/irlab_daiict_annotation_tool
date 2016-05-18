<?php

    include("session.php");
    session_start();

    header('Content-Type: application/text');

    if($_SERVER["REQUEST_METHOD"] == "GET") {
    	$doc_path = $_GET['doc_path'];
    	if( file_exists( $doc_path ) ){
    		echo readfile( $doc_path );
    	}
    	else{
    		echo $doc_path;
    	}
   	}

   	else{
   		echo "ERROR: invalid request.";
   	}
?>