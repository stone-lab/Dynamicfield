<?php $_prefix = sprintf($prefix_name, $index); ?>
<tr class="field_option field_option_text">
	<td class="col-md-3 label-txt">
		{!! Form::label($_prefix . "[default_value]", trans('dynamicfield::field.form.default_value')) !!}
		<p class="text-muted">{!! trans('dynamicfield::field.description.text.default_value') !!}</p>
	</td>
	<td class="col-md-9">
		{!! Form::text($_prefix . "[default_value]", $options["default_value"], ['class' => 'form-control slugify', 'placeholder' => trans('dynamicfield::field.form.default_value')]) !!}
	</td>
</tr>

<tr class="field_option field_option_text">
	<td class="col-md-3 label-txt">
		{!! Form::label($_prefix . "[placeholder]", trans('dynamicfield::field.form.placeholder')) !!}
		<p class="text-muted">{!! trans('dynamicfield::field.description.text.placeholder') !!}</p>
	</td>
	<td class="col-md-9">
		{!! Form::text($_prefix . "[placeholder]", $options["placeholder"], ['class' => 'form-control slugify', 'placeholder' => trans('dynamicfield::field.form.placeholder')]) !!}
	</td>
</tr>

<tr class="field_option field_option_text">
	<td class="col-md-3 label-txt">
		{!! Form::label($_prefix . "[limit]", 'Character Limit') !!}
		<p class="text-muted">{!! trans('dynamicfield::field.description.text.limit') !!}</p>
	</td>
	<td class="col-md-9">
		{!! Form::text($_prefix . "[limit]", $options["limit"], ['class' => 'form-control slugify', 'placeholder' => trans('dynamicfield::field.form.limit')]) !!}
	</td>
</tr>