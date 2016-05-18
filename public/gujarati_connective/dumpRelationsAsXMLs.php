<?php

	set_time_limit(0);
	ignore_user_abort(1);

	include('config.php');

	$user_sql = '';

	if(sizeof($argv) == 2){
		// user provided
		$user_sql = "SELECT username FROM users WHERE username = '" . $argv[1] . "'";
	}
	else if(sizeof($argv) == 1){
		// do it for all users
		$user_sql = "SELECT username FROM users";
	}
	else{
		echo "Wrong number of arguments\n";
		exit;
	}

    $users = mysqli_query($db, $user_sql);
    $users_list = array();

    if(mysqli_num_rows($users) <= 0){
		echo "no users found!!!";
		exit;
	}

    $document_sql = "SELECT document_name FROM document";
    $documents = mysqli_query($db, $document_sql);
    $documents_list = array();

    if(mysqli_num_rows($documents) <= 0){
		echo "no documents found!!!";
		exit;
	}

    while ($user_row = $users->fetch_assoc()){
    	array_push($users_list, $user_row);
    }

    while ($doc_row = $documents->fetch_assoc()){
    	array_push($documents_list, $doc_row);
    }

	foreach ($users_list as $user_row){
		foreach ($documents_list as $doc_row){
			$annotation_sql = "SELECT relation_id FROM annotation WHERE username = '" . $user_row['username'] . "' AND document_name = '" . $doc_row['document_name'] . "'";
			echo $annotation_sql . "\n";
			$annotations = mysqli_query($db, $annotation_sql);
			
			$relations_list = array();
			$doc_dir_path = "/Users/devanshujain/Documents/Annotation_Interface/annotations/" . $user_row['username'] . "/";
			if(!is_dir($doc_dir_path)){
			    mkdir($doc_dir_path, 0777, true);
			    chmod($doc_dir_path, 0777);
			}
			$doc_path = $doc_dir_path . $doc_row['document_name'] . ".xml";

			if(!$annotations || mysqli_num_rows($annotations) <= 0){
				echo 'no annotations found!!! ' . $user_row['username'] . ' ' . $doc_row['document_name'] . "\n";
				continue;
			}
			
			while ($annotation_row = $annotations->fetch_assoc()){
				$relation_sql = "SELECT * FROM relation WHERE relation_id = " . $annotation_row['relation_id'];
				$relations = mysqli_query($db, $relation_sql);
				if(!$relations || mysqli_num_rows($relations) != 1){
					echo 'no relation with this relation id' . $user_row['username'] . ' ' . $doc_row['document_name'] . $annotation_row['relation_id'] . "\n";
					continue;
				}
				else{
					$relation = array();
					$relation_row = $relations->fetch_assoc();
					$relation['relation_id'] = $relation_row['relation_id'];
					$relation['relation_name'] = $relation_row['relation_name'];
					$relation['connective_span'] = $relation_row['connective_span'];
					$relation['arg1_span'] = $relation_row['arg1_span'];
					$relation['arg2_span'] = $relation_row['arg2_span'];
					$relation['sense'] = $relation_row['sense'];
					array_push($relations_list, $relation);
				}
			}

			$doc = new DOMDocument();
  			$doc->formatOutput = true;
  			
  			$allRelations = $doc->createElement( "relations" );
  			
  			foreach ($relations_list as $relation) {
  				$rel = $doc->createElement( "relation" );
  				$rel->setAttribute('id', $relation['relation_id']);

  				$rel_name = $doc->createElement( "relation_name", $relation['relation_name'] );
  				$rel->appendChild($rel_name);

  				$connective_span_element = $doc->createElement( "connective_span" );
  				$connective_span = explode(";", $relation['connective_span']);
  				foreach($connective_span as $span){
  					$span_element = $doc->createElement( "span" );
  					$start = $doc->createElement("start", explode(":", $span)[0]);
  					$end = $doc->createElement("end", explode(":", $span)[1]);
  					$span_element->appendChild($start);
  					$span_element->appendChild($end);
  					$connective_span_element->appendChild($span_element);
  				}
  				$rel->appendChild($connective_span_element);

  				$arg1_span_element = $doc->createElement( "arg1_span" );
  				$arg1_span = explode(";", $relation['arg1_span']);
  				foreach($arg1_span as $span){
  					$span_element = $doc->createElement( "span" );
  					$start = $doc->createElement("start", explode(":", $span)[0]);
  					$end = $doc->createElement("end", explode(":", $span)[1]);
  					$span_element->appendChild($start);
  					$span_element->appendChild($end);
  					$arg1_span_element->appendChild($span_element);
  				}
  				$rel->appendChild($arg1_span_element);

  				$arg2_span_element = $doc->createElement( "arg2_span" );
  				$arg2_span = explode(";", $relation['arg2_span']);
  				foreach($arg2_span as $span){
  					$span_element = $doc->createElement( "span" );
  					$start = $doc->createElement("start", explode(":", $span)[0]);
  					$end = $doc->createElement("end", explode(":", $span)[1]);
  					$span_element->appendChild($start);
  					$span_element->appendChild($end);
  					$arg2_span_element->appendChild($span_element);
  				}
  				$rel->appendChild($arg2_span_element);

  				$rel_sense = $doc->createElement( "relation_sense", $relation['sense'] );
  				$rel->appendChild($rel_sense);

  				$allRelations->appendChild( $rel );
  			}

  			$doc->appendChild( $allRelations );

  			echo $doc_path;

  			if(file_exists($doc_path)){
  				unlink($doc_path);
  			}

  			if($doc->save($doc_path)){
  				echo "The simple document was created\n";
  			}
			else{
				echo "Error: the example_dom.xml cannot be created\n";
			}
		}
	}

?>