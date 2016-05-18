<?php

    include("session.php");
    session_start();

    header('Content-Type: application/json');

    $response = array();

    if($_SERVER["REQUEST_METHOD"] == "POST") {
        $user_name = mysqli_real_escape_string($db, $_POST['user_name']);
        $doc_name = mysqli_real_escape_string($db, $_POST['doc_name']);
        $delete_relation_id = mysqli_real_escape_string($db, $_POST['relation_id']);

        $sql = "DELETE FROM annotation WHERE username = '$user_name' AND document_name = '$doc_name' AND relation_id = $delete_relation_id";
        $response['annotation_sql'] = $sql;   

        if ($db->query($sql) === TRUE) {
            $response['relation_message'] = 'success';
            $sql = "DELETE FROM relation WHERE relation_id=$delete_relation_id";
            $response['relation_sql'] = $sql;
            if ($db->query($sql) === TRUE) {
                $response['relation_message'] = 'success';
            }
            else{
                $response['relation_message'] = 'error';
            }
        } 
        else {
            $response['annotation_message'] = 'error';
        }
    }

    else{
        $response['error'] = "WTF";
    }

    echo json_encode($response);

?>