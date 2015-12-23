var DynamicFields = {
		
	    advanceUrl: "",
	    pageId: 0,
	    templateId: "template",
	    advanceFieldId: "#advance_template_",
	    init: function(advanceUrl,pageId,templateId) {
	    	this.advanceUrl = advanceUrl ;
	    	this.pageId = pageId;
	    	this.templateId = templateId ;

			this.changeTemplete();

	    },
		 changeTemplete:function(){
			
				var templateId  = "#" + this.templateId  ; 
				
				$(templateId).bind('change',function(event){
					var template = $(this).val();
					DynamicFields.renderProcess(template);

				});

	    },
	    renderProcess:function(template){
				var reqData = "id="+this.pageId + "&"+"template="+template ;
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
									var editor_class = id  + ' .form-group .ckeditor' ;
									$(editor_class).each(function(i){
											 var name = $(this).attr('name');
											 CKEDITOR.replace(name);
									});
								/********************************/
							}
							
					}
				});
			
	    },
	   
	    
	       
}

