$(document).ready(function() {
	
	var selected_document_html = '';

	var doc_text = '';
	var doc_id = '';
	var doc_path = '';
	var user_name = '';
	var relation_data = '';

	var selected = '';

	var edit_relation_id = '';

	$('.row .btn-detail').on('click', function(e) {
		console.log("click");
	    e.preventDefault();
	    var $this = $(this);
	    var $collapse = $this.closest('.collapse-group').find('.collapse');
	    $collapse.collapse('toggle');
	});

	$('.dropdown-menu a').on('click', function(){ 
		selected_document_html = "<span id='doc_id_selected'>" + $(this).find('.doc_id').html() + "</span><span class='hide' id='doc_path_selected'>" + $(this).find('.doc_path').html() + "</span>" + ' <span class="caret"></span>';
	    $('.dropdown-toggle').html(selected_document_html);
		doc_id = $('#doc_id_selected').html();
		doc_path = $('#doc_path_selected').html();
	});

	$('#load_doc').on('click', function(){
		if(doc_path == ''){
			alert("No document selected!!!");
			$("#list-heading .add-relation").attr('disabled', 'disabled');
			doc_id = '';
			doc_text = '';
			relation_data = '';
			$('#pre-text').html(doc_text);
			$('#modal-pre-text').html(doc_text);
			$("#accordion").empty();
		}
		else{
			user_name = $("#username").html();
			console.log(doc_path);
		    jQuery.ajax({
			    type: 'GET',
			    url: '/public/gujarati_connective/getDocument.php?doc_path=' + doc_path,
			    dataType: 'text',
			    success: function (data, text){
			    	doc_text = data;
			    	console.log(doc_text);
			    	$('#pre-text').html(doc_text);
			    	$('#modal-pre-text').html(doc_text);
			    	jQuery.ajax({
					    type: 'GET',
					    url: '/public/gujarati_connective/getRelation.php',
					    dataType: 'html',
					    data: {
					    	username: user_name,
					    	doc_id: doc_id
					    },
					    success: function (data, text){
					    	data = $.parseJSON(data);
					    	relation_data = data;
					    	listRelations();
					    	$("#list-heading .add-relation").removeAttr('disabled');
					    },
				        error: function (request, error) {
					        console.log(error);
					        console.log(request);
							console.log(doc_path);
					    }
					});
			    },
		        error: function (request, error) {
			        console.log(error);
			        console.log(request);
					console.log(doc_path);
			    }
			});
		}
	});

	$("#dump-relation").on('click', function(){
		$.ajax({
		    type: 'POSt',
		    url: '/public/gujarati_connective/dumpFiles.php',
		    dataType: 'text',
		    success: function (data, text){
		    	if(data.error == 'true'){
		    		alert(data.message);
		    	}
		    	else if(data.error == 'false'){
		    		console.log("Relations have been dumped");
		    	}
		    },
	        error: function (request, error) {
		        console.log(error);
		        console.log(request);
		    }
		});
	});

	function listRelations(){
		var count = 1;
		$("#accordion").accordion();
		$("#accordion").accordion('destroy');
		$("#accordion").empty();
		jQuery.each(relation_data, function(key, value){
			console.log(key);
			var newDiv = $('<div class="panel panel-default"><div class="panel-heading"><h4 class="panel-title"><a data-toggle="collapse" data-parent="#accordion" href="#collapse' + key + '">Relation ' + count + '</a></h4></div><div id="collapse' + key + '" class="panel-collapse collapse"><div class="panel-body"><div class="list-group"><span class="hide relation_id">' + key + '</span><span class="list-group-item"><span class="label label-success"><font size="2">Relation Type</font></span><span class="label-badge label-info label relation_name"><font size="2">' + value.relation_name + '</font></span></span><a href="#" class="list-group-item "><span class="label label-success"><font size="2">Connective</font></span><span class="hide connective">' + value.connective_span + '</span></a><a href="#" class="list-group-item "><span class="label label-success"><font size="2">Argument 1</font></span><span class="hide arg1">' + value.arg1_span + '</span></a><a href="#" class="list-group-item "><span class="label label-success"><font size="2">Argument 2</font></span><span class="hide arg2">' + value.arg2_span + '</span></a><span class="list-group-item "><span class="label label-success"><font size="2">Relation Sense</font></span><span class="label-badge label-info label sense"><font size="2">' + value.sense + '</font></span></span></div><div class="btn-group-container"><div class="btn-group"><button type="button" class="btn btn-primary edit-relation" data-toggle="modal" data-target="#myModal">Edit</button><button type="button" class="btn btn-danger delete-relation">Delete</button></div></div></div></div></div>');
			$("#accordion").append(newDiv);
			newDiv.on('click', '.edit-relation', editHandler);
			newDiv.on('click', '.delete-relation', deleteHandler);
			count += 1;
		});
		// $("#accordion").accordion('refresh');
		$('.list-group a').on('click', function(){
			if($(this).hasClass('active')){
				$(this).removeClass('active');
				pageUnHighlight();
			}
			else{
				$('.list-group a').removeClass('active');
				$(this).addClass('active');
				// Highlight text
				if($(this).find('.hide').hasClass('connective')){
					pageHighlight($(this).find('.hide').html());
				}
				else if($(this).find('.hide').hasClass('arg1')){
					pageHighlight($(this).find('.hide').html());
				}
				else if($(this).find('.hide').hasClass('arg2')){
					pageHighlight($(this).find('.hide').html());
				}
				else{
					console.log("error in finding span");
				}
			}
		});
	}

	function modalUnHighlight(){
		$('#modal-pre-text').html(doc_text);
	}

	function modalHighlight(textSpan){
		if(textSpan == ''){
			return ;
		}
		var spanList = textSpan.split(";");
		spanList.sort(function(a,b){
			a_s = a.split(":")[0];
			b_s = b.split(":")[0];
			return a_s-b_s;
		});
		console.log(spanList);
		var i;
		var last = 0;
		var htmlContent = '';
		for(i = 0 ; i < spanList.length ; i ++){
			var span = spanList[i].split(":");
			var span_start = span[0];
			var span_end = span[1];
			htmlContent += doc_text.substring(last, span_start) + "<span style='background-color:yellow'>" + doc_text.substring(span_start, span_end) + "</span>";
			last = span_end;
		}
		htmlContent += doc_text.substring(last);
		$('#modal-pre-text').html(htmlContent);
	}

	function pageUnHighlight(){
		$('#pre-text').html(doc_text);
	}

	function pageHighlight(textSpan){
		if(textSpan == ''){
			pageUnHighlight();
			return ;
		}
		var spanList = textSpan.split(";");
		spanList.sort(function(a,b){
			a_s = a.split(":")[0];
			b_s = b.split(":")[0];
			return a_s-b_s;
		});
		console.log(spanList);
		var i;
		var last = 0;
		var htmlContent = '';
		for(i = 0 ; i < spanList.length ; i ++){
			var span = spanList[i].split(":");
			var span_start = span[0];
			var span_end = span[1];
			htmlContent += doc_text.substring(last, span_start) + "<span style='background-color:yellow'>" + doc_text.substring(span_start, span_end) + "</span>";
			last = span_end;
		}
		htmlContent += doc_text.substring(last);
		$('#pre-text').html(htmlContent);
	}

	$(".radioSelect").on('click', function(){
		console.log("123");
		console.log($(this).closest('.form-group').find('.form-control').attr('id'));
		$('#modal-body-form').find('.form-control').attr('disabled', 'disabled');
		$(this).closest('.form-group').find('.form-control').removeAttr('disabled');
		selected = $(this).closest('.form-group').find('.form-control').attr('id');
		if(selected == 'modal_relation_name' || selected == 'modal_sense'){
			$("#btn-snap").attr('disabled', 'disabled');
			$("#btn-show").attr('disabled', 'disabled');
		}
		else{
			$("#btn-snap").removeAttr('disabled');	
			$("#btn-show").removeAttr('disabled');
		}
	});

	function doesOverlap(a_s, a_e, b_s, b_e){
			if((a_s<a_e && a_e<b_s && b_s<b_e) || (b_s<b_e && b_e<a_s && a_s<a_e)){
				return false;
			}
			else{
				return true;
			}
	}

	function checkSpansValidity(listSpan){
		// Check if some overlaps or exceeds the length of doc
		var text_length = doc_text.length;
		var i, j;
		for(i = 0 ; i < listSpan.length ; i ++){
			var start = parseInt(listSpan[i].split(':')[0]);
			var end = parseInt(listSpan[i].split(':')[1]);
			console.log("Text length: " + text_length);
			if(start < 0 || start > end || end >= text_length){
				alert("Problems with span: Span does not fit the text length" + start + ":" + end);
				return false;
			}
		}
		for(i = 0 ; i < listSpan.length ; i ++){
			for(j = 0 ; j < listSpan.length ; j ++){
				if(i == j){
					continue;
				}
				var a_s = parseInt(listSpan[i].split(':')[0]);
				var a_e = parseInt(listSpan[i].split(':')[1]);
				var b_s = parseInt(listSpan[j].split(':')[0]);
				var b_e = parseInt(listSpan[j].split(':')[1]);

				if(!doesOverlap(a_s, a_e, b_s, b_e)){
					// correct
				}
				else{
					alert("Problems with span: Spans overlapping " + a_s + ":" + a_e + " and " + b_s + ":" + b_e);
					return false;
				}
			}
		}
		console.log("correct");
		return true;
	}

	$('#btn-save').on('click', function(){
		$('#btn-save').button({loadingText: 'Saving...'});
		$('#btn-save').button('loading');
		
		var relation_name = $('#modal_relation_name').val();
		if(relation_name == ''){
			alert("Relation Name not specified!");
			$('#btn-save').button('reset');
			return ;
		}
		var connective = $('#modal_connective').val();
		if(relation_name != 'Explicit'){
			connective = '';
		}
		else if(connective == ''){
			return ;
		}

		var arg1 = $('#modal_arg1').val();
		var arg2 = $('#modal_arg2').val();
		var sense = $('#modal_sense').val();
		if(arg1 == ''){
			alert("Argument 1 not specified!!!");
			$('#btn-save').button('reset');
			return ;
		}
		if(arg2 == ''){
			alert("Argument 2 not specified!!!");
			$('#btn-save').button('reset');
			return ;
		}
		if(relation_name == 'EntRel'){
			sense = 'EntRel';
		}
		else if(sense == ''){
			alert("Relation is not EntRel but still Relation Sense not specified!!!");
			$('#btn-save').button('reset');
			return ;
		}
		listTextSpan = [arg1, arg2];

		if(connective != ''){
			listTextSpan.push(connective);
		}

		var listSpan = []
		var i;
		for(i = 0 ; i < listTextSpan.length ; i ++){
			var span = listTextSpan[i];
			var allSpan = span.split(';');
			listSpan = listSpan.concat(allSpan);
		}

		var validity =  checkSpansValidity(listSpan);
		if(!validity){
			$('#btn-save').button('reset');
			return ;
		}
		
		$.ajax({
			type: 'POST',
			url: '/public/gujarati_connective/addRelation.php',
			dataType: 'json',
			data: {
				user_name: user_name,
				doc_name: doc_id,
				relation_name: relation_name,
				connective: connective,
				arg1: arg1,
				arg2: arg2,
				sense: sense,
				edit_relation_id: edit_relation_id
			},
			success: function(data){
				console.log(data);
				alert("Your data has been saved..");
				$('#btn-save').button('reset');
				// location.reload(true);
				$("#myModal").modal('hide');
				$('.dropdown-toggle').html(selected_document_html);
				$('#load_doc').trigger('click');
			},
			error: function(){
				$('#btn-save').button('reset');
			}
		});

	});

	$("#btn-show").on('click', function(){
		if(selected == ''){
			return ;
		}
		if($(this).hasClass('active')){
			$(this).removeClass('active');
			modalUnHighlight();
			$('#' + selected).removeAttr('disabled');
			$("#btn-snap").removeAttr('disabled');
			$('#modal-body-form .radioSelect').removeAttr('disabled');
		}
		else{
			listSpan = $("#" + selected).val().split(";");
			for(i = 0 ; i < listSpan.length ; i ++){
				for(j = 0 ; j < listSpan.length ; j ++){
					if(i == j){
						continue;
					}
					var a_s = parseInt(listSpan[i].split(':')[0]);
					var a_e = parseInt(listSpan[i].split(':')[1]);
					var b_s = parseInt(listSpan[j].split(':')[0]);
					var b_e = parseInt(listSpan[j].split(':')[1]);

					if(!doesOverlap(a_s, a_e, b_s, b_e)){
						// correct
					}
					else{
						alert("Problems with span: Spans overlapping " + a_s + ":" + a_e + " and " + b_s + ":" + b_e);
						return ;
					}
				}
			}
			$(this).addClass('active');
			modalHighlight($("#" + selected).val());
			$('#' + selected).attr('disabled', 'disabled');
			$("#btn-snap").attr('disabled', 'disabled');
			$('#modal-body-form .radioSelect').attr('disabled', 'disabled');
		}
		
	});

	$("#btn-snap").on('click', function(){
        
        console.log("111111111111");
        
		var selection = window.getSelection();
		var selection_start = selection.anchorOffset;
		var anchorNode = selection.anchorNode;
		var selection_end = selection.focusOffset;
		var start = selection_start;
		var end = selection_end;
        
        console.log(start + "  " + end);

		if(selection_start > selection_end){
			start = selection_end;
			end = selection_start;
		}

		var focusNode = selection.focusNode;
		if(start == end){
			return ;
		}
        
        console.log(start + "  " + end);
        console.log(anchorNode.parentElement + "   " + focusNode.parentElement);
        console.log(selection);
		if(anchorNode.parentElement.id == 'modal-pre-text' && focusNode.parentElement.id == 'modal-pre-text'){
			if($("#" + selected).val() != ''){
				$("#" + selected).val($("#" + selected).val() + ";" + start + ":" + end);
			}
			else{
				$("#" + selected).val($("#" + selected).val() + start + ":" + end);
			}
		}
	});

	$("#btn-clear").on('click', function(){
		if(selected == ''){
			return ;
		}
		$('#' + selected).val('');
	});

	$("#btn-cancel").on('click', function(){
		$("#myModal").modal('hide');
	});

	function editHandler(){
		edit_relation_id = $(this).closest('.panel-body').find('.relation_id').html();
		console.log("333333");
		var relation_name = $(this).closest('.panel-body').find('.relation_name').find('font').html();
		var connective = $(this).closest('.panel-body').find('.connective').html();
		var arg1 = $(this).closest('.panel-body').find('.arg1').html();
		var arg2 = $(this).closest('.panel-body').find('.arg2').html();
		var sense = $(this).closest('.panel-body').find('.sense').find('font').html();

		console.log(relation_name);
		console.log(connective);
		console.log(arg1);
		console.log(arg2);
		console.log(sense);

		$("#modal_relation_name").val(relation_name);
		$("#modal_connective").val(connective);
		$("#modal_arg1").val(arg1);
		$("#modal_arg2").val(arg2);
		$("#modal_sense").val(sense);
	}

	function deleteHandler(){
		var delete_relation_id = $(this).closest('.panel-body').find('.relation_id').html();
		$.ajax({
			type: 'POST',
			url: '/public/gujarati_connective/deleteRelation.php',
			dataType: 'json',
			data: {
				user_name: user_name,
				doc_name: doc_id,
				relation_id: delete_relation_id
			},
			success: function(data){
				console.log(data);
				alert("Relation has been deleted..");
				// location.reload(true);
				$('.dropdown-toggle').html(selected_document_html);
				$('#load_doc').trigger('click');
			},
			error: function(){
				$('#btn-save').button('reset');
			}
		});
	}

	$('.add-relation').on('click', function(){
		edit_relation_id = '';
		$("#modal_relation_name").val('');
		$("#modal_connective").val('');
		$("#modal_arg1").val('');
		$("#modal_arg2").val('');
		$("#modal_sense").val('');
	})

});