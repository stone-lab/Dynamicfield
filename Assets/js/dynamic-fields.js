var DynamicFields = {
		
	    advanceUrl: "",
	    pageId: 0,
	    templateId: "template",
	    entityType: "",
	    advanceFieldId: "#advance_template_",
	    init: function(advanceUrl,pageId,templateId,entityType) {
	    	this.advanceUrl = advanceUrl ;
	    	this.pageId = pageId;
	    	this.templateId = templateId ;
	    	this.entityType = entityType ;
			this.changeTemplete();

	    },
		 changeTemplete:function(){
			
				var templateId  = "#" + this.templateId  ; 
				/* var selected = $(templateId).val();
					DynamicFields.renderProcess(selected); */
				$(templateId).bind('change',function(event){
					var template = $(this).val();
					DynamicFields.renderProcess(template);

				});

	    },
		// fixing ajax editor when change template
		refreshEditor:function(editor_class){
			$(editor_class + " .ckeditor").each(function(i){
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
		},
	    renderProcess:function(template){
				var reqData = "id="+this.pageId + "&"+"template="+template + "&"+ "entity_type=" + this.entityType;
				jQuery.ajax( {
						url : this.advanceUrl,
						type : 'POST',
						enctype : 'multipart/form-data',
						datatype: 'json',
						data : reqData +"&ajax=true",
						success : function(responseData) {
							var data = eval(responseData);
							var locale = data.locale;
							for(var i=0;i<locale.length;i++){
								var lang =  locale[i] ;
								var id = DynamicFields.advanceFieldId + lang;
								var htmlContent  = eval("data.html." + lang);
								$(id).html(htmlContent);
								/********************************/
									var editor_class = id  ;
									DynamicFields.refreshEditor(editor_class);
								/********************************/
							}
							
					}
				});
			
	    },
	   
	    
	       
}

