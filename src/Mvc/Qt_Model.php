<?php

/**
 * Quantum PHP Framework
 *
 * An open source software development framework for PHP
 *
 * @package Quantum
 * @author Arman Ag. <arman.ag@softberg.org>
 * @copyright Copyright (c) 2018 Softberg LLC (https://softberg.org)
 * @link http://quantum.softberg.org/
 * @since 1.0.0
 */

namespace Quantum\Mvc;

use Quantum\Exceptions\ExceptionMessages;
use Quantum\Libraries\Database\Database;
use Quantum\Exceptions\ModelException;
use Quantum\Factory\ModelFactory;
use Quantum\Helpers\Helper;

/**
 * Base Model Class
 *
 * Qt_Model class is a base abstract class that every model should extend,
 * This class also connects to database and prepares object relational mapping
 *
 * @package Quantum
 * @subpackage MVC
 * @category MVC
 */
abstract class Qt_Model
{

    /**
     * The database table associated with model
     *
     * @var string
     */
    public $table;

    /**
     * Id column of table
     *
     * @var string
     */
    public $idColumn = 'id';

    /**
     * Foreign keys
     * 
     * @var array 
     */
    public $foreignKeys = [];

    /**
     * Models fillable properties
     * @var array
     */
    protected $fillable = [];

    /**
     * ORM database abstract layer object
     *
     * @var object
     */
    private $orm;

    /**
     * The model
     * @var string
     */
    private $model;

    /**
     * Class constructor
     *
     * @return void
     * @throws \Exception When called directly
     */
    public final function __construct()
    {
        $this->model = get_called_class();
        $this->orm = Database::getDbalInstance($this->model, $this->table, $this->idColumn);
    }

    /**
     * Fill Object Properties
     *
     * Fills the properties with values
     *
     * @param array $arguments
     * @return void
     * @throws \Exception When the property is not appropriate
     */
    public function fillObjectProps($arguments)
    {
        foreach ($arguments as $key => $value) {
            if (!in_array($key, $this->fillable)) {
                throw new ModelException(Helper::_message(ExceptionMessages::INAPPROPRIATE_PROPERTY, $key));
            }

            $this->$key = $value;
        }

        return $this;
    }

    /**
     * __get magic
     *
     * Allows to access to models property
     *
     * @param string $property
     * @return mixed
     */
    public function __get($property)
    {
        return isset($this->orm->ormObject->$property) ? $this->orm->ormObject->$property : null;
    }

    /**
     * __set magic
     *
     * Allows to set values to models properties
     *
     * @param string $property
     * @param mixed $vallue
     */
    public function __set($property, $value)
    {
        $this->orm->ormObject->$property = $value;
    }

    /**
     * __call magic
     *
     * Allows to call models methods
     *
     * @param string $method
     * @param mixed $args
     * @return mixed
     * @throws \Exception
     */
    public function __call($method, $args = null)
    {
        if (method_exists($this->orm, $method)) {

            $result = $this->orm->{$method}(...$args);

            if (is_array($result) || is_int($result) || is_string($result)) {
                return $result;
            } else {
                if (is_object($result)) {
                    $this->orm->ormObject = $result;
                }
                return $this;
            }
        } else {
            throw new ModelException(Helper::_message(ExceptionMessages::UNDEFINED_MODEL_METHOD, $method));
        }
    }

}
