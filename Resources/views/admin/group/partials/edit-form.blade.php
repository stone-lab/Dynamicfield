
	<div class="col-md-9 group-content">
		<div class="box box-body">
			<h2>
				@if($group->id>0)
					{!! trans('dynamicfield::group.title.edit') !!}
				@else
					{!! trans('dynamicfield::group.title.create') !!}
				@endif	
			</h2>
			{!! Form::hidden("group[id]", isset($group->id)?$group->id:null, ['class' => 'form-control slugify field-label']) !!}
			<div class="form-group{{ $errors->has('group.name') ? ' has-error' : '' }}">
				{!! Form::text("group[name]", isset($group->name)?$group->name:null, ['class' => 'form-control slugify', 'placeholder' => trans('dynamicfield::group.form.name')]) !!}
				{!! $errors->first("group.name", '<span class="help-block">:message</span>') !!}
			</div>
			{!! Form::hidden("group[delete]", '', ['class' => 'form-control slugify fields-deleted']) !!}
			{!! Form::hidden("group[delete_repeater]", '', ['class' => 'form-control slugify repeaters-deleted']) !!}
			<div class='form-group field_infor'>
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

				<div class="field-data sortable ">
					<?php $index=1; if (count($fields)) :?>
					<?php foreach ($fields as $field): ?>
					<div class="another-field form-title" data-type="text" data-id='{{$index}}'>
						@include('dynamicfield::admin.group.partials.fields.field', ['index' => $index,'type'=>$field->type,'field'=>$field])
					</div>
					<?php 
                        $index++;endforeach;
                    ?>
					<?php endif;?>
					
				</div>
			
				<!-- fields container all field clone-->
				<div class="table_footer">
					<a class="btn btn-primary btn-add-field" data-toggle="modal"   onclick="appendField(event,this)">
						<i class="fa fa-pencil"></i> + {{ trans('dynamicfield::field.button.add_field') }}
					</a>
				</div>
			</div>
			<div class="more-files-template hidden" data-type="text" data-id='field_clone'>
				<?php 
                    $field = new Modules\Dynamicfield\Entities\Field();
                    $field->id = "field_clone";
                    $field->label = "";
                    $field->type = "text";
                    $field->data = "{}";
                ?>
				
				@include('dynamicfield::admin.group.partials.fields.field', ['index' => 'field_clone','type'=>'text','field'=>$field])
			</div>
		</div>
	</div>
	<!--left content -->
	<div class="col-md-3">
		<div class="box box-primary">
			<div class="box-body">
				<div class="form-group{{ $errors->has('group.template') ? ' has-error' : '' }}">
					{!! Form::label("group[template]", trans('page::pages.form.template')) !!}
					{!! Form::select("group[template]", $all_templates, $group->template, ['class' => "form-control selectpicker", 'placeholder' => trans('page::pages.form.template')]) !!}	
					{!! $errors->first("group.template", '<span class="help-block">:message</span>') !!}
				</div>
				<button type="submit" class="btn btn-primary btn-flat">{{ trans('core::core.button.update') }}</button>
			</div>
		</div>
	</div>
	<!--right content -->