<?php

    include("session.php");
    session_start();

    header('Content-Type: application/json');

    $response = array();

    if($_SERVER["REQUEST_METHOD"] == "POST") {
        $user_name = mysqli_real_escape_string($db, $_POST['user_name']);
        $doc_name = mysqli_real_escape_string($db, $_POST['doc_name']);
        $relation_name = mysqli_real_escape_string($db, $_POST['relation_name']);
        $connective = mysqli_real_escape_string($db, $_POST['connective']);
        $arg1 = mysqli_real_escape_string($db, $_POST['arg1']);
        $arg2 = mysqli_real_escape_string($db, $_POST['arg2']);
        $sense = mysqli_real_escape_string($db, $_POST['sense']);
        $edit_relation_id = mysqli_real_escape_string($db, $_POST['edit_relation_id']);

        if($edit_relation_id == ''){
            // add a new relation : annotation and relation table
            $sql = "INSERT INTO relation (relation_name, connective_span, arg1_span, arg2_span, sense) VALUES ('$relation_name', '$connective', '$arg1', '$arg2', '$sense')";
            $response['relation_sql'] = $sql;
            if ($db->query($sql) === TRUE) {
                $add_relation_id = mysqli_insert_id($db);
                $response['relation_id'] = $add_relation_id;
                $response['relation_message'] = 'success';
                $sql = "INSERT INTO annotation (username, document_name, relation_id) VALUES ('$user_name', '$doc_name', '$add_relation_id')";
                $response['annotation_sql'] = $sql;
                if ($db->query($sql) === TRUE) {
                    $response['annotation_message'] = 'success';
                }
                else{
                    $response['annotation_message'] = 'error';
                }
            } 
            else {
                $response['relation_message'] = 'error';
            }
        }
        else{
            // edit the same relation : relation table
            $response['edit'] = $edit_relation_id;
            $sql = "UPDATE relation SET relation_name = '$relation_name', connective_span = '$connective', arg1_span = '$arg1', arg2_span = '$arg2', sense = '$sense' WHERE relation_id = $edit_relation_id ";
            $response['edit_relation_sql'] = $sql;
            if ($db->query($sql) === TRUE) {
                $response['edit_relation_message'] = 'success';
            } 
            else {
                $response['edit_relation_message'] = 'error';
            }
        }
    }

    else{
        $response['error'] = "WTF";
    }

    echo json_encode($response);

?>