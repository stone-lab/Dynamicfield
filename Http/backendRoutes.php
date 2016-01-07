<?php

use Illuminate\Routing\Router;

$router->bind('group', function ($id) {
    return app('Modules\Dynamicfield\Repositories\GroupRepository')->find($id);
});
$router->bind('page', function ($id) {
    return app('Modules\Page\Repositories\PageRepository')->find($id);
});
$router->group(['prefix' => '/dynamicfield'], function (Router $router) {
    $router->resource('group', 'GroupFieldsController', ['except' => ['show'], 'names' => [
        'index' => 'admin.dynamicfield.group.index',
        'create' => 'admin.dynamicfield.group.create',
        'store' => 'admin.dynamicfield.group.store',
        'update' => 'admin.dynamicfield.group.update',
        'destroy' => 'admin.dynamicfield.group.destroy',
    ]]);
    get('group/edit/{group}', ['as' => 'admin.dynamicfield.group.edit', 'uses' => 'GroupFieldsController@edit']);
    post('group/renderOption', ['as' => 'admin.dynamicfield.group.renderOption', 'uses' => 'GroupFieldsController@renderOption']);
    post('group/renderRepeaterOption', ['as' => 'admin.dynamicfield.group.renderRepeaterOption', 'uses' => 'GroupFieldsController@renderRepeaterOption']);
    post('group/edit/renderOption', ['as' => 'admin.dynamicfield.group.renderOption', 'uses' => 'GroupFieldsController@renderOption']);
    post('group/edit/renderRepeaterOption', ['as' => 'admin.dynamicfield.group.renderRepeaterOption', 'uses' => 'GroupFieldsController@renderRepeaterOption']);
    post('group/renderControl', ['as' => 'admin.dynamicfield.group.renderControl', 'uses' => 'GroupFieldsController@ajaxRender']);
    post('group/edit/renderLocationDrop', ['as' => 'admin.dynamicfield.group.renderLocationDrop', 'uses' => 'GroupFieldsController@renderLocationDrop']);
    post('group/renderLocationDrop', ['as' => 'admin.dynamicfield.group.renderLocationDrop', 'uses' => 'GroupFieldsController@renderLocationDrop']);

    post('media/link', ['as' => 'admin.dynamicfield.media.linkMedia', 'uses' => 'MediaController@linkMedia']);
    get('page/{page}/duplicate', ['as' => 'admin.dynamicfield.page.duplicate', 'uses' => 'GroupFieldsController@duplicate']);

});
