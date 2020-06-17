<?php

namespace Validix;

use Zend\Validator\AbstractValidator;
use Zend\Validator\ValidatorInterface;

class Sns
    extends AbstractValidator
        implements ValidatorInterface
{
    const MSG_NUMERIC   = 'msgNumeric';
    const MSG_INT       = 'msgInt';
    const MSG_SNS       = 'msgSns';
    const MSG_SIZE      = 'msgSize';

    /**
     * Size of SNS number
     *
     * @var array
     */
    protected $size = 9;


    protected $messageTemplates = array(
        self::MSG_NUMERIC   => "'%value%' is not a number",
        self::MSG_INT       => "'%value%' is not a integer",
        self::MSG_SNS       => "'%value%' is not a valid SNS number",
        self::MSG_SIZE      => "'%value%' is not a valid SNS number size",
    );


    public function isValid( $value )
    {
        $value = (string) $value;

        $this->setValue($value);

        if ( ! is_numeric($value) ) {
            $this->error(self::MSG_NUMERIC );
            return false;
        }

        if ( ! ctype_digit($value) ) {
            $this->error(self::MSG_INT );
            return false;
        }

        if ( ! $this->validateSnsSize( $value ) ) {
            $this->error(self::MSG_SIZE);
            return false;
        }

        if ( ! $this->validateSns( $value ) ) {
            $this->error(self::MSG_SNS);
            return false;
        }

        return true;
    }


    protected function validateSnsSize ( $sns ) {

        if( strlen( $sns) == $this->size  ) {
            return TRUE;
        }

        return FALSE;
    }


    protected function validateSns ( $sns ) {

        $length = $this->size;

        $digitoVerificador = $sns[ $length - 1 ];

        $soma = 0;

        for ( $i = 0 ; $i <= $length - 2 ; $i++ ) {

            $parcela = (int) $sns[$i];
            $sj = $soma + $parcela;

            if ( $sj != 10 ) {
                $sj = $sj % 10;
            }

            $soma = ( 2 * $sj ) % 11;
        }

        $soma = ( 11 - $soma ) % 10;

        if( $digitoVerificador == $soma ) {

            return TRUE;
        }

        return FALSE;

    }

}