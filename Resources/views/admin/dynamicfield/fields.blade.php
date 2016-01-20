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

    $request = Request::all();
    $fields = new Modules\Dynamicfield\Utility\DynamicFields($entityDynamic, $lang);
    $fields->init($request);

    $htmlFields = $fields->renderFields($lang);
?>
<div id="advance_template_{!! $lang !!}">
	{!! $htmlFields !!}
</div>