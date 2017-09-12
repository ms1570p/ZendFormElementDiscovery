<?php

namespace ms1570p\ZendFormElementDiscovery;

use ms1570p\ZendFormElementDiscovery\Element\Base;
use ms1570p\ZendFormElementDiscovery\Element\Email;
use ms1570p\ZendFormElementDiscovery\Element\Number;
use ms1570p\ZendFormElementDiscovery\Element\Password;
use ms1570p\ZendFormElementDiscovery\Element\Select;
use ms1570p\ZendFormElementDiscovery\Element\Text;
use ms1570p\ZendFormElementDiscovery\Element\Url;
use Zend_Db_Table_Abstract;
use Zend_Exception;

/**
 * Class FormElementDiscoveryTrait
 *
 * @package ms1570p\ZendFormElementDiscovery
 */
trait ZendFormElementDiscoveryTrait
{
    /**
     * @var array
     */
    protected $definitionByField = [
        'email'     => Email::class,
        'url'       => Url::class,
        'site_url'  => Url::class,
        'password'  => Password::class,
    ];

    /**
     * @var array
     */
    protected $definitionByType = [
        'varchar'   => Text::class,
        'char'      => Text::class,
        'decimal'   => Number::class,
        'int'       => Number::class,
        'tinyint'   => Number::class,
        'mediumint' => Number::class,
        'smallint'  => Number::class,
        'bit'       => Number::class,
        'float'     => Number::class,
    ];

    /**
     * @var array
     */
    protected $definitionByParam = [
        'multiOptions' => Select::class,
    ];

    /**
     * @param Zend_Db_Table_Abstract $model
     * @param                        $field
     * @param array                  $params
     *
     * @return $this
     * @throws Zend_Exception
     */
    public function addElementDiscovery(Zend_Db_Table_Abstract $model, $field, array $params = [])
    {
        if (!array_key_exists($field, $model->info(Zend_Db_Table_Abstract::METADATA))) {
            throw new Zend_Exception(sprintf('Undefined field "%s"', $field));
        }

        if (!($elementClass = $this->getElementClassByField($field))) {
            $elementClass = $this->getElementClassByType($model, $field);
        }

        if ($elementClassByParam = $this->getElementClassByParam($params)) {
            $elementClass = $elementClassByParam;
        }

        /** @var Base $instance */
        $instance = new $elementClass($model, $field, $params);
        $instance->setForm($this);
        $instance->addElement();

        return $this;
    }

    /**
     * @param $field
     *
     * @return null
     */
    private function getElementClassByField($field)
    {
        if (array_key_exists($field, $this->definitionByField)) {
            return $this->definitionByField[$field];
        }

        return null;
    }

    /**
     * @param Zend_Db_Table_Abstract $model
     * @param                        $field
     *
     * @return mixed
     * @throws Zend_Exception
     */
    private function getElementClassByType(Zend_Db_Table_Abstract $model, $field)
    {
        $type = $model->info(Zend_Db_Table_Abstract::METADATA)[$field]['DATA_TYPE'];
        if (!array_key_exists($type, $this->definitionByType)) {
            throw new Zend_Exception(sprintf('Not implemented field "%s"', $type));
        }

        return $this->definitionByType[$type];
    }

    /**
     * @param array $params
     *
     * @return null
     */
    private function getElementClassByParam(array $params)
    {
        if ($intersectKey = array_intersect_key($params, $this->definitionByParam)) {
            return $this->definitionByParam[key($intersectKey)];
        }

        return null;
    }

    /**
     * @param array $params
     *
     * @return $this
     */
    public function addDefinitionByField(array $params)
    {
        $this->definitionByField = array_merge(
            $this->definitionByField,
            $params
        );

        return $this;
    }

    /**
     * @param array $params
     *
     * @return $this
     */
    public function addDefinitionByType(array $params)
    {
        $this->definitionByType = array_merge(
            $this->definitionByType,
            $params
        );

        return $this;
    }
}
