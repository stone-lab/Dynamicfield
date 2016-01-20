<link  media="all" type="text/css" rel="stylesheet"  href="{{{ Module::asset('dynamicfield:css/jquery-ui.min.css') }}}"/>
<link  media="all" type="text/css" rel="stylesheet"  href="{{{ Module::asset('dynamicfield:css/styles.css') }}}"/>
@section('scripts')
	@parent
	<script src="{!! Module::asset('dynamicfield:js/jquery-ui.min.js') !!}"></script>
	<script src="{!! Module::asset('dynamicfield:js/dynamic-fields.js') !!}"></script>
	<script src="{!! Module::asset('dynamicfield:js/custom.js') !!}"></script>
@stop
<script type="text/javascript">
	<?php 
            // assign $entityType from template file ;
            $entityDynamic = null;
            $arrType = config('asgard.dynamicfield.config.entity-type');

            if (isset($$entityType)) {
                $entityDynamic = $$entityType ;
            }
            if (is_null($entityDynamic)) {
                foreach ($arrType as $modelClass=>$type) {
                    if ($entityType == $type) {
                        $entityDynamic = new $modelClass;
                    }
                }
            }
            // assign $entityType from template file ;
            $templateId = 'template';
            $entityType = get_class($entityDynamic);
            $entityId = @$entityDynamic->id ? $entityDynamic->id : '0';
            $entityType = str_replace('\\', '\\\\', $entityType);
    ?>
	$(document).ready(function() {
		DynamicFields.init('<?= route('admin.dynamicfield.group.renderControl') ?>',{{ $entityId }},'{{$templateId}}','{{ $entityType}}');
		initDynamicEditor();
	})

</script>			
