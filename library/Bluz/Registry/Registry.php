<?php

/**
 * Copyright (c) 2011 by Bluz PHP Team
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

/**
 * @namespace
 */
namespace Bluz\Registry;

use Bluz\Package;

/**
 * Registry
 *
 * @category Bluz
 * @package  Registry
 *
 * @author   Anton Shevchuk
 */
class Registry extends Package
{
    /**
     * Stored data
     * 
     * @var array
     */
    protected $_data = array();

    /**
     * reset data
     *
     * @param array $data
     * @return Registry
     */
    public function setData(array $data)
    {
        $this->_data = $data;
    }

    /**
     * setter for key
     *
     * @param string $key
     * @param mixed $value
     * @return mixed
     */
     public function __set($key, $value)
     {
         $this->_data[$key] = $value;
     }

    /**
     * getter for key
     *
     * @param string $key
     * @return mixed
     */
    public function __get($key)
    {
        if (isset($this->_data[$key])) {
            return $this->_data[$key];
        } else {
            return null;
        }
    }
}