<?php
	include 'session.php';
	$user_check = $_SESSION['login_user'];
	if($user_check != 'admin'){
		header("Location: /public/gujarati_connective/logout.php");
	}

	$response = array();

    if($_SERVER["REQUEST_METHOD"] == "POST") {
    	$doc_name = $_POST['doc_name'];
    	$doc_text = $_POST['doc_text'];

    	$doc_path = '/Users/devanshujain/Documents/Annotation_Interface/htdocs/data/raw/' . $doc_name;

    	if(file_exists($doc_path)){
    		$response['error'] = 'true';
    		$response['message'] = 'File with this name already exists';
    	}
    	else{
    		$myfile = fopen($doc_path, "w") or die("Unable to open file!");
	    	fwrite($myfile, $doc_text);
	    	fclose($myfile);
	    	$response['error'] = 'false';
	    	$response['file_path'] = $doc_path;

	    	$doc_name_sql = mysqli_real_escape_string($db, $doc_name);
	    	$doc_path_sql = mysqli_real_escape_string($db, $doc_path);

	    	$sql = "INSERT INTO document (document_name, document_path) VALUES ('$doc_name_sql', '$doc_path_sql')";
            $response['sql'] = $sql;
            if ($db->query($sql) === TRUE) {
            	$response['message'] = 'Stuff done...';
            }
            else{
            	$response['error'] = 'true';
    			$response['message'] = 'Uploaded the document but could not add document to database.. deleting the uploaded document';
    			if(unlink($doc_path)){
                    // successfully deleted the uploaded document
                }
                else{
                    $response['error'] = 'true';
                    $response['message'] = 'Could not delete the uploaded file';
                }
            }
    	}
    }
    else{
    	$response['error'] = 'true';
    	$response['message'] = 'Invalid request';
    }

    echo json_encode($response);
?>