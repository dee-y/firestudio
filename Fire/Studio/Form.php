<?php

namespace Fire\Studio;

use \Valitron\Validator;
use \Fire\Studio\DataMapper;
use \Fire\Studio;

class Form
{
    use \Fire\Studio\Injector;

    const CONFIG_FORM_ERROR_CODES = '/Application/Config/FormErrors';
    const CONFIG_FORM_ERROR_CODES_FILE = 'ErrorCodes';

    /**
     * All validations available. See https://github.com/vlucas/valitron for more
     * info about validations.
     */
    const VALIDATION_REQUIRED = 'required';
    const VALIDATION_EQUALS = 'equals';
    const VALIDATION_DIFFERENT = 'different';
    const VALIDATION_ACCEPTED = 'accepted';
    const VALIDATION_NUMERIC = 'numeric';
    const VALIDATION_INTEGER = 'integer';
    const VALIDATION_BOOLEAN = 'boolean';
    const VALIDATION_ARRAY = 'array';
    const VALIDATION_LENGTH = 'length';
    const VALIDATION_LENGTH_BETWEEN = 'lengthBetween';
    const VALIDATION_LENGTH_MIN = 'lengthMin';
    const VALIDATION_LENGTH_MAX = 'lengthMax';
    const VALIDATION_MIN = 'min';
    const VALIDATION_MAX = 'max';
    const VALIDATION_IN = 'in';
    const VALIDATION_NOT_IN = 'notIn';
    const VALIDATION_IP = 'ip';
    const VALIDATION_EMAIL = 'email';
    const VALIDATION_EMAIL_DNS = 'emailDNS';
    const VALIDATION_URL = 'url';
    const VALIDATION_ALPHA = 'alpha';
    const VALIDATION_ALPHA_NUM = 'alphaNum';
    const VALIDATION_SLUG = 'slug';
    const VALIDATION_REGEX = 'regex';
    const VALIDATION_DATE = 'date';
    const VALIDATION_DATE_FORMAT = 'dateFormat';
    const VALIDATION_DATE_BEFORE = 'dateBefore';
    const VALIDATION_DATE_AFTER = 'dateAfter';
    const VALIDATION_CONTAINS = 'contains';
    const VALIDATION_CREDITCARD = 'creditCard';
    const VALIDATION_INSTANCEOF = 'instanceOf';
    const VALIDATION_OPTIONAL = 'optional';

    private $_validator;

    private $_fieldErrorMap;

    private $_fieldIds;

    public function __construct($formDataObj)
    {
        DataMapper::mapDataToObject($this, $formDataObj);
        $this->_validator = new Validator(
            (array) $formDataObj,
            null,
            self::CONFIG_FORM_ERROR_CODES_FILE,
            __DIR__ . self::CONFIG_FORM_ERROR_CODES
        );
        $this->_fieldErrorMap = [];
        $this->_fieldIds = array_keys((array) $formDataObj);
    }

    public function fieldValidation($fieldName, $validation, $errorMessage)
    {
        $validationType = is_array($validation) ? $validation[0] : $validation;
        $errorMapKey = $fieldName . '.' . $validationType;
        $this->_fieldErrorMap[$errorMapKey] = $errorMessage;
        $this->_validator->mapFieldRules($fieldName, [$validation]);
    }

    public function isValid()
    {
        $isValid = $this->_validator->validate();
        if ($isValid) {
            return true;
        } else {
            $errors = $this->getValidationErrors();
            $fieldErrors = [];
            foreach ($errors as $error) {
                $fieldErrors[] = $this->_fieldErrorMap[$error];
            }
            $_SESSION[Studio::SESSION_ERRORS_KEY] = $fieldErrors;
            Studio::$sessionErrors = $fieldErrors;
        }

        return false;
    }

    public function getValidationErrors()
    {
        $errors = $this->_validator->errors();
        $cleanedErrors = [];
        foreach ($errors as $field => $error) {
            foreach ($error as $validationError) {
                $cleanedErrors[] = $field . '.' . explode(' ', $validationError)[1];
            }
        }
        return $cleanedErrors;
    }

    public function getFieldIds()
    {
        return $this->_fieldIds;
    }
}
