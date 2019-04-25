<?php

namespace Validix;

use Zend\Validator\AbstractValidator;
use Zend\Validator\ValidatorInterface;

class Datetime
    extends AbstractValidator
        implements ValidatorInterface
{
    const MSG_UNDEFINED_FORMAT = 'msgNoFormat';
    const MSG_DATE_INTERVAL_ERROR = 'msgIntervalError';
    const MSG_DATE_ERROR = 'msgDateError';

    protected $min = null;

    protected $max = null;

    protected $valueDateTime = null;

    /**
     * Formato a ser utilizado
     *
     * @var string
     */
    protected $format = 'd-m-Y H:i:s';

    protected $messageTemplates = array(
        self::MSG_UNDEFINED_FORMAT => "O formato não foi corretamente definido",
        self::MSG_DATE_INTERVAL_ERROR => "'%value%' não está entre os valores definidos de intervalo",
        self::MSG_DATE_ERROR => "'%value%' não identifica uma data válida",

    );


    public function __construct(array $options = array())
    {

        $this->setOptions($options);
        return $this;
    }


    public function isValid($value)
    {

        $value = (string)$value;

        $this->setValue($value);

        $dataTime = \DateTime::createFromFormat($this->getFormat(), $value);
        if (!$dataTime) {

            $this->error(self::MSG_DATE_ERROR);
            return false;
        }

        if (!($dataTime->format($this->getFormat()) === $value)) {

            $this->error(self::MSG_UNDEFINED_FORMAT);
            return false;
        }

        $this->setValueDateTime($dataTime);

        $min = $this->getMin();
        $min = $min ? $min : $dataTime;

        $max = $this->getMax();
        $max = $max ? $max : $dataTime;

        if ($this->validateDateInterval($dataTime, $min, $max)) {

            $this->setValueDateTime($dataTime);
            return true;
        }

        $this->error(self::MSG_DATE_INTERVAL_ERROR);
        return false;

    }


    /**
     * Definir o formato a ser utilizado na validação
     *
     * @param $format
     * @return $this
     */
    public function setFormat($format)
    {

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
     * Definir a data validada como objecto DateTime
     *
     * @param \DateTime $value
     * @return $this
     */
    protected function setValueDateTime(\DateTime $value)
    {

        $this->valueDateTime = $value;
        return $this;
    }


    /**
     * Obter o valor validado como objecto DateTime
     *
     * @return DateTime
     */
    public function getValueDateTime()
    {

        return $this->valueDateTime;
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
}