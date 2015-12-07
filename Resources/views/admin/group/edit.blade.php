@extends('layouts.master')

@section('content-header')
    <h1>
        {{ trans('dynamicfield::group.title.field_group') }}
    </h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('dashboard.index') }}"><i class="fa fa-dashboard"></i> {{ trans('core::core.breadcrumb.home') }}</a></li>
        <li class="active">{{ trans('dynamicfield::group.title.field_group') }}</li>
    </ol>
@stop

@section('styles')
    {!! Theme::script('js/vendor/ckeditor/ckeditor.js') !!}
    {!! Theme::style('css/vendor/iCheck/flat/blue.css') !!}
	
	<link href="{{{ Module::asset('dynamicfield:css/jquery-ui.min.css') }}}" rel="stylesheet" type="text/css" />
	<link href="{{{ Module::asset('dynamicfield:css/styles.css') }}}" rel="stylesheet" type="text/css" />

@stop

@section('content')
    {!! Form::open(['route' => ['admin.dynamicfield.group.store'], 'method' => 'post']) !!}
    <div class="row">
        <div class="col-md-12">
			@include('dynamicfield::admin.group.partials.edit-form', ['fields' => $fields,'group'=>$group])
        </div>
    </div>
    {!! Form::close() !!}
@stop

@section('footer')
    <a data-toggle="modal" data-target="#keyboardShortcutsModal"><i class="fa fa-keyboard-o"></i></a> &nbsp;
@stop
@section('shortcuts')
    <dl class="dl-horizontal">
        <dt><code>b</code></dt>
        <dd>{{ trans('core::core.back to index') }}</dd>
    </dl>
@stop

@section('scripts')
	<script src="{{{ Module::asset('dynamicfield:js/jquery-ui.min.js') }}}"></script>
	<script src="{{{ Module::asset('dynamicfield:js/custom.js') }}}"></script>
	<script src="{{{ Module::asset('dynamicfield:js/sortable.js') }}}"></script>
  
    <script>
        $( document ).ready(function() {
            $('input[type="checkbox"].flat-blue, input[type="radio"].flat-blue').iCheck({
                checkboxClass: 'icheckbox_flat-blue',
                radioClass: 'iradio_flat-blue'
            });

            $('input[type="checkbox"]').on('ifChecked', function(){
                $(this).parent().find('input[type=hidden]').remove();
            });

            $('input[type="checkbox"]').on('ifUnchecked', function(){
                var name = $(this).attr('name'),
                    input = '<input type="hidden" name="' + name + '" value="0" />';
                $(this).parent().append(input);
            });
        });
    </script>
@stop
