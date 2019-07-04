<?php

namespace Validix;

use Zend\Validator\AbstractValidator;
use Zend\Validator\ValidatorInterface;

class Nif
    extends AbstractValidator
        implements ValidatorInterface
{
    const MSG_NUMERIC   = 'msgNumeric';
    const MSG_NIF       = 'msgNif';

    protected $_messageTemplates = array(
        self::MSG_NUMERIC   => "'%value%' is not a number",
        self::MSG_NIF       => "'%value%' is not a valid NIF",
    );

    public function isValid($value)
    {

        $this->setValue($value);

        if ( ! is_numeric($value) ) {
            $this->error(self::MSG_NUMERIC);
            return false;
        }

        if ( ! $this->validateNif( $value ) ) {
            $this->error(self::MSG_NIF);
            return false;
        }

        return true;

    }


    protected function validateNif ( $nif ) {

        if ( (!is_null($nif)) && (is_numeric($nif)) && (strlen($nif)==9) && ($nif[0]==1 || $nif[0]==2 || $nif[0]==5 || $nif[0]==6 || $nif[0]==7 || $nif[0]==8 || $nif[0]==9) ) {

            $dC = $nif[0] * 9;

            for ($i=2;$i<=8;$i++) {
                $dC += ($nif[$i-1])*(10-$i);
            }
            $dC = 11-($dC % 11);

            $dC = ($dC>=10)?0:$dC;

            if ($dC==$nif[8]) {

                return true;
            }
        }

        return false;
    }


}