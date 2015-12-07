$( document ).ready(function() {
	$('.sortable').sortable({
        cursor: 'move',
        update: function (event, ui) {
          panel = $(this).parent();
		  update_order_numbers(panel);
        }
    });
});