<?php

    include("session.php");
    $user_check = $_SESSION['login_user'];
    if($user_check != 'admin'){
        header("Location: /public/gujarati_connective/logout.php");
    }
    
    header('Content-Type: application/json');

    $response = array();

    if($_SERVER["REQUEST_METHOD"] == "POST") {
        $doc_name = mysqli_real_escape_string($db, $_POST['doc_name']);

        $annotations = mysqli_query($db,"SELECT relation_id FROM annotation WHERE document_name = '$doc_name'");

        if ($annotations) {
            $sql = "DELETE FROM annotation WHERE document_name = '$doc_name'";

            if ($db->query($sql) === TRUE) {
                while ($row = $annotations->fetch_assoc()){
                    $relation_id = $row['relation_id'];
                    $sql = "DELETE FROM relation WHERE relation_id = '$relation_id'";
                    if ($db->query($sql) === TRUE) {
                        // successful execution of query
                    }
                    else{
                        $response['error'] = 'true deleting from relation';
                        echo json_encode($response);
                        exit();
                    }
                }

                $documents = mysqli_query($db,"SELECT document_path FROM document WHERE document_name = '$doc_name'");
                while ($row = $documents->fetch_assoc()){
                    $doc_path = $row['document_path'];
                    if(unlink($doc_path)){
                        $sql = "DELETE FROM document WHERE document_name = '$doc_name' AND document_path = '$doc_path'";
                        if ($db->query($sql) === TRUE) {
                            $response['error'] = 'false';
                        }
                        else{
                            $response['error'] = 'true deleting from document';
                            echo json_encode($response);
                            exit();
                        }
                    }
                    else{
                        $response['error'] = 'true deleting the actual document';
                        echo json_encode($response);
                        exit();
                    }
                }
            } 
            else {
                $response['error'] = 'true deleting from annotation';
                echo json_encode($response);
                exit();
            }
        } 
        else {
            $response['error'] = 'true selecting from annotation';
            echo json_encode($response);
            exit();
        }
    }

    echo json_encode($response);

?>