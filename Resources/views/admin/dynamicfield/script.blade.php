<script type="text/javascript">
	$(document).ready(function() {
		DynamicFields.init('<?= route('admin.dynamicfield.group.renderControl') ?>',{{ $pageId }},'{{$templateId}}');
		initDynamicEditor();
	})

</script>			
