<?php

use Illuminate\Routing\Router;

/** @var Router $router */
  $router->bind('group', function ($id) {
            return app('Modules\Dynamicfield\Repositories\GroupRepository')->find($id);
        });
		
$router->bind('page', function ($id) {
    return app(\Modules\Page\Repositories\PageRepository::class)->find($id);
});

$router->group(['prefix' =>'/dynamicfield'], function (Router $router) {

        $router->resource('group', 'GroupFieldsController', ['except' => ['show'], 'names' => [
            'index' => 'admin.dynamicfield.group.index',
            'create' => 'admin.dynamicfield.group.create',
            'store' => 'admin.dynamicfield.group.store',
            'update' => 'admin.dynamicfield.group.update',
            'destroy' => 'admin.dynamicfield.group.destroy',
        ]]);
    get('group/edit/{group}', ['as' => 'admin.dynamicfield.group.edit', 'uses' => 'GroupFieldsController@edit']);
// append
    post('group/renderOption', ['as' => 'admin.dynamicfield.group.renderOption', 'uses' => 'GroupFieldsController@renderOption']);
    post('group/renderRepeaterOption', ['as' => 'admin.dynamicfield.group.renderRepeaterOption', 'uses' => 'GroupFieldsController@renderRepeaterOption']);
    post('group/edit/renderOption', ['as' => 'admin.dynamicfield.group.renderOption', 'uses' => 'GroupFieldsController@renderOption']);
    post('group/edit/renderRepeaterOption', ['as' => 'admin.dynamicfield.group.renderRepeaterOption', 'uses' => 'GroupFieldsController@renderRepeaterOption']);
    post('group/renderControl', ['as' => 'admin.dynamicfield.group.renderControl', 'uses' => 'GroupFieldsController@ajaxRender']);
    
	post('media/link', ['as' => 'admin.dynamicfield.media.linkMedia', 'uses' => 'MediaController@linkMedia']);
});
