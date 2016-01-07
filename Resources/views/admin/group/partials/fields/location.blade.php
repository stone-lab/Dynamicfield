<?php $prefix = sprintf($prefixName, $index); ?>
<tr class="field_option field_option_wysiwyg">
	<td class="col-md-3 label-txt">
		{!! Form::label($prefix . "[default_value]", trans('dynamicfield::field.form.default_value')) !!}
		<p class="text-muted">{!! trans('dynamicfield::field.description.text.default_value') !!}</p>
	</td>
	<td class="col-md-9">
		{!! Form::textarea($prefix . "[default_value]", $options["default_value"], ['class' => 'form-control slugify', 'placeholder' => trans('dynamicfield::field.form.default_value')]) !!}
	</td>
</tr>

<tr class="field_option field_option_wysiwyg">
	<td class="col-md-3 label-txt">
		{!! Form::label($prefix . "[toolbar]", trans('dynamicfield::field.form.toolbar')) !!}
		
	</td>
	<td class="col-md-9">
		{!! Form::text($prefix . "[toolbar]", $options["toolbar"], ['class' => 'form-control slugify', 'placeholder' => trans('dynamicfield::field.form.toolbar')]) !!}
	</td>
</tr>	
