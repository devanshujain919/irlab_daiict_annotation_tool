<?php
	include('session.php');

	$user_check = $_SESSION['login_user'];
	if($user_check != 'admin'){
		header("Location: /public/gujarati_connective/logout.php");
	}

	$sql = "SELECT document_name, document_path FROM document";
    $result = mysqli_query($db,$sql);
    $docPathPair = array();
	while ($row = $result->fetch_assoc()){
		// echo $row['document_name'];
		$docPathPair[$row['document_name']] = $row['document_path'];
	}
?>

<html>
   
   	<head>
   	   <meta charset="utf-8">
	    <meta http-equiv="X-UA-Compatible" content="IE=edge">
	    <meta name="viewport" content="width=device-width, initial-scale=1">
	    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
	    <meta name="description" content="">
	    <meta name="author" content="">
	    <link rel="icon" href="/lib/img/favicon.ico">

	    <title>Add docs</title>

	    <!-- Bootstrap core CSS -->
	    <link href="/lib/css/bootstrap.min.css" rel="stylesheet">

	    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
	    <link href="/lib/css/ie10-viewport-bug-workaround.css" rel="stylesheet">

	    <!-- Custom styles for this template -->
	    <link href="/lib/css/gujarati_connective/admin.css" rel="stylesheet">

	    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
	    <!--[if lt IE 9]>
	      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
	      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
	    <![endif]-->
   	</head>

   	<body>
   		<nav class="navbar navbar-inverse navbar-fixed-top">
      		<div class="container-fluid">
        		<div class="navbar-header">
        			<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>                        
					</button>
          			<a class="navbar-brand" href="#">Annotation Tool</a>
        		</div>
        		<div class="collapse navbar-collapse" id="myNavbar">
	        		<ul class="nav navbar-nav">
	          			<li><a href="/public/index.php">Home</a></li>
	          			<li><a href="https://goo.gl/g4XcgZ" target="_blank">Give Feedback</a></li>
	          			<?php
	        				if($_SESSION['login_user'] == 'admin'){
	        					echo '<li><a href = "/public/gujarati_connective/admin.php">Add Docs</a></li>';
	        				}
	        			?>
	        		</ul>
	        		<ul class="nav navbar-nav navbar-right">
						<li><a href="/public/gujarati_connective/logout.php"><span class="glyphicon glyphicon-log-out"></span> Sign Out</a></li>
					</ul>
	        	</div>
      		</div>
    	</nav>

    	<div class="container" id="page-container">
    	    <h1>Welcome <?php echo "<span id='username'>$login_session</span>"; ?></h1> 
	    	<hr>

			<div id="workspace">
				<div id="text">
					<pre id="pre-text">
											
					</pre>
				</div>
				<div id="list">
					<div id="list-heading" style="text-align: center">
						<h4>List of Files: </h4>
						<button type="button" class="btn btn-primary" id="add-file" data-toggle="modal" data-target="#myModal">Add</button>
					</div>
					<div id='file-list-container'>
						<div class="list-group">
							<?php
		    					foreach($docPathPair as $x => $x_value) {
		    						// echo $x;
								    echo '<span class="list-group-item">';
								    echo '<span class="label label-success file-name"><font size="2">' . $x . '</font></span>';
								    echo '<span class="label label-success file-path hide">' . $x_value . '</span>';
								    echo '<span class="glyphicon glyphicon-remove file-remove"></span>';
								    echo '</span>';
								    echo '<br>';
								}
		    				?>
						</div>
  					</div> 
				</div>
	      	</div>

			<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
				<div class="vertical-alignment-helper">
					<div class="modal-dialog vertical-align-center">
						<div class="modal-content">
							<div class="modal-header">
								<h4 class="modal-title" id="myModalLabel">Add Files</h4>
							</div>
							<div class="modal-body">
								<div id="modal-body-text">
									<pre id="modal-pre-text">
										
									</pre>
								</div>
								<div id='modal-file-list-container'>
									<div id='accordion-container'>
										<div class='panel-group' id='accordion'>
					  						<!-- <div class="panel panel-default">
					  							<div class="panel-heading">
					  								<h4 class="panel-title">
					  									<a data-toggle="collapse" data-parent="#accordion" href="#collapse1">File Name</a>
					  									<span class='status'>
					  										<span class="glyphicon glyphicon-ok hide"></span>
					  										<span class="glyphicon glyphicon-remove hide"></span>
					  									</span>
					  								</h4>
					  							</div>
					  							<div id="collapse1" class="panel-collapse collapse">
					  								<div class="panel-body">
					  									<div class="list-group">
					  										<span class="file hide"></span>
															<span class="list-group-item "><span class="label label-success label-key"><font size="2">File Size</font></span><span class="label-badge label-info label label-value"><font size="2">Some Size</font></span></span>
															<span class="list-group-item "><span class="label label-success label-key"><font size="2">File Type</font></span><span class="label-badge label-info label label-value"><font size="2">Some Type</font></span></span>
														</div>
														<div class="modal-btn-group-container">
															<div class="modal-btn-group">
																<button type="button" class="btn btn-danger remove-file">Remove</button>
															</div>
														</div>
					  								</div>
					  							</div>
					  						</div> -->
					  					</div>
  									</div>
			  					</div>
							</div>
							<div class="modal-footer">
							 	<div id='modal-btn-group' class="fileinput fileinput-new" data-provides="fileinput">
									<span class="btn btn-info btn-add-file">Add Files <input type="file" class="input-file" multiple></span>
									<button type="button" class="btn btn-success" id="btn-upload">Upload</button>
									<button type="button" class="btn btn-warning" id="btn-remove-all">Remove All</button>
									<button type="button" class="btn btn-danger" id="btn-cancel">Cancel</button>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

      	</div>

      	<footer>
			<div id="footer-container">
       			<hr>
        		<p>&copy; 2016 IR Lab, DA-IICT</p>
    		</div>
  		</footer>
      	

      	<script src="/lib/js/jquery.min.js"></script>
    	<script src="/lib/js/jquery-ui.min.js"></script>
    	<script src="/lib/js/bootstrap.min.js"></script>
    	<script src="/lib/js/gujarati_connective/admin.js"></script>
    	<!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    	<script src="/lib/js/ie10-viewport-bug-workaround.js"></script>

    </body>

</html>