var dynamicEditorConfig = {
    toolbar:
        [
            [ 'Bold','Italic','Underline','Strike','Subscript','Superscript','-','RemoveFormat' ],
            [ 'Link','Unlink','Image','Flash','Table','PageBreak','Iframe' ] ,
            [ 'NumberedList','BulletedList','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock' ],
            [ 'Maximize'],
			['Format'],['Source']
        ],
    coreStyles_bold: { element : 'b', overrides : 'strong' },
	format_tags: 'p;h1;h2;h3;h4;h5;h6;pre'

};
$( document ).ready(function() {
	$(function () {
		$("select.drop-parameter").change();
	});
	// ajax when change select type
	//TODO remove it as soon it is defined globally
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('[name=_token]').val()
		}
	});

	$(document).on('change', '.field-type', function(){
		// vars
		var select = $(this),
			tbody = select.closest('tbody'),
			field = tbody.closest('.another-field'),
			field_type = field.attr('data-type'),
			field_index = field.attr('data-id'),
			val = select.val();
		// update data atts
		field.removeClass('field_type-' + field_type).addClass('field_type-' + val);
		field.attr('data-type', val);
		// tab - override field_name
		if( val == 'tab' || val == 'message' )
		{
			tbody.find('tr.field_name input[type="text"]').val('').trigger('keyup');
		}
		if( tbody.children( 'tr.field_option_' + val ).length )
		{
			// hide + disable options
			tbody.children('tr.field_option').hide().find('[name]').attr('disabled', 'true');
			
			// show and enable options
			tbody.children( 'tr.field_option_' + val ).show().find('[name]').removeAttr('disabled');
		}else{
			// add loading gif
			var tr = $('<tr><td class="label"></td><td><div class="glyphicon glyphicon-refresh"></div></td></tr>');
			
			// hide current options
			tbody.children('tr.field_option').hide().find('[name]').attr('disabled', 'true');
			// append tr
			tbody.children('tr.field_save').before(tr);
			
			var reqData = "field_type="+val+"&field_index="+field_index;
			jQuery.ajax( {
						url : "renderOption",
						type : 'POST',
						enctype : 'multipart/form-data',
						datatype: 'json',
						data : reqData +"&ajax=true",
						success : function(responseData) {
							var data = eval(responseData);
							//$('#render_option').html(data.html);
							if( !data.html)
							{
								tr.remove();
								return;
							}
					
							tr.replaceWith(data.html);
					}
			});
		}
		var label = $(this).find('option[value="' + val + '"]').html();
		$(this).closest('.field').find('td.field-type').html(label);
		
		$('.sortable').sortable({
				cursor: 'move',
				update: function (event, ui) {
				  panel = $(this).parent();
				  update_order_numbers(panel);
				}
			});
		
	});
	
	
	
	
	// open and close form by toggle
	$(document).on('click', 'a.btn-toggle', function(){
		
		var $field = $(this).closest('.another-field');
		
		$field.children('.field_form_mask').animate({'height':'toggle'}, 250);
		if( $field.hasClass('form-open') )
		{
			$field.removeClass('form-open');
			$field.addClass('form-title');
		}
		else
		{
			$field.addClass('form-open');
			$field.removeClass('form-title');
		}
	});
	// remove when field when click btn delete
	$(document).on('click', 'a.btn-delete-field', function(){
		var data_id = $(this).attr("data-id");
		var delete_id = "." + $(this).attr("delete-list-id");
		if(data_id !="" ){
			var strItems = $('.box-body').find(delete_id).val();
			if(strItems!="") strItems+= ",";
			strItems	+= data_id;
			$('.box-body').find(delete_id).val(strItems);
		}
		
		// vars
		var a = $(this),
			field = a.closest('.another-field'),
			fields = field.closest('.fields'),
			temp = $('<div style="height:' + field.height() + 'px"></div>');
			
		var panel = field.parent().parent();	
		// fade away
		field.animate({'left' : '50px', 'opacity' : 0}, 250, function(){
			field.before(temp);
			field.remove();
			// no more fields, show the message
			if( fields.children('.field').length <= 1 )
			{
				temp.remove();
				
				/* fields.children('.no_fields_message').show(); */
			}
			else
			{
				temp.animate({'height' : 0 }, 250, function(){
					temp.remove();
				});
			}
			
			update_order_numbers(panel);
		});
	});
	// update value in table when change in form
	$(document).on('keyup', '.field_infor .field_form tr.field-label input.field-label', function(){
		var val = $(this).val();
		var name = $(this).closest('.another-field').find('td.field-label a').first().html(val);
		
	});
	
	$(document).on('keyup', '.field_infor .field_form tr.field-name input.field-name', function(){
	
		var val = $(this).val();
		var name = $(this).closest('.another-field').find('td.field-name').first().html(val);
		
	});
	
	$(document).on('change', '.field_form tr select.field-type,.field_form tr select.field-repeater-type', function(){
		var val = $(this).val();
		var label = $(this).find('option[value="' + val + '"]').html();
		
		$(this).closest('.another-field').find('td.field-type ').first().html(label);
		
	});
});
/*
	*  Auto Complete Field Name
	*
	*  @description: 
	*  @since 3.5.1
	*  @created: 15/10/12
*/
	
