<?php

namespace Validix;

use Zend\Validator\AbstractValidator;
use Zend\Validator\ValidatorInterface;
use Validix\Datetime as DatetimeValidator;

class DatetimeInterval
    extends AbstractValidator
        implements ValidatorInterface
{
    const MSG_NOT_ARRAY = 'msgNotArray';
    const MSG_NOT_START_AND_END = 'msgNotSartAndEnd';
    const MSG_START_DATE_ERROR = 'msgErrorStartDateFormat';
    const MSG_END_DATE_ERROR = 'msgErrorEndDateFormat';
    const MSG_DATE_INVALID_INTERVAL = 'msgIntervalError';

    protected $start = null;

    protected $end = null;

    /**
     * Formato a ser utilizado
     *
     * @var string
     */
    protected $format = 'd-m-Y H:i:s';

    protected $messageTemplates = array(
        self::MSG_NOT_ARRAY             => "The value to validate must be of type array",
        self::MSG_NOT_START_AND_END     => "The array submitted does not contain the keys 'start' and 'end'",
        self::MSG_START_DATE_ERROR      => "The datetime of 'start' is not a string or a Datetime object",
        self::MSG_END_DATE_ERROR        => "The datetime of 'end' is not a string or a Datetime object",
        self::MSG_DATE_INVALID_INTERVAL => "Invalid datetime interval",
    );


    public function __construct(array $options = array())
    {

        $this->setOptions($options);
        return $this;
    }


    public function isValid($value)
    {

        if ( ! is_array($value) ) {

            $this->error(self::MSG_NOT_ARRAY);
            return false;
        }

        if ( !( isset($value['start']) && isset($value['end']) ) ) {

            $this->error(self::MSG_NOT_START_AND_END);
            return false;
        }

        $this->setValue($value);
        $start  = $this->transformToDatetime( $value['start'] );
        if ( !$start ) {

            $this->error(self::MSG_START_DATE_ERROR);
            return false;
        }

        $end    = $this->transformToDatetime( $value['end'] );
        if ( !$end ) {

            $this->error(self::MSG_END_DATE_ERROR);
            return false;
        }

        if ( $start <= $end ) {

            $this->start = $start;
            $this->end = $end;
            return true;
        }

        $this->error(self::MSG_DATE_INVALID_INTERVAL);
        return false;

    }


    protected function transformToDatetime($value)
    {

        if ( $value instanceof \DateTime) {
            return $value;
        }

        if (!is_string($value)) {
            return false;
        }

        $dateTimeValidator = new DatetimeValidator();
        if ( $this->getFormat() ) {
            $dateTimeValidator->setFormat($this->getFormat());
        }

        if ( $dateTimeValidator->isValid($value) ) {
            return $dateTimeValidator->getValueDateTime();
        }

        return false;
    }


    /**
     * @param $format
     * @return $this
     * @throws ValidixException
     */
    public function setFormat($format)
    {

        if ( !is_string($format) ) {
            throw new ValidixException('Os formatos aceites devem ser strings');
        }

        $this->format = $format;
        return $this;
    }


    /**
     * Obter o formato definido
     *
     * @return string
     */
    public function getFormat()
    {

        return $this->format;
    }


    /**
     * Obter o valor validado de início de intervalo como objecto DateTime
     *
     * @return \DateTime|null
     */
    public function getValueDatetimeStart()
    {

        return $this->start;
    }

    /**
     * Obter o valor validado de fim de intervalo como objecto DateTime
     *
     * @return \DateTime|null
     */
    public function getValueDatetimeEnd()
    {

        return $this->end;
    }

}