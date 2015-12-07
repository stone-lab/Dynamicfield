<?php namespace Modules\Dynamicfield\Composers;

use Illuminate\Contracts\View\View;
use Modules\Dynamicfield\Utility\DynamicFields;
use Modules\Page\Entities\Page;
use Request;
use Route;

class DynamicfieldViewComposer
{
    /**
     * @param View $view
     */
    public function compose(View $view)
    {
        $currentRoute = Route::current();
        $view = $this->_assignDynamicFieldsToPageObject($view);
    }

    /**
     * @param $view
     * @return mixed
     */
    private function _assignDynamicFieldsToPageObject($view)
    {
        $data = $view->getData();

        //TODO Fix with event
        if (count($data)) {
            $page = new Page();
            foreach ($data as $item) {
                if (is_a($item, 'Modules\Page\Entities\Page')) {
                    $page = $item;
                    break;
                }
            }
            $request = Request::all();
            $fields = new DynamicFields($page);
            $fields->init($request);
            $view->with('dynamicfield', $fields);
            //print_r($fields); exit;
            $templateId = 'template';
            $pageId    =  $page->id?$page->id:"0";
            $view->nest('dynamicFieldScript', 'dynamicfield::admin.dynamicfield.script', compact('templateId', 'pageId'))
                ->nest('dynamicFieldCss', 'dynamicfield::admin.dynamicfield.style');
        }

        return $view;
    }
}
