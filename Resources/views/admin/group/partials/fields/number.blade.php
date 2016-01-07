<?php $prefix = sprintf($prefixName, $index); ?>
<tr class="field_option field_option_number">
	<td class="col-md-3 label-txt">
		{!! Form::label($prefix . "[placeholder]", trans('dynamicfield::field.form.placeholder')) !!}
		<p class="text-muted">Appears within the input</p>
	</td>
	<td class="col-md-9">
		{!! Form::text($prefix . "[placeholder]", $options["placeholder"], ['class' => 'form-control slugify', 'placeholder' => trans('dynamicfield::field.form.placeholder')]) !!}
	</td>
</tr>
<tr class="field_option field_option_number">
	<td class="col-md-3 label-txt">
		{!! Form::label($prefix . "[default_value]", 'Default Value') !!}
		<p class="text-muted">Appears when creating a new page</p>
	</td>
	<td class="col-md-9">
		{!! Form::number($prefix . "[default_value]", $options["default_value"], ['class' => 'form-control slugify', 'placeholder' => trans('dynamicfield::field.form.default_value')]) !!}
	</td>
</tr>
<tr class="field_option field_option_number">
	<td class="col-md-3 label-txt">
		{!! Form::label($prefix . "[min_value]", 'Minimum Value') !!}
	</td>
	<td class="col-md-9">
		{!! Form::number($prefix . "[min_value]", $options["min_value"], ['class' => 'form-control slugify', 'placeholder' => trans('dynamicfield::field.form.min_value')]) !!}
	</td>
</tr>
<tr class="field_option field_option_number">
	<td class="col-md-3 label-txt">
		{!! Form::label($prefix . "[max_value]", 'Maximum Value') !!}
	</td>
	<td class="col-md-9">
		{!! Form::number($prefix . "[max_value]", $options["max_value"], ['class' => 'form-control slugify', 'placeholder' => trans('dynamicfield::field.form.max_value')]) !!}
	</td>
</tr>
