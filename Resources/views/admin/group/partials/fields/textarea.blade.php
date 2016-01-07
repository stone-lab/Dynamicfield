<?php $prefix = sprintf($prefixName, $index); ?>
<tr class="field_option field_option_textarea">
	<td class="col-md-3 label-txt">
		{!! Form::label($prefix . "[default_value]", trans('dynamicfield::field.form.default_value')) !!}
		<p class="text-muted">Appears when creating a new post</p>
	</td>
	<td class="col-md-9">
		{!! Form::textarea($prefix . "[default_value]", $options["default_value"], ['class' => 'form-control slugify', 'placeholder' => trans('dynamicfield::field.form.default_value')]) !!}
	</td>
</tr>
<tr class="field_option field_option_textarea">
	<td class="col-md-3 label-txt">
		{!! Form::label($prefix . "[placeholder]", 'Placeholder') !!}
		<p class="text-muted">Appears when creating a new post</p>
	</td>
	<td class="col-md-9">
		{!! Form::textarea($prefix . "[placeholder]", $options["placeholder"], ['class' => 'form-control slugify', 'placeholder' => trans('dynamicfield::field.form.placeholder')]) !!}
	</td>
</tr>
<tr class="field_option field_option_textarea">
	<td class="col-md-3 label-txt">
		{!! Form::label($prefix . "[rows]", 'Rows') !!}
		<p class="text-muted">Sets the textarea height</p>
	</td>
	<td class="col-md-9">
		{!! Form::number($prefix . "[rows]", $options["rows"], ['class' => 'form-control slugify', 'placeholder' => trans('dynamicfield::field.form.rows')]) !!}
	</td>
</tr>	
