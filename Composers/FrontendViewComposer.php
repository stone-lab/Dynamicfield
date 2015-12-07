<?php namespace Modules\Dynamicfield\Composers;

use Illuminate\Contracts\View\View;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Modules\Dynamicfield\Utility\DynamicFields;
use Request;
use Route;

class FrontendViewComposer
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
        $arrAllow = ['page','homepage'] ;
        $data = $view->getData();
        $routName = Route::currentRouteName();
        if (in_array($routName, $arrAllow)) {
            if (count($data)) {
                foreach ($data as $item) {
                    if (is_a($item, 'Modules\Page\Entities\Page')) {
                        $locale  = LaravelLocalization::getCurrentLocale();
                        $request = Request::all();

                        $dynamicFields = new DynamicFields($item, $locale);
                        $dynamicFields->init($request);
                        $fieldValues = $dynamicFields->getFieldValues($locale);

                        $view->with('dynamicfields',    $fieldValues);
                    }
                }
            }
        }

        return $view;
    }
}
