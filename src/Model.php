<?php

/*
 * Copyright (C) 2019 Leda Ferreira
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace cognitive_services;

/**
 * cognitive_services\Model.
 */
abstract class Model implements \ArrayAccess, \Iterator, \JsonSerializable
{
    /**
     * @var \ArrayObject
     */
    private $attributes;

    /**
     * @var \ArrayIterator
     */
    private $iterator;

    /**
     * @var array
     */
    protected $populateClassMap = [];

    /**
     * @var array
     */
    protected $populateListMap = [];

    /**
     * Creates a new instance of this class.
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        $this->populate($attributes);
    }

    /**
     * Implementation of the magic method __get().
     * @param string $attribute
     * @return mixed
     */
    public function __get($attribute)
    {
        if ($this->iterator->offsetExists($attribute)) {
            return $this->iterator->offsetGet($attribute);
        }
        return null;
    }

    /**
     * Implementation of the magic method __set();
     * @param string $attribute
     * @param mixed $value
     */
    public function __set($attribute, $value)
    {
        $this->iterator->offsetSet($attribute, $value);
    }

    /**
     * Implementation of the magic method __isset();
     * @param string $attribute
     * @return mixed
     */
    public function __isset($attribute)
    {
        return $this->iterator->offsetExists($attribute);
    }

    /**
     * Implementation of the magic method __unset();
     * @param string $attribute
     */
    public function __unset($attribute)
    {
        $this->iterator->offsetUnset($attribute);
    }

    /**
     * Implementation of the magic method __debugInfo().
     * @return array
     */
    public function __debugInfo()
    {
        return (array)$this->attributes;
    }

    /**
     * Implementation of the ArrayAccess interface.
     * @param string $offset
     * @return boolean
     */
    public function offsetExists($offset)
    {
        return $this->iterator->offsetExists($offset);
    }

    /**
     * Implementation of the ArrayAccess interface.
     * @param string $offset
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return $this->iterator->offsetGet($offset);
    }

    /**
     * Implementation of the ArrayAccess interface.
     * @param string $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value)
    {
        $this->iterator->offsetSet($offset, $value);
    }

    /**
     * Implementation of the ArrayAccess interface.
     * @param string $offset
     */
    public function offsetUnset($offset)
    {
        $this->iterator->offsetUnset($offset);
    }

    /**
     * Implementation of the Iterator interface.
     * @return mixed.
     */
    public function current()
    {
        return $this->iterator->current();
    }

    /**
     * Implementation of the Iterator interface.
     * @return mixed.
     */
    public function key()
    {
        return $this->iterator->key();
    }

    /**
     * Implementation of the Iterator interface.
     */
    public function next()
    {
        $this->iterator->next();
    }

    /**
     * Implementation of the Iterator interface.
     */
    public function rewind()
    {
        $this->iterator->rewind();
    }

    /**
     * Implementation of the Iterator interface.
     * @return boolean
     */
    public function valid()
    {
        return $this->iterator->valid();
    }

    /**
     * Implementation of the JsonSerializable interface
     * @return array
     */
    public function jsonSerialize()
    {
        return (array)$this->attributes;
    }

    /**
     * Implementation of ClientInterface.
     * Populates this record's attributes.
     * @param array $attributes
     * @return $this
     */
    protected function populate(array $attributes = [])
    {
        foreach ($this->populateClassMap as $attribute => $class) {
            if (isset($attributes[$attribute])) {
                $attributes[$attribute] = new $class($attributes[$attribute]);
            }
        }

        foreach ($this->populateListMap as $attribute => $class) {
            if (isset($attributes[$attribute])) {
                $list = [];
                foreach ($attributes[$attribute] as $element) {
                    $list[] = new $class($element);
                }
                $attributes[$attribute] = $list;
            }
        }

        $attributes = $this->prepareAttributes($attributes);
        $this->attributes = new \ArrayObject($attributes);
        $this->iterator = $this->attributes->getIterator();
        return $this;
    }

    /**
     * Allows for transformation of attributes.
     * @param array $attributes
     * @return array
     */
    protected function prepareAttributes($attributes = [])
    {
        return $attributes;
    }
}
