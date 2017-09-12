<?php

namespace App\ZendFormElementDiscovery\Element;

use Twitter_Bootstrap3_Form;
use Zend_Db_Table_Abstract;

/**
 * Class Base
 *
 * @package App\ZendFormElementDiscovery\Element
 */
abstract class Base
{
    /**
     * @var Zend_Db_Table_Abstract
     */
    protected $model;

    /**
     * @var string
     */
    protected $field;

    /**
     * @var array
     */
    protected $params = [];

    /**
     * @var bool
     */
    protected $isRequired = false;

    /**
     * @var bool
     */
    protected $isUnsigned = false;

    /**
     * @var int
     */
    protected $length;

    /**
     * @var string
     */
    protected $elementClass;

    /**
     * @var array
     */
    protected $validators = [];

    /**
     * @var Twitter_Bootstrap3_Form
     */
    protected $form;

    /**
     * ElementAbstract constructor.
     *
     * @param Zend_Db_Table_Abstract $model
     * @param                        $field
     * @param array                  $params
     */
    public function __construct(Zend_Db_Table_Abstract $model, $field, array $params = [])
    {
        $this->model = $model;
        $this->field = $field;
        $this->params = $params;

        $metadata = $model->info(Zend_Db_Table_Abstract::METADATA)[$field];
        $this->isRequired = !$metadata['NULLABLE'];
        $this->length = $metadata['LENGTH'];
        $this->isUnsigned = !!$metadata['UNSIGNED'];
    }

    /**
     * @param Twitter_Bootstrap3_Form $form
     *
     * @return $this
     */
    public function setForm(Twitter_Bootstrap3_Form $form)
    {
        $this->form = $form;
        return $this;
    }

    /**
     * @return string
     */
    public function getElementClass()
    {
        return $this->elementClass;
    }

    /**
     * @return string
     */
    public function getElementName()
    {
        return strtolower(end(explode('_', $this->getElementClass())));
    }

    /**
     * @return $this
     */
    public function addElement()
    {
        $this->form->addElement($this->getElementName(), $this->field);

        $element = $this->form->getElement($this->field);

        foreach ($this->params as $key => $value) {
            $method = 'set' . ucfirst($key);
            if (method_exists($element, $method)) {
                $element->$method($value);
            }
        }

        if ($this->isRequired()) {
            $element->setRequired(true);
            $element->setAttrib('required', 'required');
        }

        if (method_exists($this->model, 'getLabel')) {
            $element->setLabel(($this->model)::getLabel($this->field));
        }

        if ($this->isUnsigned()) {
            $this->validators = array_merge($this->validators, [
                ['GreaterThan', true, [
                    'min' => 0,
                ]],
            ]);
        }

        if ($this->length) {
            $this->validators = array_merge($this->validators, [
                ['StringLength', true, [
                    'max' => $this->length,
                ]],
            ]);
        }

        if ($this->validators) {
            $element->addValidators($this->validators);
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function isRequired()
    {
        return (bool)$this->isRequired;
    }

    /**
     * @return bool
     */
    public function isUnsigned()
    {
        return (bool)$this->isUnsigned;
    }
}
