<?php
App::uses('EntityAppModel', 'Entity.Model');
App::uses('Entity', 'Entity.Model/Entity');
App::uses('AppEntity', 'Entity.Model/Entity');
App::uses('Hash', 'Utility');

class EntityModel extends EntityAppModel {

	public $entity;

	protected $_entityClass = null;

	protected $_savedEntityStates = array();

	public function __construct($id = false, $table = null, $ds = null) {
		parent::__construct($id, $table, $ds);
		$this->entityClass($this->name . 'Entity');
	}

	public function table($table = null) {
		if ($table !== null) {
			$this->setSource($table);
		}
		return $this->table;
	}

/**
 * @throws Exception Unimplemented
 */
	public function alias($alias = null) {
		throw new Exception("Method 'alias' not implemented");
	}

/**
 * @throws Exception Unimplemented
 */
	public function connection($alias = null) {
		throw new Exception("Method 'connection' not implemented");
	}

	public function primaryKey($key = null) {
		if ($key !== null) {
						$this->primaryKey = $key;
		}

		return $this->primaryKey;
	}

	public function displayField($key = null) {
		if ($key !== null) {
			$this->displayField = $key;
		}

		return $this->displayField;
	}

	public function entityClass($name = null) {
		if ($name === null && !$this->_entityClass) {
			$name = $this->name . 'Entity';
		}

		if ($name !== null) {
			App::uses($name, 'Model/Entity');
			$this->_entityClass = $name;
		}

		return $this->_entityClass;
	}

	public function addBehavior() {
		$this->Behaviors->load($name, $options);
	}

	public function behaviors() {
		$this->Behaviors->loaded();
	}

	public function hasBehavior($name) {
		$this->Behaviors->loaded($name);
	}

/**
 * @throws Exception Unimplemented
 */
	public function association($name) {
		throw new Exception("Method 'association' not implemented");
	}

/**
 * @throws Exception Unimplemented
 */
	public function associations($alias = null) {
		throw new Exception("Method 'associations' not implemented");
	}

	public function hasOne($associated, $options = array()) {
		$this->_bindModel('hasOne', $associated, $options);
	}

	public function belongsTo($associated, $options = array()) {
		$this->_bindModel('belongsTo', $associated, $options);
	}

	public function hasMany($associated, $options = array()) {
		$this->_bindModel('hasMany', $associated, $options);
	}

	public function belongsToMany($associated, $options = array()) {
		$this->_bindModel('hasAndBelongsToMany', $associated, $options);
	}

	protected function _bindModel($type, $associated, $options = array()) {
		$reset = empty($options['reset']) ? true : false;
		if (isset($options['reset'])) {
			unset($options['reset']);
		}

		return $this->bindModel(array(
			$type => array($associated => $options)
		), $reset);
	}

/**
 *	Convert passed $data structure into coresponding entity object.
 *
 *	@param $data Hash to be converted. If omitted, $this->data will be converted.
 *	@return Entity object
 */
	public function convertToEntity($data) {
		if (is_null($data) || empty($data[$this->alias]['id'])) {
			return null;
		}

		return $this->entity($data);
	}

	public function convertToEntities($list) {
		if ($list && !Hash::numeric(array_keys($list))) {
			return $this->convertToEntity($list);
		}

		$result = array();
		foreach ($list as $data) {
			$result[] = $this->convertToEntity($data);
		}
		return $result;
	}

	public function entity($data = array()) {
		$class = $this->entityClass();

		if (!class_exists($class)) {
			App::uses($class, 'Model/Entity');
			if (!class_exists($class)) {
				$class = 'AppEntity';
			}
		}

		$entity = new $class($data);
		return $entity;
	}

	public function beforeFind($queryData) {
		$this->_saveEntityState();

		if (isset($queryData['entity'])) {
			$this->entity = $queryData['entity'];
		}

		return parent::beforeFind($queryData);
	}

	public function afterFind($results, $primary = false) {
		$results = parent::afterFind($results, $primary);

		if ($this->entity && $primary && is_array($results)) {
			$results = $this->convertToEntities($results);
		}

		$this->_restoreEntityState();
		return $results;
	}

	protected function _saveEntityState() {
		$this->_savedEntityStates[] = $this->entity;
	}

	protected function _restoreEntityState() {
		$this->entity = array_pop($this->_savedEntityStates);
	}

	protected function _entityClassForData($data) {
		return $this->entityClass();
	}

	public function allEntities($params = array()) {
		$params['entity'] = true;
		return $this->find('all', $params);
	}

	public function entities($params = array()) {
		return $this->allEntities($params);
	}

	public function __call($method, $params) {
		list($entity, $method) = $this->_analyzeMethodName($method);

		$return = parent::__call($method, $params);

		if ($entity && !is_null($return)) {
			$return = $this->convertToEntities($return);
		}

		return $return;
	}

	protected function _analyzeMethodName($method) {
		$entity = false;

		if (preg_match('/^(entity|(?:all)?entities)by(.+)$/i', $method, $matches)) {
			$entity = true;
			$all = (strtolower($matches[1]) != 'entity');
			$method = ($all ? 'findAllBy' : 'findBy') . $matches[2];
		}

		return array($entity, $method);
	}

/**
 * Override. To support passing entity to set() directly.
 * Because save() will pass its data to set(), you can now
 * call save() with entity like this:
 *
 *    $Model->save($entity);
 *
 */
	public function set($one, $two = null) {
		if (is_a($one, 'Entity')) {
			$one = $one->toArray();
		}
		return parent::set($one, $two);
	}

	public function paginateCount($conditions, $recursive, $extra) {
		$parameters = $extra + compact('conditions');
		if ($recursive != $this->recursive) {
			$parameters['recursive'] = $recursive;
		}
		$parameters['entity'] = false;

		return $this->find('count', $parameters);
	}

	public function paginate($conditions, $fields, $order, $limit, $page, $recursive, $extra) {
		$params = compact('conditions', 'fields', 'order', 'limit', 'page');

		if ($recursive != $this->recursive) {
			$params['recursive'] = $recursive;
		}

		$type = !empty($extra['type']) ? $extra['type'] : 'all';

		return $this->find($type, array_merge($params, $extra));
	}

	public function count($conditions = null) {
		return $this->find('count', array(
			'conditions' => $conditions,
			'recursive' => -1
		));
	}

	public function assignProperty(Entity $entity, $alias, $value) {
		$name = Inflector::underscore($alias);

		$Model = $this->getAssociatedModel($alias);
		if ($Model) {
			if (is_array($value) && (empty($value) || Hash::numeric(array_keys($value)))) {
				$result = array();
				foreach ($value as $columns) {
					$data = array($alias => $columns);
					$result[] = $Model->entity($data);
				}
				$name = Inflector::pluralize($name);
				$value = $result;
			} else {
				$data = array($alias => $value);
				$value = $Model->entity($data);
			}
		}

		$entity->{$name} = $value;
	}

	public function getAssociatedModel($alias) {
		if ($this->schema($alias) || !preg_match('/^[A-Z]/', $alias)) {
			return null;
		}

		$Model = null;

		foreach ($this->_associations as $type) {
			if (!empty($this->{$type}[$alias])) {
				$association = $this->{$type}[$alias];

				$Model = ClassRegistry::init(array(
					'class' => $association['className'],
					'alias' => $alias,
				));

				break;
			}
		}

		if (!$Model) {
			$Model = ClassRegistry::init($alias);
		}

		if ($Model && is_a($Model, 'EntityModel')) {
			return $Model;
		}

		return null;
	}

}
