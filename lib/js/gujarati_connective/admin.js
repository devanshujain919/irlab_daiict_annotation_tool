$(document).ready(function(){

	var number_of_files_staged_overall = 0;

	var file_staged_overall = {};

	var allowed_mime_types = ['text', ''];

	var file_staged_overall_keys = [];

	$('#file-list-container .list-group .list-group-item').on('click', function(e){
		if($(e.target).hasClass('file-remove')){
			return ;
		}

		$('#file-list-container .list-group .list-group-item').removeClass('active');
		$(this).addClass('active');

		var doc_path = $(this).find('.file-path').html();
		console.log(doc_path);

		jQuery.ajax({
		    type: 'GET',
		    url: '/public/gujarati_connective/getDocument.php?doc_path=' + doc_path,
		    dataType: 'text',
		    success: function (data, text){
		    	var doc_text = data;
		    	console.log(doc_text);
		    	$('#pre-text').html(doc_text);
		    },
	        error: function (request, error) {
		        console.log(error);
		        console.log(request);
				console.log(doc_path);
		    }
		});
	});

	$('#file-list-container .list-group .list-group-item .file-remove').on('click', function(){
		$.ajax({
			type: 'POST',
			url: '/public/gujarati_connective/deleteDocument.php',
			dataType: 'json',
			data: {
				doc_name: $(this).closest('.list-group-item').find('.file-name').find('font').html()
			},
			success: function(data){
				console.log(data);
				if(data.error == 'false'){
					alert("Your file has been deleted..");
					// $("#pre-text").html('');
					// $('#file-list-container .list-group .list-group-item').removeClass('active');
					location.reload(true);
				}
			},
			error: function(data){
				console.log(data);
			}
		});
	});

	$('.btn-add-file .input-file').not('.hide').on('change', inputFileChangeHandler);

	function inputFileChangeHandler(){
		for (var i = 0; i < $(this).get(0).files.length; ++i) {
			console.log($(this).get(0).files[i].type);
			if($.inArray($(this).get(0).files[i].type.split('/')[0], allowed_mime_types) == -1){
				// not found
				alert('Invalid Mime type file in the selected list');
				$(this).val('');
				return ;
			}
	        var new_accordion_element = $('<div class="panel panel-default"><div class="panel-heading"><h4 class="panel-title"><a data-toggle="collapse" data-parent="#accordion" href="#collapse' + (number_of_files_staged_overall + i + 1) + '">' + $(this).get(0).files[i].name + '</a><span class="status"><span class="glyphicon glyphicon-ok hide"></span><span class="glyphicon glyphicon-remove hide"></span></span></h4></div><div id="collapse' + (number_of_files_staged_overall + i + 1) + '" class="panel-collapse collapse"><div class="panel-body"><div class="list-group"><span class="file hide">' + (number_of_files_staged_overall + i + 1) + '</span><span class="list-group-item "><span class="label label-success label-key"><font size="2">File Size</font></span><span class="label-badge label-info label label-value"><font size="2">' + displayableSize($(this).get(0).files[i].size) + '</font></span></span><span class="list-group-item "><span class="label label-success label-key"><font size="2">File Type</font></span><span class="label-badge label-info label label-value"><font size="2">' + $(this).get(0).files[i].type + '</font></span></span></div><div class="modal-btn-group-container"><div class="modal-btn-group"><button type="button" class="btn btn-danger remove-file">Remove</button></div></div></div></div></div>');
	        new_accordion_element.find('.remove-file').on('click', removeFile);
	        $("#accordion").append(new_accordion_element);
	        file_staged_overall[number_of_files_staged_overall + i + 1] = $(this).get(0).files[i];
	    }
	    var number_of_files_staged_this = $(this).get(0).files.length;
		$('.btn-add-file .input-file').addClass('hide');
		var newInput = $('<input type="file" class="input-file" multiple>');
		$('.btn-add-file').append(newInput);
		newInput.on('change', inputFileChangeHandler);
	    number_of_files_staged_overall += number_of_files_staged_this;

	}

	function displayableSize(size_in_bytes){
		if(size_in_bytes < 1024){
			return Math.round(size_in_bytes) + ' bytes';
		}
		else if(size_in_bytes < 1024*1024){
			return Math.round(size_in_bytes / 1024.0) + ' KB';
		}
		else if(size_in_bytes < 1024*1024*1024){
			return Math.round(size_in_bytes / (1024.0*1024.0)) + ' MB';
		}
		return size_in_bytes;
	}

	function removeFile(){
		delete file_staged_overall[$(this).closest('.panel-body').find('.file')];
		var accordion_element = $(this).closest('.panel');
		accordion_element.remove();
		$('#modal-pre-text').html('');
	}

	$('#accordion').find('.remove-file').on('click', removeFile);

	$('#btn-upload').on('click', function(){
		console.log("####################");
		$('.btn-add-file').find('.input-file.hide').each(function(){
			console.log("---------------------------------new");
			for (var i = 0; i < $(this).get(0).files.length; ++i) {
		        console.log($(this).get(0).files[i].name);
		    }
			console.log("---------------------------------end");
		});
		console.log("####################");

		var flag = validate();

		if(!flag){
			console.log('returning');
			return ;
		}

		for(var key in file_staged_overall){
			file_staged_overall_keys.push(key);
		}

		uploadFile(0);

	});

	$('#btn-cancel').on('click', function(){
		$("#myModal").modal('hide');
	});

	function uploadFile(index){

		if(index >= file_staged_overall_keys.length){
			console.log("wronng index of to-be-upload file provided...");
			// all files have been uploaded or have been taken care of..
			location.reload(true);
		}

		var reader = new FileReader();
		reader.onload = function (e) {
			var doc_text = e.target.result;
		    var doc_name = file_staged_overall[file_staged_overall_keys[index]].name;

		    $.ajax({
				type: 'POST',
				url: '/public/gujarati_connective/addDocument.php',
				dataType: 'json',
				data: {
					doc_name: doc_name,
					doc_text: doc_text
				},
				success: function(data){
					console.log(data);
					if(data.error == 'true'){
						alert(data.message);
						$('#collapse' + file_staged_overall_keys[index]).closest('.panel').find('.glyphicon-remove').removeClass('hide');
						$('#collapse' + file_staged_overall_keys[index]).closest('.panel').find('.glyphicon-ok').addClass('hide');
					}
					else{
						$('#collapse' + file_staged_overall_keys[index]).closest('.panel').find('.glyphicon-remove').addClass('hide');
						$('#collapse' + file_staged_overall_keys[index]).closest('.panel').find('.glyphicon-ok').removeClass('hide');
					}
					uploadFile(index + 1);
				},
				error: function(data){
					console.log(data);
				}
			});
		};
		console.log(file_staged_overall_keys[index]);
		reader.readAsText(file_staged_overall[file_staged_overall_keys[index]]);
	}

	function validate(){
		// no file greater than 2 MB
		for(var key in file_staged_overall){
			var file = file_staged_overall[key];
			var threshold_size = 1024*1024*2; // 2 MB
			if(file.size > threshold_size){
				alert('file ' + file.name + ' is greater than 2MB.')
				return false;
			}
		}
		return true;
	}

	$('#add-file').on('click', function(){
		$('#btn-remove-all').trigger('click');
	})

	$('#btn-remove-all').on('click', function(){
		$("#accordion").accordion();
		$("#accordion").accordion('destroy');
		$("#accordion").empty();
		$("#modal-pre-text").html('');
		$('.btn-add-file').empty();
		var newInput = $('<input type="file" class="input-file" multiple>');
		$('.btn-add-file').append('Add Files ');
		$('.btn-add-file').append(newInput);
		newInput.on('change', inputFileChangeHandler);
		file_staged_overall = {};
		number_of_files_staged_overall = 0;
	});

	$('#accordion').on('show.bs.collapse', function (e) {
		var reader = new FileReader();
		reader.onload = function (e) {
			var text = e.target.result;
		    console.log(text);
		    $("#modal-pre-text").html(text);
		 //    var arr = new Uint8Array(e.target.result).subarray(0, 4);
			// var header = "";
			// for(var i = 0; i < arr.length; i++) {
			// 	header += arr[i].toString(16);
			// }
			// console.log(header);
		};
		console.log($(e.target).find('.file').html());
		reader.readAsText(file_staged_overall[$(e.target).find('.file').html()]);
		console.log(file_staged_overall[$(e.target).find('.file').html()]["type"]);
	});

});