$(document).on('blur', '.field_infor .field_form tr.field-label input.field-label', function(){
	// vars
	var $label = $(this),
		$field = $label.closest('.another-field'),
		$name = $field.find('tr.field-name:first input[type="text"]');
	if( $name.val() == '' )
	{
		var val = $label.val(),
			replace = {
				'ä': 'a',
				'æ': 'a',
				'å': 'a',
				'ö': 'o',
				'ø': 'o',
				'é': 'e',
				'ë': 'e',
				'ü': 'u',
				'ó': 'o',
				'ő': 'o',
				'ú': 'u',
				'é': 'e',
				'á': 'a',
				'ű': 'u',
				'í': 'i',
				' ' : '_',
				'\'' : '',
				'\\?' : ''
			};
		
		$.each( replace, function(k, v){
			var regex = new RegExp( k, 'g' );
			val = val.replace( regex, v );
		});
		
		
		val = val.toLowerCase();
		$name.val( val );
		$name.trigger('keyup');
	}
	
});
// append field when click button on client
function appendField(e,o){
	e.preventDefault();
	addField(o);
}
function addField(o) {
	var _groupId	= '';

	var $tempate = $(o).parent().parent().next();
	var $list = $(o).parent().prev();
	var $panel = $list.parent();
	if(_groupId != ''){
		var $_new =  $tempate.clone(true).removeClass("more-files-template hidden").addClass("another-field");
	}else{
		var $_new =  $tempate.clone(true).removeClass("more-files-template hidden").addClass("another-field form-open");
	}
	
	// update names
	
	var index = getNewIndex($panel);
	var repeater_id = $(o).attr('repeater-data-id');
	$_new.update_names(index,repeater_id);
	$list.append( $_new );
	
	update_order_numbers($panel);
}
function addRepeaterField(Id) {

	
	var _repeateId	= '#repeater_template_' + Id;
	var _repeater_index  = '#repeater_index_' + Id;
	var _new_index  = $(_repeater_index).val();

	var $template = $(_repeateId);
	
	_new_index++;
	var $_new =  $template.clone(true).removeClass("repeater-template").addClass("another-field").attr('id','');
	 
	// update names
	var new_id  = "-" + _new_index;
	$_new.attr('data-id',new_id);
	var editors = [] ;
	var i=0 ;
	$_new.find('[id*="clone"]').each(function()
		{
			var id = $(this).attr('id') ;
			var name = $(this).attr('name') ;
			
			$(this).attr('id', id.replace("clone", new_id) );
			$(this).attr('name', name.replace("clone", new_id) );
			if($(this).hasClass('dynamic-editor')){
				editors[i] = $(this).attr('name');
				i++;
			}
				
		});
	$_new.find('.add-media').each(function()
	{
		var onclick = $(this).attr('onclick') ;
		
		$(this).attr('onclick', onclick.replace("clone", new_id) );
		
	});		
	$(_repeater_index).val(_new_index);
	$template.parent().append( $_new );
	
	// fixing editoer
	for(i=0;i< editors.length;i++){
		var new_name =  editors[i] ;
		CKEDITOR.replace(new_name,dynamicEditorConfig);
	 }
	 update_order_numbers($template.parent());
}

function getNewIndex(panel){
	var is_panel_parent = false;
   // update number for normal field
   var count = 0;
   if(panel.hasClass('field_infor')){
		is_panel_parent = true;
	}

	// update field of repeater field
	if(is_panel_parent ){
		panel.each(function(){
			count = $(this).find('.another-field').not('.field_option_repeater .another-field').length;
		});
	}else{
		panel.each(function(){
			count = $(this).find('.another-field').length;
		});
	}
	
	return count;
}
function update_order_numbers(panel){
   var is_panel_parent = false;
   // update number for normal field
	/* console.log(panel); */
	if(panel.hasClass('field_infor')){
		is_panel_parent = true;
	}



	if(is_panel_parent ){
			/* console.log("Step_1"+panel) */
			panel.each(function(){
				$(this).find('.another-field').not('.field_option_repeater .another-field').each(function(i){
					var index = i+1 ;
					var $td_order = $(this).find('td.field-order').first();
					var $circle = $td_order.find('.circle').first();
					var $order = $td_order.find('input:hidden').first();
					 $circle.html(index);
					 $order.val(index);
				});
			});
	}else{// update field of repeater field
		/* console.log(panel) */
		panel.each(function(){
			$(this).find('.another-field').each(function(i){
				var index = i+1 ;
					var $td_order = $(this).find('td.field-order').first();
					var $circle = $td_order.find('.circle').first();
					var $order = $td_order.find('input:hidden').first();
					 $circle.html(index);
					 $order.val(index);
			});
		});
	}

    // for repeater

    if( panel.parent().hasClass('table-repeater')){

        panel.each(function(){
            $(this).find('.ui-sortable-handle').not('.repeater-template').each(function(i){
                var index = i+1 ;
                var $td_order = $(this).find('td.field-order').first();
                var $circle = $td_order.find('.circle').first();
                var $order = $td_order.find('input:hidden').first();
                $circle.html(index);
                $order.val(index);
            });
        });
    }
}

