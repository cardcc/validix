<?php

namespace Validix;

use Zend\Validator\AbstractValidator;
use Zend\Validator\ValidatorInterface;

class CodigoPostal
    extends AbstractValidator
        implements ValidatorInterface
{
    const MSG_INVALID   = 'msgInvalid';

    protected $_messageTemplates = array(
        self::MSG_INVALID       => "'%value%' is not a valid Codigo Postal",
    );

    public function isValid($value)
    {

        $this->setValue($value);
        $value = (string) $value;

        if ( !preg_match('/^[0-9]{4}$/D',$value) ) {

            $this->error(self::MSG_INVALID);
            return false;
        }

        return true;

    }

}