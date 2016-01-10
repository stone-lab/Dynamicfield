<?php

namespace Modules\Dynamicfield\Http\Controllers\Admin;

use Collective\Html\FormFacade;
use Illuminate\Http\Request;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Modules\Core\Http\Controllers\Admin\AdminBaseController;
use Modules\Dynamicfield\Entities\Group;
use Modules\Dynamicfield\Entities\Rule;
use Modules\Dynamicfield\Http\Requests\GroupFieldRequest;
use Modules\Dynamicfield\Repositories\GroupRepository;
use Modules\Dynamicfield\Utility\DynamicFields;
use Modules\Dynamicfield\Utility\Template;
use Modules\Page\Entities\Page;
use Modules\Page\Repositories\PageRepository;
use Request as BaseRequest;

class GroupFieldsController extends AdminBaseController
{
    /**
     * @var GroupRepository
     * @var PageRepository
     * @var RoleRepository
     */
    private $group;
    private $page;
    private $template;

    public function __construct(GroupRepository $group, PageRepository $page, Template $template)
    {
        parent::__construct();
        $this->group = $group;
        $this->page = $page;
        $this->template = $template;
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
        $dataGroup = $data['group'];
        $dataFields = $data['field'];
        // reset key of array location
        $dataLocations     = array_values($data["location"]);
        $newDataLocations = array_map('array_values', $dataLocations);

        $bResult = $this->group->saveData($dataGroup, $dataFields, $newDataLocations);
        $groupId = @$data["group"]["id"];
        $tran_core = 'core::core.messages.resource created';
        if ($groupId > 0) {
            $tran_core = 'core::core.messages.resource updated';
        }
        if ($bResult) {
            flash()->success(trans($tran_core, ['name' => trans('dynamicfield::dynamicfield.title.field_group')]));
        }

        return redirect()->route('admin.dynamicfield.group.index');
    }

    /**
     * @param Group $group
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Group $group)
    {
        $fields     = $group->getListFields();
        $locations  = Rule::where('group_id', '=', $group->id)->get();

        return view('dynamicfield::admin.group.edit', compact('group', 'fields', 'locations'));
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
        $repeaterIndex = $index;

        $optionClass = "Modules\Dynamicfield\Utility\Enum\Options\\" . ucfirst($type);
        $options = $optionClass::getList();
        $prefixName = 'field[%s]';
        $viewPath = 'dynamicfield::admin.group.partials.fields.' . $type;
        $fields = array();
        $html = view($viewPath, compact('index', 'options', 'fields', 'prefixName', 'repeaterIndex'));

        return response()->json(['html' => $html->render()]);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function renderRepeaterOption()
    {
        $type = $_REQUEST['field_type'];
        $index = $_REQUEST['field_index'];
        $repeaterIndex = $_REQUEST['repeater_index'];

        $prefixName = sprintf('field[%s][repeater][%s]', $repeaterIndex, $index);
        $optionClass = "Modules\Dynamicfield\Utility\Enum\Options\\" . ucfirst($type);
        $options = $optionClass::getList();
        $viewPath = 'dynamicfield::admin.group.partials.fields.' . $type;
        $html = view($viewPath, compact('index', 'options', 'repeaterIndex', 'prefixName'));

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
        $entityType = $request['entity_type'];
        $id = $request['id'];
        $entity = new $entityType();
        if (get_class($entity) == "stdClass") {
            $entity->id = null;
        } else {
            $entity = $entity->firstOrNew(['id' => $id]);
        }
        $entity->template = $template;
        $advanceFields = new DynamicFields($entity);
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

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function renderLocationDrop()
    {
        $selected   = $_REQUEST['selected'];
        $dropName    = $_REQUEST['dropName'];
        $value      = null;
        if ($_REQUEST['value'] != "undefined") {
            $value    = $_REQUEST['value'];
        }
        $name       = str_replace("parameter", "value", $dropName);
        $arrData   = array();
        switch ($selected) {
            case "type":
                $arrData = config('asgard.dynamicfield.config.entity-type');
                break;
            case "template":
                $arrData    = $this->template->getTemplates();
                break;
        }
        $html = FormFacade::select($name, $arrData, $value, ['class' => "form-control"]);

        return response()->json(['html' => $html]);
    }
}