// update name when clone
(function($){
	$.fn.update_names = function(index,repeater_id)
	{
		var field = $(this),
			old_id = "field_clone" ;//field.attr('data-id'),
			old_repeater_id = "repeater_clone" ;//field.attr('data-id'),
			new_id = index + 1;
		// give field a new id
		field.attr('data-id', new_id);

		field.find('[id*="' + old_id + '"]').each(function()
		{	
			
			$(this).attr('id', $(this).attr('id').replace(old_id, new_id) );
			$(this).attr('id', $(this).attr('id').replace(old_repeater_id, repeater_id) );
			
		});
		
		field.find('[name*="' + old_id + '"]').each(function()
		{	
			$(this).attr('name', $(this).attr('name').replace(old_id, new_id) );
			$(this).attr('name', $(this).attr('name').replace(old_repeater_id, repeater_id) );
			$(this).attr('repeater-data-id',repeater_id);

		});
		
	};
})(jQuery);

function repeaterTypeChange(o){
		// vars
		var select = $(o),
			tbody = select.closest('tbody'),
			field = tbody.closest('.another-field'),
			field_type = field.attr('data-type'),
			field_index = field.attr('data-id'),
			repeater_id = $(o).attr('repeater-data-id'),
			val = select.val();
		// update data atts
		field.removeClass('field_type-' + field_type).addClass('field_type-' + val);
		field.attr('data-type', val);
		// tab - override field_name
		if( val == 'tab' || val == 'message' )
		{
			tbody.find('tr.field_name input[type="text"]').val('').trigger('keyup');
		}
		if( tbody.children( 'tr.field_option_' + val ).length )
		{
			// hide + disable options
			tbody.children('tr.field_option').hide().find('[name]').attr('disabled', 'true');
			
			// show and enable options
			tbody.children( 'tr.field_option_' + val ).show().find('[name]').removeAttr('disabled');
		}else{
			// add loading gif
			var tr = $('<tr"><td class="label"></td><td><div class="glyphicon glyphicon-refresh"></div></td></tr>');
			
			// hide current options
			tbody.children('tr.field_option').hide().find('[name]').attr('disabled', 'true');
			// append tr
			tbody.children('tr.field_save').before(tr);
			
			var reqData = "field_type="+val+"&field_index="+field_index + "&repeater_index=" + repeater_id;
			jQuery.ajax( {
						url : "renderRepeaterOption",
						type : 'POST',
						enctype : 'multipart/form-data',
						datatype: 'json',
						data : reqData +"&ajax=true",
						success : function(responseData) {
							var data = eval(responseData);
							//$('#render_option').html(data.html);
							if( !data.html)
							{
								tr.remove();
								return;
							}
					
							tr.replaceWith(data.html);
					}
			});
		}
		var label = $(o).find('option[value="' + val + '"]').html();
		$(o).closest('.field').find('td.field-type').html(label);
	}

// remove when field when click btn delete
function deleteRepeaterField(Id,o){
		var _delete_repeaterId  = '#repeater_delete_' + Id;
		
		var $tr = $(o).closest('tr');
        $tr.attr('class','');
		var data_id = $tr.attr("data-id");
		
		var delete_id = "." + $(this).attr("delete-list-id");
		if(data_id !="" ){
			var strItems = $(_delete_repeaterId).val();
			if(strItems!="") strItems+= ",";
			strItems	+= data_id;
			$(_delete_repeaterId).val(strItems);
		}
		
	
		
		// vars
		var a = $(this),
			
			tbody = $tr.parent(),
			temp = $('<div style="height:' + $tr.height() + 'px"></div>');	
		// fade away
		$tr.animate({'left' : '50px', 'opacity' : 0}, 400, function(){
			$tr.before(temp);
			$tr.remove();

			// no more fields, show the message
			if( tbody.children('tr').length <= 1 )
			{
				temp.remove();
				
				/* fields.children('.no_fields_message').show(); */
			}
			else
			{
				temp.animate({'height' : 0 }, 400, function(){
					temp.remove();
				});
			}
		});

     update_order_numbers($tr.parent());

	};


