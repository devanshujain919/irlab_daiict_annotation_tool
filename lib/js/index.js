$('.row .btn-detail').on('click', function(e) {
	console.log("click");
    e.preventDefault();
    var $this = $(this);
    var $collapse = $this.closest('.collapse-group').find('.collapse');
    $collapse.collapse('toggle');
});