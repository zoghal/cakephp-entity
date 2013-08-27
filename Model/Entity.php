<?php

App::uses('Object', 'Core');
App::uses('Hash', 'Utility');
App::uses('Set', 'Utility');

class Entity extends Object implements ArrayAccess {

	// @codingStandardsIgnoreStart

	public $_name_;

	// @codingStandardsIgnoreEnd

	// Alias for $this->_name
	protected $_name;

	protected $_modelClass;

	protected $_deleted = false;

	protected static $_modifier = null;

	protected static $_modifierMethods = null;

	public function allows() {
		return array();
	}

	public function isAllowed($method) {
		if (!EntityAccessor::methodExists($this, $method)) {
			return false;
		}

		if (EntityAccessor::propertyExists($this, $method)) {
			return true;
		}

		$allows = $this->allows();
		if (empty($allows)) {
			return false;
		}

		if ($allows == '*') {
			return true;
		}

		return in_array($method, $allows);
	}

/**
 *	Bind the entity and its source model.
 *
 *	@param $model base model object
 *	@param $data array of data, same structure with the one returned by find('first')
 *	@access ment to be public only for EntityModel.
 */
	public function bind(EntityModel $model, $data) {
		assert('is_array($data)');

		// @codingStandardsIgnoreStart
		$this->_name_ = $model->alias;
		// @codingStandardsIgnoreEnd

		$this->_name = $model->alias;
		$this->_modelClass = $model->name;

		foreach ($data as $modelClass => $values) {
			if ($modelClass == $model->alias) {
				/* if the data is array of values for my class,
					 use them as a property */

				foreach ($values as $key => $val) {
					$model->assignProperty($this, $key, $val);
				}
			} else {
				/* if not for my class, assign as another entity */

				$model->assignProperty($this, $modelClass, $values);
			}
		}
	}

	public function getModel() {
		return ClassRegistry::init(array(
			'class' => $this->_modelClass,
			'alias' => $this->_name,
			'type' => 'Model',
		));
	}

	public function isEqual($other) {
		if (!$other) {
			return false;
		}

		if (empty($other->id)) {
			return false;
		}

		if (get_class($other) != get_class($this)) {
			return false;
		}

		return (strval($other->id) == strval($this->id));
	}

	public function save($validate = true, $fields = null) {
		if ($this->_deleted) {
			return false;
		}

		$fieldList = array();
		if ($fields !== null) {
			$fieldList = (array)$fields;
		}

		$Model = $this->getModel();
		$Model->create();
		$Model->id = isset($this->id) ? $this->id : null;
		if ($Model->id === false) {
			$Model->id = null;
		}

		$id = $Model->id;
		$saved = $Model->save($this->toArray(), $validate, $fieldList);
		if (!$saved) {
			return false;
		}

		if ($id === null) {
			$this->id = $Model->getLastInsertID();
		}
		return $saved;
	}

	public function update($values = array()) {
		if ($this->_deleted) {
			return false;
		}

		if (empty($values)) {
			return false;
		}

		$Model = $this->getModel();
		$Model->id = isset($this->id) ? $this->id : null;
		if ($Model->id === false || $Model->id === null) {
			return false;
		}

		$updated = $Model->updateAll($values, array($Model->primaryKey => $Model->id));
		if (!$updated) {
			return false;
		}

		foreach ($values as $key => $val) {
			$Model->assignProperty($this, $key, $val);
		}

		return true;
	}

	public function delete($cascade = true) {
		if ($this->_deleted) {
			return false;
		}

		$Model = $this->getModel();
		$Model->id = isset($this->id) ? $this->id : null;
		if ($Model->id === false || $Model->id === null) {
			return false;
		}

		$deleted = $Model->delete($Model->id, $cascade = true);
		if ($deleted) {
			$this->_deleted = true;
		}

		return $deleted;
	}

	public function invalidate($field, $value = true) {
		$Model = $this->getModel();
		return $Model->invalidate($field, $value);
	}

	public function validates($options = array()) {
		$Model = $this->getModel();
		$Model->set($this->toArray());
		return $Model->validates($options);
	}

