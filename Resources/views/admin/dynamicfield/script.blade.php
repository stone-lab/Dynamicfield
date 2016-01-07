<script type="text/javascript">
	<?php 
        $entityType = str_replace('\\', "\\\\", $entityType);
    ?>
	$(document).ready(function() {
		DynamicFields.init('<?= route('admin.dynamicfield.group.renderControl') ?>',{{ $entityId }},'{{$templateId}}','{{ $entityType}}');
		initDynamicEditor();
	})

</script>			