function bindSortableForRepeater(repeaterId){
	
    var sortable = "#" + repeaterId + "  tbody";
    var tempData = [] ;
    $(sortable).sortable({
        placeholder: "ui-state-highlight",
        cursor: 'move',
        start: function(event, ui) {
            var $tr = ui.item ;

            ui.placeholder.height(ui.item.height());
            $tr.find('.ckeditor').each(function(){
                var name = $(this).attr('name');
                var id = $(this).attr('id');

                 if($.inArray( id, CKEDITOR.instances )){
                     tempData[id] = CKEDITOR.instances[id].getData();
                     $(this).val(tempData[id]);
                     CKEDITOR.instances[id].destroy(true);
                     CKEDITOR.remove(id);
                 }
            });
        },
        stop: function (event, ui) {
            var panel =   ui.item.parent();
            update_order_numbers(panel);
            ui.item.find('.dynamic-editor').each(function(){
                var id = $(this).attr('id');
                var value =  tempData[id];
                /* console.log(value); */
                if($.inArray( id, CKEDITOR.instances )){
                  var editor =   CKEDITOR.replace(id, dynamicEditorConfig);
                    editor.setData(value);
                }
            });
        }
    });
}


function initDynamicEditor(){
	/* console.log("initDynamicEditor"); */
    for(name in CKEDITOR.instances) {
            CKEDITOR.instances[name].destroy(true);

        }
	$('.ckeditor').each(function(i){

             var name = $(this).attr('name');
             var id = $(this).attr('id');
			if($(this).hasClass('dynamic-editor')){
				 if(!$(this).closest('tr').hasClass('repeater-template')) {
                     CKEDITOR.replace(id, dynamicEditorConfig);
                 }
			}else{
				 CKEDITOR.replace(name);
			}
    });
	
}

function location_add_group(o){
	// vars
	var parent = $(o).closest('div');
	var $group = parent.find('.location-group:last'),
		$group2 = $group.clone(),
		old_id = $group2.attr('data-id'),
		new_id = $("#index_group").val();
		new_id++;
	var new_index = "group_-" + new_id;	
	// update names
	$group2.find('[name]').each(function(){
		var name = $(this).attr('name');
		$(this).attr('name', name.replace(old_id, new_index) );
		
	});
	// update data-i
	$group2.attr( 'data-id', new_index );
	$("#index_group").val(new_id);
	// update h4
	$group2.find('h4').text( "or" );
	// remove all tr's except the first one
	$group2.find('tr:not(:first)').remove();
	// add tr
	$group.after( $group2 );
}
function location_add_rule(o){
	// vars
	var $tr = $(o).closest('tr');
	var $tr2 = $tr.clone(),
		old_id = $tr2.attr('data-id'),
		new_id = $("#index_item").val();
		new_id++;
	// update names
	var new_index = "rule_-" + new_id;
	$tr2.find('[name]').each(function(){
		var name = $(this).attr('name');
		$(this).attr('name', name.replace(old_id, new_index) );
	});
	
	$("#index_item").val(new_id);
	// update data-i
	$tr2.attr( 'data-id', new_index );
	// add tr
	$tr.after( $tr2 );
}
function location_remove(o){
	// vars
	var is_remove = true;
	var $tr = $(o).closest('tr');
	var count_group = $(".location-groups").find('.location-group').length;
	if(count_group == 1){
		var count_item = $(".location-group").find('tr').length;
		if(count_item == 1){
			is_remove = false;
		}
	}
	var siblings = $tr.siblings('tr').length;
	if(is_remove){
		if( siblings == 0 )
		{
			// remove group
			$group = $tr.closest('.location-group');
			updateDeleteLocation($group,"delete_group");
			$group.remove();
		}
		else
		{
			// remove item
			updateDeleteLocation($tr,"delete_item");
			$tr.remove();
		}
	}
}

function updateDeleteLocation(obj,deleteName){
	var data_id = obj.attr("data-id");
	if(data_id !="" ){
		var _delete_Id  = '#' + deleteName;
		var strItems = $(_delete_Id).val();
		if(strItems!="") strItems+= ",";
		strItems	+= data_id;
		$(_delete_Id).val(strItems);
	}
}

function changeLocationParameter(obj,value){
	var tr = $(obj).closest('tr');
	var selected = $(obj).val();
	var dropName = $(obj).attr("name");
	var reqData = "selected=" + selected + "&value=" + value + "&dropName=" + dropName ;
	jQuery.ajax( {
				url : "renderLocationDrop",
				type : 'POST',
				enctype : 'multipart/form-data',
				datatype: 'json',
				data : reqData +"&ajax=true",
				success : function(responseData) {
					var data = eval(responseData);
					tr.find('td.value').html(data.html);
			}
	});
}



