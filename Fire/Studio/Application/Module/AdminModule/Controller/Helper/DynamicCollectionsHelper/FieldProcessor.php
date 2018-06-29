<?php

namespace Fire\Studio\Application\Module\AdminModule\Controller\Helper\DynamicCollectionsHelper;

use \Fire\Studio\Application\Module\AdminModule\Controller\Helper\DynamicCollectionsHelper;

class FieldProcessor
{
    use \Fire\Studio\Injector;

    public function saveTextField($field)
    {
        return $field->value;
    }

    public function savePasswordField($field)
    {
        return password_hash($field->value, PASSWORD_DEFAULT);
    }

    public function saveTextAreaField($field)
    {
        return $field->value;
    }

    public function saveSelectField($field)
    {
        return $field->value;
    }

    public function saveMultiSelectField($field)
    {
        if (is_array($field->value)) {
            return $field->value;
        } elseif (!empty($field->value)) {
            return [$field->value];
        }
        return [];
    }

    public function saveDateField($field)
    {
        return $field->value;
    }

}