	// Magic actions =========================================

	public function __toString() {
		$html = '<div class="' . $this->_name . '">';
		foreach (EntityAccessor::properties($this) as $key => $val) {
			$html .= '<strong class="key">' . h($key) . '</strong>' . '<span clas="value">' . h(strval($val)) . '</span>';
		}
		$html .= '</div>';

		return $html;
	}

	public function fromArray($data) {
		$Model = $this->getModel();

		if (Hash::maxDimensions($data) === 1) {
			$data = array($Model->alias => $data);
		}

		foreach ($data as $modelClass => $values) {
			if ($modelClass == $Model->alias) {
				// if the data is array of values for my class, use them as a property
				foreach ($values as $key => $val) {
					$Model->assignProperty($this, $key, $val);
				}
			} else {
				// if not for my class, assign as another entity

				$Model->assignProperty($this, $modelClass, $values);
			}
		}
	}

	public function toArray() {
		$data = Set::reverse($this);

		$objectName = $this->_name;
		foreach (array_keys($data[$objectName]) as $name) {
			if (!is_array($data[$objectName][$name])) {
				continue;
			}

			if (Hash::numeric(array_keys($data[$objectName][$name]))) {
				// has many association

				$list = $data[$objectName][$name];
				unset($data[$objectName][$name]);

				$name = Inflector::classify($name);
				$data[$name] = array();
				foreach ($list as $sub) {
					if (is_array($sub)) {
						$sub = current($sub);
					}
					$data[$name][] = $sub;
				}
			} else {
				// has one association

				$sub = $data[$objectName][$name];
				unset($data[$objectName][$name]);

				$data[$name] = $sub;
			}
		}

		return $data;
	}

	protected function _magicFetch($key, &$value) {
		if ($key[0] == '_') {
			return null;
		}

		if (isset($this->{$key})) {
			$value = $this->{$key};
			return true;
		}

		if ($this->isAllowed($key)) {
			$value = $this->{$key}();

			// if property exists, this means cache the result of method.
			if (EntityAccessor::propertyExists($this, $key)) {
				$this->{$key} = $value;
			}
			return true;
		}

		$Model = $this->getModel();
		if ($key == $Model->alias) {
			$value = $this;
			return true;
		}

		return false;
	}

	// ArrayAccess implementations ===========================

	public function offsetExists($key) {
		return $this->_magicFetch($key, $value);
	}

	public function offsetGet($key) {
		if ($this->_magicFetch($key, $value)) {
			return $value;
		}

		if (self::$_modifier == null) {
			self::$_modifier = new EntityModifier();
			self::$_modifierMethods = get_class_methods(self::$_modifier);
		}

		foreach (self::$_modifierMethods as $method) {
			if (self::$_modifier->{$method}($this, $key, $value)) {
				return $value;
			}
		}

		return null;
	}

	public function offsetSet($key, $value) {
		$this->{$key} = $value;
	}

	public function offsetUnset($key) {
		unset($this->{$key});
	}

}

class EntityAccessor {

	public static function methodExists(Entity $entity, $method) {
		try {
			$ref = new ReflectionMethod($entity, $method);
			return $ref->isPublic();
		} catch (ReflectionException $e) {
		}
		return false;
	}

	public static function propertyExists(Entity $entity, $property) {
		try {
			$ref = new ReflectionProperty($entity, $property);
			return $ref->isPublic();
		} catch (ReflectionException $e) {
		}
		return false;
	}

	public static function properties(Entity $entity) {
		$properties = get_object_vars($entity);
		unset($properties['_name_']);
		return $properties;
	}

}

class EntityModifier {

	public function reverse($entity, $key, &$value) {
		if (!preg_match('/^reverse_(.+)$/', $key, $match)) {
			return false;
		}

		$key = $match[1];
		$value = $entity[$key];
		if (is_null($value)) {
			return false;
		}

		if (is_array($value)) {
			$value = array_reverse($value);
		} else {
			$value = implode('', array_reverse(str_split(strval($value))));

		}

		return true;
	}

}
