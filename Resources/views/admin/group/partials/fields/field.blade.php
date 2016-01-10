<?php $options = $field->getOptions(); ?>
<?php 
    if (count($options)):
?>
<div class="field-meta">
	<table class="table-field table">
		<tr>
			<td class="field-order col-md-3">
				<span class="circle">{{$index}}</span>
				{!! Form::hidden("field[$index][order]",$index)  !!}
			</td>
			<td class="field-label col-md-3">
				<a class="btn-toggle" href="javascript:;">{{$options["label"]}}</a>
				<div class="row-options">
					<a class="btn-toggle" href="javascript:;">Edit</a>
					|
					<?php $fieldId = $field->id; ?>
					<a class="btn-delete-field" href="javascript:;" data-id={{ $fieldId }} delete-list-id="fields-deleted" >Delete</a>
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
		{!! Form::hidden("field[$index][id]", $field->id, ['class' => 'form-control slugify field-label']) !!}
		<table class="table-field table">
			<tr class="field-label">
				<td class="col-md-3 label-txt">
					{!! Form::label("field[$index][label]", trans('dynamicfield::field.form.label')) !!}
					<p class="text-muted">{!! trans('dynamicfield::field.description.default.label') !!}</p>
				</td>
				<td class="col-md-9">
					{!! Form::text("field[$index][label]",  $options["label"], ['class' => 'form-control slugify field-label', 'placeholder' => trans('dynamicfield::field.form.label')]) !!}
				</td>
			</tr>
			<tr class="field-name">
				<td class="col-md-3 label-txt">
					{!! Form::label("field[$index][name]", trans('dynamicfield::field.form.name')) !!}
					<p class="text-muted">{!! trans('dynamicfield::field.description.default.name') !!}</p>
				</td>
				<td class="col-md-9">
					{!! Form::text("field[$index][name]",  $field->name, ['class' => 'form-control slugify field-name', 'placeholder' => trans('dynamicfield::field.form.name')]) !!}
				</td>
			</tr>
			<tr>
				<td class="col-md-3 label-txt">
					{!! Form::label("field[$index][instruction]", trans('dynamicfield::field.form.instruction')) !!}
					<p class="text-muted">{!! trans('dynamicfield::field.description.default.instruction') !!}</p>
				</td>
				<td class="col-md-9">
					{!! Form::text("field[$index][instruction]", $options["instruction"], ['class' => 'form-control slugify field-label', 'placeholder' => trans('dynamicfield::field.form.instruction')]) !!}
				</td>
			</tr>
			<tr>
				<td class="col-md-3 label-txt">
					{!! Form::label("field[$index][type]", trans('dynamicfield::field.form.type')) !!}
					<p class="text-muted">{!! trans('dynamicfield::field.description.default.type') !!}</p>
				</td>
				<td class="col-md-9">
					<?php 
                        $selectName = sprintf('field[%s][type]', $index);
                        $arrFields = Modules\Dynamicfield\Utility\Enum\Fields::getList();
                    ?>
					{!! 
						Form::select($selectName, $arrFields,$field->type, array('class' => 'field-type form-control'));
					!!}
				</td>
			</tr>
			<tr>
				<?php 
                    $requiredName = sprintf('field[%s][required]', $index);
                ?>
				<td class="col-md-3 label-txt">
					{!! Form::label("field[$index][required]", trans('dynamicfield::field.form.required')) !!}
					<p class="text-muted">{!! trans('dynamicfield::field.description.default.required') !!}</p>
				</td>
				<td class="col-md-9">
					{!! Form::radio($requiredName, 'true'); !!}<label>Yes</label>
					{!! Form::radio($requiredName, 'false', true); !!}<label>No</label>
				</td>
			</tr>
			<?php
                $prefixName = 'field[%s]';
                $strViewPath = 'dynamicfield::admin.group.partials.fields.' . $type;
                if ($type == 'repeater') {
                    $fields = $field->getListFields();
                    $repeaterIndex = $index;
                }
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
</div>
<?php endif; ?>