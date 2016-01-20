<?php

namespace Modules\Dynamicfield\Utility;

use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class DynamicFields
{
    protected $entities;
    private $request = null;
    private $locale;
    private $entity;
    private $type;

    public function __construct($entity, $locale = null)
    {
        $this->locale = $locale;
        $this->entity = $entity;
        $this->type = get_class($entity);
    }

    /**
     * Set entity data.
     *
     * @param null $default
     */
    public function init($default = null)
    {
        $this->request = $default;
        $locale = $this->locale;
        $entityItem = $this->entity;
        $type = $this->type;

        if (isset($locale)) {
            $entity = new Entity($entityItem->id, $entityItem->template, $locale, $type);
            $entity->init($default);
            $this->entities[$locale] = $entity;
        } else {
            $languages = LaravelLocalization::getSupportedLocales();
            foreach ($languages as $locale => $code) {
                $entity = new Entity(@$entityItem->id, @$entityItem->template, $locale, $type);
                $entity->init($default);
                $this->entities[$locale] = $entity;
            }
        }
    }

    /**
     * Render by locale.
     *
     * @param $locale
     *
     * @return mixed
     */
    public function renderFields($locale)
    {
        $entity = $this->entities[$locale];
        if (isset($this->request)) {
            $entity->valid();
        }

        $html = $entity->render();

        return $html;
    }

    /**
     * Get field data.
     *
     * @param $fieldName
     * @param string $locale
     *
     * @return string
     */
    public function getFieldValue($fieldName, $locale = 'en')
    {
        $strValue = '';
        $entity = $this->entities[$locale];
        $values = $entity->values();
        if (count($values)) {
            $keys = array_keys($values);
            if (in_array($fieldName, $keys)) {
                $strValue = $values[$fieldName];
            }
        }

        return $strValue;
    }

    /**
     * Get list fields data.
     *
     * @param string $locale
     *
     * @return mixed
     */
    public function getFieldValues($locale = 'en')
    {
        $entity = $this->entities[$locale];
        $values = $entity->values();

        return $values;
    }

    /**
     * Check valid entity.
     *
     * @return bool
     */
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

    /**
     * Save field data.
     *
     * @return bool
     */
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
