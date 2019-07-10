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
    const MSG_EMPTY_ARRAY = 'msgEmptyArray';
    const MSG_VALUE_ERROR = 'msgNotValid';

    private $validator = null;

    private $keysToValidate = array();

    private $validValues = array();

    protected $messageTemplates = array(
        self::MSG_NOT_ARRAY         => "The value to validate must be of type array",
        self::MSG_EMPTY_ARRAY       => "The value to validate is an empty array",
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

        if ( ! $value ) {

            $this->error(self::MSG_EMPTY_ARRAY);
            return false;
        }

        $this->setValue($value);

        $keys = $this->getKeysToValidate();
        if ( ! $keys ) {
            $keys = array_keys($value);
        }

        $validValues = array();
        foreach ($keys as $keyToCheck ) {

            if ( ! isset($value[$keyToCheck]) ) {
                return false;
            }

            if ( !$this->validator->isValid($value[$keyToCheck]) ) {

                $this->error(self::MSG_VALUE_ERROR);
                return false;
            }

            $validValues[$keyToCheck] = $value[$keyToCheck];
        }

        $this->validValues = $validValues;
        return true;

    }


    /**
     * Possibilidade de indicar apenas a avaliação algumas keys do
     * array submetido para validação
     *
     * @param array $keys
     * @return $this
     */
    public function defineKeysToValidate(array $keys) {

        $this->keysToValidate = $keys;
        return $this;

    }

    /**
     * Obter as definidas a avaliar
     *
     * @return array
     */
    public function getKeysToValidate() {

        return $this->keysToValidate;
    }


    /**
     * Devolve os valores validos
     *
     * @return array
     */
    public function getValidValues() {

        return $this->validValues;
    }

}