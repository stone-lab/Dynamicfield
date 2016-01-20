<?php

namespace Modules\Dynamicfield\Composers;

use Illuminate\Contracts\View\View;
use Modules\Dynamicfield\Utility\DynamicFields;

class DynamicfieldViewComposer
{
    /**
     * Assign dynamicField to view
     *
     * @param View $view
     */
    public function compose(View $view)
    {
        $this->assignDynamicFieldsToPageObject($view);
    }

    /**
     * Pass dynamicFields to view
     *
     * @param $view
     * @return mixed
     */
    private function assignDynamicFieldsToPageObject($view)
    {
        $entityDynamic  = null;
        $data    = $view->getData();
        //TODO Fix with event
        if (count($data)) {
            $arrType = config('asgard.dynamicfield.config.entity-type');
            $arrType =  array_keys($arrType);
            // edit entity
            foreach ($data as $item) {
                if (is_object($item)) {
                    $className = get_class($item);
                    if (in_array($className, $arrType)) {
                        $entityDynamic = $item;
                        break;
                    }
                }
            }
        }
        // initial model data for create new;
        if (is_null($entityDynamic)) {
            $router    = \Request::route()->getName();
            $arrCreateRouter    = config('asgard.dynamicfield.config.router');
            if (array_key_exists($router, $arrCreateRouter)) {
                $entityDynamic = new $arrCreateRouter[$router];
            }
        }
        $view->with('entityDynamic', $entityDynamic);

        return $view;
    }
}
