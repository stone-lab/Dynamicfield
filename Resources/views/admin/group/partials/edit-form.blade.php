
	<div class="col-md-10 group-content">
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
					<?php $index = 1; if (count($fields)) :?>
					<?php foreach ($fields as $field): ?>
					<div class="another-field form-title" data-type="text" data-id='{{$index}}'>
						@include('dynamicfield::admin.group.partials.fields.field', ['index' => $index,'type'=>$field->type,'field'=>$field])
					</div>
					<?php ++$index;endforeach; ?>
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
                    $field->id = 'field_clone';
                    $field->label = '';
                    $field->type = 'text';
                    $field->data = '{}';
                ?>
				
				@include('dynamicfield::admin.group.partials.fields.field', ['index' => 'field_clone','type'=>'text','field'=>$field])
			</div>
			<!-- -->
			<div class="postbox" id="location">
				<h3 class="title"><span>Location</span></h3>
				<div class="inside">
					<table class="data-table table dataTable table-location">
						<tbody>
							<tr>
								<td class="rule-des">
									<label for="post_type">Rules</label>
									<p class="description">Create a set of rules to determine which edit screens will use these advanced custom fields</p>
								</td>
								<td>
                                    @include('dynamicfield::admin.group.partials.location.rule',['locations'=>@$locations])
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
			<div class="box-footer">
				<button type="submit" class="btn btn-primary btn-flat">{{ trans('core::core.button.update') }}</button>
			</div>
		</div>
		
	</div>
	<!--left content -->