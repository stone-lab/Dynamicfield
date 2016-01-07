<?php

namespace Modules\Dynamicfield\Composers;

use Illuminate\Contracts\View\View;
use Modules\Dynamicfield\Utility\DynamicFields;
use Request;

class DynamicfieldViewComposer
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
        $data    = $view->getData();
        //TODO Fix with event
        if (count($data)) {
            $arrType = config('asgard.dynamicfield.config.entity-type');
            $arrType =  array_keys($arrType);
            $entity  = new \stdClass();
            $entity->id = null;
            $entity->template = null;
            // edit entity
            foreach ($data as $item) {
                if (is_object($item)) {
                    $className = get_class($item);
                    if (in_array($className, $arrType)) {
                        $entity = $item;
                        break;
                    }
                }
            }
            // create new entity
            $router    = \Request::route()->getName();
            $arrRouter    = config('asgard.dynamicfield.config.router');
            if (array_key_exists($router, $arrRouter)) {
                $entity = new $arrRouter[$router];
            }
            
            $request    = Request::all();
            $fields    = new DynamicFields($entity);
            $fields->init($request);
            $view->with('dynamicfield', $fields);
            $templateId = 'template';
            $entityType = get_class($entity);
            $entityId = @$entity->id ? $entity->id : '0';
            $view->nest('dynamicFieldScript', 'dynamicfield::admin.dynamicfield.script', compact('templateId', 'entityId', 'entityType'))
                ->nest('dynamicFieldCss', 'dynamicfield::admin.dynamicfield.style');
        }

        return $view;
    }
}
