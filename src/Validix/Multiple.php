<?php

namespace Validix;

use Zend\Validator\AbstractValidator;
use Zend\Validator\ValidatorInterface;
use Validix\Datetime as DatetimeValidator;

class Multiple
    extends AbstractValidator
        implements ValidatorInterface
{
    const MSG_NOT_ARRAY = 'msgNotArray';
    const MSG_VALUE_ERROR = 'msgNotValid';

    private $validator = null;

    protected $messageTemplates = array(
        self::MSG_NOT_ARRAY         => "The value to validate must be of type array",
        self::MSG_VALUE_ERROR       => "Not valid",
    );


    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
        return $this;
    }


    public function isValid($value)
    {

        if ( ! is_array($value) ) {

            $this->error(self::MSG_NOT_ARRAY);
            return false;
        }

        foreach ($value as $containedValue) {

            if ( !$this->validator->isValid($containedValue) ) {

                $this->error(self::MSG_VALUE_ERROR);
                return false;
            }
        }

        return true;

    }

}