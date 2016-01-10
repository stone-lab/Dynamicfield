<?php

namespace Modules\Dynamicfield\Composers;

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
        $this->assignDynamicFieldsToPageObject($view);
    }

    /**
     * @param $view
     *
     * @return mixed
     */
    private function assignDynamicFieldsToPageObject($view)
    {
        $arrAllow = ['page', 'homepage',"en.blog.slug"];
        $data = $view->getData();
        $routName = Route::currentRouteName();

        if (in_array($routName, $arrAllow)) {
            $arrType = config('asgard.dynamicfield.config.entity-type');
            $arrType =  array_keys($arrType);
            if (count($data)) {
                foreach ($data as $item) {
                    if (is_object($item)) {
                        $className = get_class($item);
                        if (in_array($className, $arrType)) {
                            $locale = LaravelLocalization::getCurrentLocale();
                            $request = Request::all();
                            $dynamicFields = new DynamicFields($item, $locale);
                            $dynamicFields->init($request);
                            $fieldValues = $dynamicFields->getFieldValues($locale);
                            $view->with('dynamicfields', $fieldValues);
                        }
                    }
                }
            }
        }

        return $view;
    }
}
