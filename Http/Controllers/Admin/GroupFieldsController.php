<?php

namespace Modules\Dynamicfield\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Laracasts\Flash\Flash;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Modules\Core\Http\Controllers\Admin\AdminBaseController;
use Modules\Dynamicfield\Entities\Group;
use Modules\Dynamicfield\Http\Requests\GroupFieldRequest;
use Modules\Dynamicfield\Repositories\GroupRepository;
use Modules\Dynamicfield\Utility\DynamicFields;
use Modules\Page\Repositories\PageRepository;
use Modules\Page\Entities\Page;
use Request as BaseRequest;

class GroupFieldsController extends AdminBaseController
{
    /**
     * @var GroupRepository
     * @var PageRepository
     */
    private $group;
    private $page;

    public function __construct(GroupRepository $group, PageRepository $page)
    {
        parent::__construct();
        $this->group = $group;
        $this->page = $page;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $group = $this->group->all();

        return view('dynamicfield::admin.group.index', compact('group'));
    }

    /**
     * @param Group $group
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create(Group $group)
    {
        $fields = $group->getListFields();

        return view('dynamicfield::admin.group.edit', compact('group', 'fields'));
    }

    /**
     * @param GroupFieldRequest $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(GroupFieldRequest $request)
    {
        $data = $request->all();
        $data_group = $data['group'];
        $data_fields = $data['field'];
        $this->group->saveData($data_group, $data_fields);
        flash()->success(trans('core::core.messages.resource created', ['name' => trans('dynamicfield::dynamicfield.title.field_group')]));

        return redirect()->route('admin.dynamicfield.group.index');
    }

    /**
     * @param Group $group
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Group $group)
    {
        $fields = $group->getListFields();

        return view('dynamicfield::admin.group.edit', compact('group', 'fields'));
    }

    /**
     * @param Group   $group
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Group $group, Request $request)
    {
        $this->group->update($group, $request->all());
        flash()->success(trans('core::core.messages.resource updated', ['name' => trans('dynamicfield::dynamicfield.title.field_group')]));

        return redirect()->route('admin.dynamicfield.group.index');
    }

    /**
     * @param Group $group
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Group $group)
    {
        $this->group->destroy($group);
        flash()->success(trans('core::core.messages.resource deleted', ['name' => trans('dynamicfield::dynamicfield.title.field_group')]));

        return redirect()->route('admin.dynamicfield.group.index');
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function renderOption()
    {
        $type = $_REQUEST['field_type'];
        $index = $_REQUEST['field_index'];
        $repeater_index = $index;

        $optionClass = "Modules\Dynamicfield\Utility\Enum\Options\\".ucfirst($type);
        $options = $optionClass::getList();
        $prefix_name = 'field[%s]';
        $view_path = 'dynamicfield::admin.group.partials.fields.'.$type;
        $fields = array();
        $html = view($view_path, compact('index', 'options', 'fields', 'prefix_name', 'repeater_index'));

        return response()->json(['html' => $html->render()]);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function renderRepeaterOption()
    {
        $type = $_REQUEST['field_type'];
        $index = $_REQUEST['field_index'];
        $repeater_index = $_REQUEST['repeater_index'];

        $prefix_name = sprintf('field[%s][repeater][%s]', $repeater_index, $index);
        $optionClass = "Modules\Dynamicfield\Utility\Enum\Options\\".ucfirst($type);
        $options = $optionClass::getList();
        $view_path = 'dynamicfield::admin.group.partials.fields.'.$type;
        $html = view($view_path, compact('index', 'options', 'repeater_index', 'prefix_name'));

        return response()->json(['html' => $html->render()]);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function ajaxRender()
    {
        $this->assetPipeline->requireJs('ckeditor.js');
        $request = BaseRequest::all();
        $template = $request['template'];
        $id = $request['id'];

        $page = Page::firstOrNew(['id' => $id]);
        $page->template = $template;
        $advanceFields = new DynamicFields($page);
        $advanceFields->init();
        $locale = array_keys(LaravelLocalization::getSupportedLocales());
        $jsonData = array();
        foreach ($locale as $v) {
            $jsonData['html'][$v] = $advanceFields->renderFields($v);
        }
        $jsonData['locale'] = $locale;

        return response()->json($jsonData);
    }

    public function duplicate(Page $page)
    {
        $this->page->replicate($page);

        flash(trans('dynamicfield::messages.page.duplicate successful'));

        return redirect()->route('admin.page.page.index');
    }
}
