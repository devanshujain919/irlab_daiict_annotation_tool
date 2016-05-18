<?php
      include('session.php');

      $response = array();

      if($_SERVER["REQUEST_METHOD"] == "GET"){
            $username = $_GET["username"];
            $doc_id = $_GET["doc_id"];
            $relations = mysqli_query($db,"select relation_id from annotation where username = '$username' and document_name = '$doc_id' ");
            while ($row = $relations->fetch_assoc()){
                  $relation_id = $row['relation_id'];
                  $rel = mysqli_query($db,"select * from relation where relation_id = '$relation_id' ");
                  $rel_row = $rel->fetch_assoc();

                  $rel_data = array();
                  $rel_data['relation_name'] = $rel_row['relation_name'];
                  $rel_data['connective_span'] = $rel_row['connective_span'];
                  $rel_data['arg1_span'] = $rel_row['arg1_span'];
                  $rel_data['arg2_span'] = $rel_row['arg2_span'];
                  $rel_data['sense'] = $rel_row['sense'];
                  $response[$rel_row['relation_id']] = $rel_data;
            }
            echo json_encode($response);
      }
      else{
            $responseVar = array(
                  'error'=>'not the correct request',
            );
            echo json_encode($responseVar);
      }
?>