<?php 
    $options = $field->getOptions();
?>
<div class="field-meta">
	
	<table class="table-field table">
		<tr>
			<td class="field-order col-md-3">
				<span class="circle">{{$index}} </span>
				{!! Form::hidden("field[$repeater_index][repeater][$index][order]",$index)  !!}
			</td>
			<td class="field-label col-md-3">
				<a class="btn-toggle" href="javascript:;">{{$options["label"]}}</a>
				<div class="row-options">
					<a class="btn-toggle" href="javascript:;">Edit</a>
					|
					<?php 
                        $field_id = $field->id;
                    ?>
					<a class="btn-delete-field" href="javascript:;" data-id={{ $field_id }} delete-list-id="repeaters-deleted">Delete</a>
				</div>
			</td>
			<td class="field-name col-md-3">
				{{$field->name}}
			</td>
			<td class="field-type col-md-3">
				{{$field->type}}
			</td>
		</tr>
	</table>
	
</div>

<div class="field_form_mask">
	<div class="field_form">
		{!! Form::hidden("field[$repeater_index][repeater][$index][id]", $field->id, ['class' => 'form-control slugify field-label']) !!}
		<table class="table-field table">
			<tr class="field-label">
				<td class="col-md-3 label-txt">
					{!! Form::label("field[$index][repeater][$index][label]", trans('dynamicfield::field.form.label')) !!}
					<p class="text-muted">{!! trans('dynamicfield::field.description.default.label') !!}</p>
				</td>
				<td class="col-md-9">
					{!! Form::text("field[$repeater_index][repeater][$index][label]",  $options["label"], ['class' => 'form-control slugify field-label', 'placeholder' => trans('dynamicfield::field.form.label')]) !!}
				</td>
			</tr>
			<tr class="field-name">
				<td class="col-md-3 label-txt">
					{!! Form::label("field[$repeater_index][repeater][$index][name]", trans('dynamicfield::field.form.name')) !!}
					<p class="text-muted">{!! trans('dynamicfield::field.description.default.name') !!}</p>
				</td>
				<td class="col-md-9">
					{!! Form::text("field[$repeater_index][repeater][$index][name]",  $field->name, ['class' => 'form-control slugify field-name', 'placeholder' => trans('dynamicfield::field.form.name')]) !!}
				</td>
			</tr>
			<tr>
				<td class="col-md-3 label-txt">
					{!! Form::label("field[$repeater_index][repeater][$index][instruction]", trans('dynamicfield::field.form.instruction')) !!}
					<p class="text-muted">{!! trans('dynamicfield::field.description.default.instruction') !!}</p>
				</td>
				<td class="col-md-9">
					{!! Form::text("field[$repeater_index][repeater][$index][instruction]", $options["instruction"], ['class' => 'form-control slugify field-label', 'placeholder' => trans('dynamicfield::field.form.instruction')]) !!}
				</td>
			</tr>
			<tr>
				<td class="col-md-3 label-txt">
					{!! Form::label("field[$repeater_index][repeater][$index][type]", trans('dynamicfield::field.form.type')) !!}
					<p class="text-muted">{!! trans('dynamicfield::field.description.default.type') !!}</p>
				</td>
				<td class="col-md-9">
					<?php 
                        $select_name =sprintf("field[%s][repeater][%s][type]", $repeater_index, $index);
                        $arrFields = Modules\Dynamicfield\Utility\Enum\Fields::getList();
                        unset($arrFields["repeater"]);
                    ?>
					
					{!! 
						Form::select($select_name, $arrFields,$field->type, array('class' => 'field-repeater-type form-control','onChange'=>'repeaterTypeChange(this)','repeater-data-id'=>$repeater_index));
					!!}
				</td>
			</tr>
			<tr>
				<?php 
                    $required_name =sprintf("field[%s][repeater][%s][required]", $repeater_index, $index);
                ?>
				<td class="col-md-3 label-txt">
					{!! Form::label("field[$repeater_index][repeater][$index][required]", trans('dynamicfield::field.form.required')) !!}
					<p class="text-muted">{!! trans('dynamicfield::field.description.default.required') !!}</p>
				</td>
				<td class="col-md-9">
					{!! Form::radio($required_name, 'true'); !!}<label>Yes</label>
					{!! Form::radio($required_name, 'false', true); !!}<label>No</label>
				</td>
			</tr>
			<?php
                $prefix_name =sprintf("field[%s][repeater][%s]", $repeater_index, $index);
                $strViewPath = 'dynamicfield::admin.group.partials.fields.' . $type ;
            ?>
			 @include($strViewPath)
			<tr class="field_save">
				<td class="col-md-3 label-txt"></td>
				<td class="col-md-9">
					<a class="btn btn-default btn-toggle" title="Close Field" href="javascript:;">{!! trans('dynamicfield::field.button.close_field') !!}</a>
				</td>
			</tr>
		</table>
		<!-- field_option start - default with option type is text-->
	</div>
	<script>
		
	</script>
</div>