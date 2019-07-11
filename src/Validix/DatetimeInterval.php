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

    protected $min = null;

    protected $max = null;

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

        if ( $start > $end ) {

            $this->error(self::MSG_DATE_INVALID_INTERVAL);
            return false;

        }

        $min = $this->getMin() ? $this->getMin() : $start;
        $max = $this->getMax() ? $this->getMax() : $end;

        if ( $start >= $min && $end <= $max ) {

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
     * Obter o mínimo
     *
     * @return \DateTime
     */
    public function getMin()
    {

        return $this->min;
    }


    /**
     * Obter o máximo
     *
     * @return \DateTime
     */
    public function getMax()
    {

        return $this->max;
    }


    /**
     * Definir o mínimo
     *
     * @param \Datetime $value
     * @return $this
     * @throws ValidixException
     */
    public function setMin(\DateTime $value)
    {

        $max = $this->getMax();
        if ($max) {

            if (!$this->validateDateInterval($value, $value, $max)) {

                throw new ValidixException(get_class($this) . " - O mínimo submetido " . $value->format('Y-m-d') . " não é menor que o máximo " . $max->format('Y-m-d'));
            }
        }

        $this->min = $value;
        return $this;
    }


    /**
     * Definir o máximo
     *
     * @param \DateTime $value
     * @return $this
     * @throws ValidixException
     */
    public function setMax(\DateTime $value)
    {

        $min = $this->getMin();
        if ($min) {

            if (!$this->validateDateInterval($value, $min, $value)) {

                throw new ValidixException(get_class($this) . " - O máximo submetido " . $value->format('Y-m-d') . " não é maior que o mínimo " . $min->format('Y-m-d'));
            }
        }

        $this->max = $value;
        return $this;
    }


    /**
     * Verificar se a data inserida se encontra contida num determinado intervalo
     *
     * @param \DateTime $value
     * @param \DateTime $min
     * @param \DateTime $max
     * @return bool
     */
    protected function validateDateInterval(\DateTime $value, \DateTime $min, \DateTime $max)
    {

        if (($value >= $min) && ($value <= $max)) {

            return true;
        }

        return false;
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