<?php

class Query extends Object implements ArrayAccess, IteratorAggregate, Countable {

	public $type;

	protected $_Model;

	protected $_options;

	protected static $_defaults = array();

	public static function setDefaultOptions($Model, $options = array()) {
		self::$_defaults[$Model->alias] = $options;
	}

	public static function defaultOptions($Model) {
		if (isset(self::$_defaults[$Model->alias])) {
			return self::$_defaults[$Model->alias];
		}

		return array();
	}

	public function __construct($Model, $type, $options = array(), $default = true) {
		$this->_Model = $Model;
		$this->type = $type;
		$this->_options = $options;

		if ($default) {
			$this->_options += self::defaultOptions($Model);
		}
	}

	public function result() {
		return $this->_Model->find($this->type, $this->_options);
	}

	public function type($newType) {
		return new Query($this->_Model, $newType, $this->_options, false);
	}

	// public function __call($method, $args) {
	// 	return new Query($this->_Model, $newType, $this->_options, false);
	// }

	public function offsetExists($key) {
		return array_key_exists($key, $this->_options);
	}

	public function offsetGet($key) {
		return $this->_options[$key];
	}

	public function offsetSet($key, $value) {
		$this->_options[$key] = $value;
	}

	public function offsetUnset($key) {
		unset($this->_options[$key]);
	}

	public function getIterator() {
		return $this->result();
	}

	public function count($total = false) {
	}

}
