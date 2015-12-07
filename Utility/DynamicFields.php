<?php namespace Modules\Dynamicfield\Utility;

use Illuminate\View\View ;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class DynamicFields
{
    private $_field_type;
    protected $entities;
    public static $test_index =1;
    private $request = null;
    public function __construct($page, $locale =null)
    {
        $this->locale = $locale;
        $this->page = $page ;
    }
    public function init($default=null)
    {
        $this->request    =  $default;
        $locale            = $this->locale;
        $page            = $this->page;

        if (isset($locale)) {
            $entity = new Entity($page->id, $page->template, $locale);
            $entity->init($default);
            $this->entities[$locale] = $entity;
        } else {
            $languages = LaravelLocalization::getSupportedLocales() ;
            foreach ($languages as $locale=>$code) {
                $entity = new Entity($page->id, $page->template, $locale);

                $entity->init($default);

                $this->entities[$locale] = $entity;
            }
        }
        self::$test_index++;
    }
    public function render($locale)
    {
        $htmlFields = $this->renderFields($locale);
        $html = view('dynamicfield::admin.dynamicfield.fields', compact('locale', 'htmlFields'))->render();

        return $html ;
    }
    public function renderFields($locale, $request = null)
    {
        $html = "";

        $entity = $this->entities[$locale];

        if (isset($this->request)) {
            $entity->valid();
        }
        $html = $entity->render();

        return $html;
    }
    public function getFieldValue($fieldName, $locale="en")
    {
        $strValue  = "";
        $entity  = $this->entities[$locale];
        $values    = $entity->values();
        if (count($values)) {
            $keys = array_keys($values);
            if (in_array($fieldName, $keys)) {
                $strValue  = $values[$fieldName] ;
            }
        }

        return $strValue  ;
    }
    public function getFieldValues($locale="en")
    {
        $entity  = $this->entities[$locale];
        $values    = $entity->values();

        return $values  ;
    }
    public function valid()
    {
        $isValid = true;

        foreach ($this->entities as $entity) {
            $isValid = $entity->valid();
            if (!$isValid) {
                break;
            }
        }

        return $isValid;
    }
    public function save()
    {
        $isSave = true;
        foreach ($this->entities as $entity) {
            $isSave = $entity->save();
            if (!$isSave) {
                break;
            }
        }

        return $isSave;
    }
}
