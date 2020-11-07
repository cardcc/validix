<?php

namespace Validix;

use Zend\Validator\AbstractValidator;
use Zend\Validator\ValidatorInterface;

class ArrayIntValues
    extends AbstractValidator
        implements ValidatorInterface
{
    const MSG_NOT_ARRAY     = 'msgNotArray';
    const MSG_EMPTY_ARRAY   = 'msgEmptyArray';
    const MSG_NOT_VALID     = 'msgNotValid';

    protected $_messageTemplates = array(
        self::MSG_NOT_ARRAY       => "'%value%' is not a array",
        self::MSG_EMPTY_ARRAY     => "'%value%' is empty array",
        self::MSG_NOT_VALID       => "'%value%' is not a valid array with integers",
    );

    public function isValid($value)
    {

        $this->setValue($value);

        if ( ! is_array($value) ) {
            $this->error(self::MSG_NOT_ARRAY);
            return false;
        }

        if ( !$value ) {
            $this->error(self::MSG_EMPTY_ARRAY);
            return false;
        }

        foreach ($value as $elementValue ) {

            if ( is_string($elementValue) ) {

                if ( ctype_digit($elementValue) ) {

                    continue;
                }

            } else {

                if ( is_int($elementValue) ) {
                    continue;
                }
            }

            $this->error(self::MSG_NOT_VALID);
            return false;
        }

        return true;

    }


}