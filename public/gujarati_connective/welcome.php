<?php
	include('session.php');

   	$sql = "SELECT document_name, document_path FROM document";
    $result = mysqli_query($db,$sql);
    $docPathPair = array();
	while ($row = $result->fetch_assoc()){
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

	    <title>Welcome</title>

	    <!-- Bootstrap core CSS -->
	    <link href="/lib/css/bootstrap.min.css" rel="stylesheet">

	    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
	    <link href="/lib/css/ie10-viewport-bug-workaround.css" rel="stylesheet">

	    <!-- Custom styles for this template -->
	    <link href="/lib/css/gujarati_connective/welcome.css" rel="stylesheet">

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

	    	<div class="dropdown my_dropdown">
				<button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown"><span id="doc_id_selected">Choose a Document </span><span class="hide" id="doc_path_selected"></span><span class="caret"></span></button>
				<ul class="dropdown-menu">
					<li><a href='#'><span class='doc_id'>Choose a Document </span><span class="hide doc_path"></span></a></li>
    				<?php
    					foreach($docPathPair as $x => $x_value) {
						    echo "<li><a href='#'><span class='doc_id'>$x</span><span class='hide doc_path'>$x_value</span></a></li>";
						}
    				?>
				</ul>
			</div>

			<div class="doc_btn_group">
				<button class="btn btn-large" type="button" id="load_doc">Load this document</button>
				<button type="button" class="btn btn-info" id="dump-relation">Dump All Relations</button>
			</div>

			<hr>

			<div id="workspace">
				<div id="text">
					<pre id="pre-text">
											
					</pre>
				</div>
				<div id="list">
					<div id="list-heading" style="text-align: center">
						<h4>List of Relations: </h4>
						<button type="button" class="btn btn-primary add-relation" data-toggle="modal" data-target="#myModal" disabled="disabled">Add</button>
					</div>
					<div id='accordion-container'>
						<div class='panel-group' id='accordion'>
	  						<!-- <div class="panel panel-default">
	  							<div class="panel-heading">
	  								<h4 class="panel-title">
	  									<a data-toggle="collapse" data-parent="#accordion" href="#collapse1">Relation 1</a>
	  								</h4>
	  							</div>
	  							<div id="collapse1" class="panel-collapse collapse">
	  								<div class="panel-body">
	  									<div class="list-group">
											<span class="list-group-item"><span class="label label-success"><font size="2">Relation Type</font></span><span class="label-badge label-info label" id="relation_name"><font size="2">Explicit</font></span></span>
											<a href="#" class="list-group-item "><span class="label label-success"><font size="2">Connective</font></span><span class="hide" id="connective"></span></a>
											<a href="#" class="list-group-item "><span class="label label-success"><font size="2">Argument 1</font></span><span class="hide" id="arg1"></span></a>
											<a href="#" class="list-group-item "><span class="label label-success"><font size="2">Argument 2</font></span><span class="hide" id="arg2"></span></a>
											<span class="list-group-item "><span class="label label-success"><font size="2">Relation Sense</font></span><span class="label-badge label-info label" id="sense"><font size="2">Expansion.Conjunction</font></span></span>
										</div>
										<div class="btn-group-container">
											<div class="btn-group">
												<button type="button" class="btn btn-primary edit-relation" data-toggle="modal" data-target="#myModal">Edit</button>
												<button type="button" class="btn btn-danger delete-relation">Delete</button>
											</div>
										</div>
	  								</div>
	  							</div>
	  						</div> -->
	  					</div>
  					</div> 
				</div>
	      	</div>

			<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
				<div class="vertical-alignment-helper">
					<div class="modal-dialog vertical-align-center">
						<div class="modal-content">
							<div class="modal-header">
								<h4 class="modal-title" id="myModalLabel">Annotate a Relation</h4>
							</div>
							<div class="modal-body">
									<div id="modal-body-text">
										<pre id="modal-pre-text">
											
										</pre>
									</div>
									<div id="modal-body-form">
										<form role="form">
											<div class="form-group">
      											<label for="relation_name"><input type="radio" name="optradio" class="radioSelect"> Relation Name: </label>
      											<select class="form-control" id="modal_relation_name" disabled>
												    <option></option>
												    <option>Explicit</option>
												    <option>Implicit</option>
												    <option>AltLex</option>
												    <option>EntRel</option>
												 </select>
    										</div>
    										<div class="form-group">
      											<label for="connective"><input type="radio" name="optradio" class="radioSelect"> Connective: </label>
      											<input type="text" class="form-control" id="modal_connective" disabled>
    										</div>
    										<div class="form-group">
      											<label for="arg1"><input type="radio" name="optradio" class="radioSelect"> Argument 1: </label>
      											<input type="text" class="form-control" id="modal_arg1" disabled>
    										</div>
    										<div class="form-group">
      											<label for="arg2"><input type="radio" name="optradio" class="radioSelect"> Argument 2: </label>
      											<input type="text" class="form-control" id="modal_arg2" disabled>
    										</div>
    										<div class="form-group">
      											<label for="sense"><input type="radio" name="optradio" class="radioSelect"> Relation Sense: </label>
      											<input type="text" class="form-control" id="modal_sense" disabled>
    										</div>
    										<!-- <div id="modal-btn-group-container"> -->
	    										<div id="modal-btn-group">
		    										<button type="button" class="btn btn-success" id="btn-save">Save</button>
		    										<button type="button" class="btn btn-primary" id="btn-clear">Clear</button>
		    										<button type="button" class="btn btn-default" id="btn-show" disabled="disabled">Show</button>
		    										<button type="button" class="btn btn-info" id="btn-snap" disabled="disabled">Snap</button>
		    										<button type="button" class="btn btn-danger" id="btn-cancel">Cancel</button>
	    										</div>
    										<!-- </div> -->
  										</form>
									</div>
							</div>
							<div class="modal-footer">
								
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
    	<script src="/lib/js/gujarati_connective/welcome.js"></script>
    	<!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    	<script src="/lib/js/ie10-viewport-bug-workaround.js"></script>

 	</body>
   
</html>