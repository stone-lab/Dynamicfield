<tr class="field_option field_option_repeater">
	<td class="col-md-3 label-txt">
	</td>
	<td class="col-md-9">
	<div class="col-md-12 group-content">
		<div class="box box-body">
			{!! Form::hidden("repeatField[delete]", '', ['class' => 'form-control slugify items-deleted']) !!}
			<div class='form-group repeater-fields'>
				<table class="table-field">
					<thead>
						<tr>
							<th class="col-md-3">{{ trans('dynamicfield::field.table.field_order') }}</th>
							<th class="col-md-3">{{ trans('dynamicfield::field.table.field_label') }}</th>
							<th class="col-md-3">{{ trans('dynamicfield::field.table.field_name') }}</th>
							<th class="col-md-3">{{ trans('dynamicfield::field.table.field_type') }}</th>
						</tr>
					</thead>
				</table>
				
				<div class="field-data sortable">
					<?php $index = 1; if (count($fields)) :?>
					<?php foreach ($fields as $field): ?>
					<?php $prefixName = sprintf('field[%s][repeater]', $index);?>
					<div class="another-field form-title" data-type="text" data-id='{{$index}}'>
						@include('dynamicfield::admin.group.partials.fields.repeater.field', ['index' => $index,'type'=>$field->type,'field'=>$field, 'repeater_index'=>$repeaterIndex])
					</div>
					<?php ++$index;endforeach; ?>
					<?php endif;?>
					
				</div>
				<!-- fields container all field clone-->
				<div class="table_footer">
					<a class="btn btn-primary btn-add-field" data-toggle="modal"  repeater-data-id ="{{ $repeaterIndex }}"  onclick="appendField(event,this)">
						<i class="fa fa-pencil"></i> + {{ trans('dynamicfield::field.button.add_field') }}
					</a>
				</div>
			</div>
			<div class="more-files-template hidden" data-type="text" data-id='field_clone'>
				<?php 
                    $prefixName = sprintf('field[%s][repeater]', 'repeater_clone');
                    $field = new Modules\Dynamicfield\Entities\Field();
                    $field->id = -1;
                    $field->label = '';
                    $field->type = 'text';
                    $field->data = '{}';
                ?>
				
				@include('dynamicfield::admin.group.partials.fields.repeater.field', ['index' => 'field_clone','type'=>'text','field'=>$field, 'repeater_index'=>"repeater_clone"	])
			</div>
		</div>
	</div>
	</td>
</tr>