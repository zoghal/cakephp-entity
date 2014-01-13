<?php
App::uses('AppModel', 'Model');
App::uses('Entity', 'Entity.ORM');
App::uses('Hash', 'Utility');

class Table extends AppModel {

	public $entity = true;

	protected $_entityClass = null;

	protected $_savedEntityStates = array();

	public function __construct($id = false, $table = null, $ds = null) {
		parent::__construct($id, $table, $ds);
		$this->entityClass($this->name . 'Entity');
		$this->initialize(array());
	}

/**
 * Initialize a table instance. Called after the constructor.
 *
 * You can use this method to define associations, attach behaviors
 * define validation and do any other initialization logic you need.
 *
 * {{{
 *        public function initialize(array $config) {
 *                $this->belongsTo('Users');
 *                $this->belongsToMany('Tagging.Tags');
 *                $this->primaryKey('something_else');
 *        }
 * }}}
 *
 * @param array $config Configuration options passed to the constructor
 * @return void
 */
	public function initialize(array $config) {
	}

/**
 * Returns the database table name or sets a new one
 *
 * @param string $table the new table name
 * @return string
 */
	public function table($table = null) {
		if ($table !== null) {
			$this->setSource($table);
		}

		if ($this->table === null) {
			$table = get_class($this);
			$table = substr(end($table), 0, -5);
			if (empty($table)) {
				$table = $this->alias();
			}
			$this->table = Inflector::underscore($table);
		}

		return $this->table;
	}

/**
 * Returns the table alias or sets a new one
 *
 * @param string $table the new table alias
 * @return string
 */
	public function alias($alias = null) {
		if ($alias !== null) {
			$this->_alias = $alias;
		}
		if ($this->_alias === null) {
			$alias = get_class($this);
			$alias = substr($alias, 0, -5) ?: $this->table;
			$this->_alias = $alias;
		}
		return $this->_alias;
	}

/**
 * Returns the connection instance or sets a new one
 *
 * @param \Cake\Database\Connection $conn the new connection instance
 * @return \Cake\Database\Connection
 */
	public function connection($conn = null) {
		if ($conn === null) {
			return $this->getDataSource();
		}

		$this->setDataSource($conn);
		return $this->getDataSource();
	}

/**
 * Returns the primary key field name or sets a new one
 *
 * @param string $key sets a new name to be used as primary key
 * @return string
 */
	public function primaryKey($key = null) {
		if ($key !== null) {
			$this->primaryKey = $key;
		}

		return $this->primaryKey;
	}

/**
 * Returns the display field or sets a new one
 *
 * @param string $key sets a new name to be used as display field
 * @return string
 */
	public function displayField($key = null) {
		if ($key !== null) {
			$this->displayField = $key;
		}

		return $this->displayField;
	}

/**
 * Returns the class used to hydrate rows for this table or sets
 * a new one
 *
 * @param string $name the name of the class to use
 * @return string
 */
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

/**
 * Add a behavior.
 *
 * Adds a behavior to this table's behavior collection. Behaviors
 * provide an easy way to create horizontally re-usable features
 * that can provide trait like functionality, and allow for events
 * to be listened to.
 *
 * Example:
 *
 * Load a behavior, with some settings.
 *
 * {{{
 * $this->addBehavior('Tree', ['parent' => 'parentId']);
 * }}}
 *
 * Behaviors are generally loaded during Table::initialize().
 *
 * @param string $name The name of the behavior. Can be a short class reference.
 * @param array $options The options for the behavior to use.
 * @return void
 */
	public function addBehavior() {
		$this->Behaviors->load($name, $options);
	}

/**
 * Get the list of Behaviors loaded.
 *
 * This method will return the *aliases* of the behaviors attached
 * to this instance.
 *
 * @return array
 */
	public function behaviors() {
		$this->Behaviors->loaded();
	}

/**
 * Check if a behavior with the given alias has been loaded.
 *
 * @param string $name The behavior alias to check.
 * @return array
 */
	public function hasBehavior($name) {
		$this->Behaviors->loaded($name);
	}

/**
 * Returns a association objected configured for the specified alias if any
 *
 * @param string $name the alias used for the association
 * @return Cake\ORM\Association
 * @throws Exception Method 'association' not implemented
 */
	public function association($name) {
		throw new Exception("Method 'association' not implemented");
	}

/**
 * Creates a new BelongsTo association between this table and a target
 * table. A "belongs to" association is a N-1 relationship where this table
 * is the N side, and where there is a single associated record in the target
 * table for each one in this table.
 *
 * Target table can be inferred by its name, which is provided in the
 * first argument, or you can either pass the to be instantiated or
 * an instance of it directly.
 *
 * The options array accept the following keys:
 *
 * - className: The class name of the target table object
 * - targetTable: An instance of a table object to be used as the target table
 * - foreignKey: The name of the field to use as foreign key, if false none
 *   will be used
 * - conditions: array with a list of conditions to filter the join with
 * - joinType: The type of join to be used (e.g. INNER)
 *
 * This method will return the association object that was built.
 *
 * @param string $associated the alias for the target table. This is used to
 * uniquely identify the association
 * @param array $options list of options to configure the association definition
 * @return Cake\ORM\Association\BelongsTo
 */
	public function belongsTo($associated, $options = array()) {
		$this->_bindModel('belongsTo', $associated, $options);
	}

/**
 * Creates a new HasOne association between this table and a target
 * table. A "has one" association is a 1-1 relationship.
 *
 * Target table can be inferred by its name, which is provided in the
 * first argument, or you can either pass the class name to be instantiated or
 * an instance of it directly.
 *
 * The options array accept the following keys:
 *
 * - className: The class name of the target table object
 * - targetTable: An instance of a table object to be used as the target table
 * - foreignKey: The name of the field to use as foreign key, if false none
 *   will be used
 * - dependent: Set to true if you want CakePHP to cascade deletes to the
 *   associated table when an entity is removed on this table. Set to false
 *   if you don't want CakePHP to remove associated data, for when you are using
 *   database constraints.
 * - cascadeCallbacks: Set to true if you want CakePHP to fire callbacks on
 *   cascaded deletes. If false the ORM will use deleteAll() to remove data.
 *   When true records will be loaded and then deleted.
 * - conditions: array with a list of conditions to filter the join with
 * - joinType: The type of join to be used (e.g. LEFT)
 *
 * This method will return the association object that was built.
 *
 * @param string $associated the alias for the target table. This is used to
 * uniquely identify the association
 * @param array $options list of options to configure the association definition
 * @return Cake\ORM\Association\HasOne
 */
	public function hasOne($associated, $options = array()) {
		$this->_bindModel('hasOne', $associated, $options);
	}

/**
 * Creates a new HasMany association between this table and a target
 * table. A "has many" association is a 1-N relationship.
 *
 * Target table can be inferred by its name, which is provided in the
 * first argument, or you can either pass the class name to be instantiated or
 * an instance of it directly.
 *
 * The options array accept the following keys:
 *
 * - className: The class name of the target table object
 * - targetTable: An instance of a table object to be used as the target table
 * - foreignKey: The name of the field to use as foreign key, if false none
 *   will be used
 * - dependent: Set to true if you want CakePHP to cascade deletes to the
 *   associated table when an entity is removed on this table. Set to false
 *   if you don't want CakePHP to remove associated data, for when you are using
 *   database constraints.
 * - cascadeCallbacks: Set to true if you want CakePHP to fire callbacks on
 *   cascaded deletes. If false the ORM will use deleteAll() to remove data.
 *   When true records will be loaded and then deleted.
 * - conditions: array with a list of conditions to filter the join with
 * - sort: The order in which results for this association should be returned
 * - strategy: The strategy to be used for selecting results Either 'select'
 *   or 'subquery'. If subquery is selected the query used to return results
 *   in the source table will be used as conditions for getting rows in the
 *   target table.
 *
 * This method will return the association object that was built.
 *
 * @param string $associated the alias for the target table. This is used to
 * uniquely identify the association
 * @param array $options list of options to configure the association definition
 * @return Cake\ORM\Association\HasMany
 */
	public function hasMany($associated, $options = array()) {
		$this->_bindModel('hasMany', $associated, $options);
	}

/**
 * Creates a new BelongsToMany association between this table and a target
 * table. A "belongs to many" association is a M-N relationship.
 *
 * Target table can be inferred by its name, which is provided in the
 * first argument, or you can either pass the class name to be instantiated or
 * an instance of it directly.
 *
 * The options array accept the following keys:
 *
 * - className: The class name of the target table object
 * - targetTable: An instance of a table object to be used as the target table
 * - foreignKey: The name of the field to use as foreign key
 * - joinTable: The name of the table representing the link between the two
 * - through: If you choose to use an already instantiated link table, set this
 *   key to a configured Table instance containing associations to both the source
 *   and target tables in this association.
 * - cascadeCallbacks: Set to true if you want CakePHP to fire callbacks on
 *   cascaded deletes. If false the ORM will use deleteAll() to remove data.
 *   When true join/junction table records will be loaded and then deleted.
 * - conditions: array with a list of conditions to filter the join with
 * - sort: The order in which results for this association should be returned
 * - strategy: The strategy to be used for selecting results Either 'select'
 *   or 'subquery'. If subquery is selected the query used to return results
 *   in the source table will be used as conditions for getting rows in the
 *   target table.
 * - saveStrategy: Either 'append' or 'replace'. Indicates the mode to be used
 *   for saving associated entities. The former will only create new links
 *   between both side of the relation and the latter will do a wipe and
 *   replace to create the links between the passed entities when saving.
 *
 * This method will return the association object that was built.
 *
 * @param string $associated the alias for the target table. This is used to
 * uniquely identify the association
 * @param array $options list of options to configure the association definition
 * @return Cake\ORM\Association\BelongsToMany
 */
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
 * Returns a single record after finding it by its primary key, if no record is
 * found this method throws an exception.
 *
 * ###Example:
 *
 * {{{
 * $id = 10;
 * $article = $articles->get($id);
 *
 * $article = $articles->get($id, ['contain' => ['Comments]]);
 * }}}
 *
 * @param mixed primary key value to find
 * @param array $options options accepted by `Table::find()`
 * @throws NotFoundException if the record with such id could not be found
 * @return \Cake\ORM\Entity
 * @see Table::find()
 */
	public function get($primaryKey, $options = []) {
		$key = (array)$this->primaryKey();
		$conditions = array_combine($key, (array)$primaryKey);
		if (!isset($options['conditions'])) {
			$options['conditions'] = [];
		}
		$options['conditions'] = array_merge($options['conditions'], $conditions);
		$entity = $this->find('first', $options);

		if (!$entity) {
			throw new NotFoundException(sprintf(
				'Record "%s" not found in table "%s"',
				implode(',', (array)$primaryKey),
				$this->table()
			));
		}

		return $entity;
	}

/**
 * Returns the default validator object. Subclasses can override this function
 * to add a default validation set to the validator object.
 *
 * @param \Cake\Validation\Validator $validator The validator that can be modified to
 * add some rules to it.
 * @return \Cake\Validation\Validator
 * @throws Exception Method 'validationDefault' not implemented
 */
	public function validationDefault(Validator $validator) {
		throw new Exception("Method 'validationDefault' not implemented");
	}

	public function save($entity = null, $validate = true, $fieldList = array()) {
		if (!is_object($entity) || !($entity instanceof $entity)) {
			$success = parent::save($entity, $validate, $fieldList);
			if (!$success) {
				return false;
			}

			$entity = $this->newEntity($success);
			$entity->isNew(false);
			return $entity;
		}

		if ($entity->isNew() === false && !$entity->dirty()) {
			return $entity;
		}

		$isNew = $entity->isNew();
		$success = parent::save($entity, $validate, $fieldList);
		if (!$success) {
			return false;
		}

		$entity->isNew(false);
		$entity->set($this->primaryKey(), $this->getInsertID());
		return $entity;
	}

/**
 * Get the object used to marshal/convert array data into objects.
 *
 * Override this method if you want a table object to use custom
 * marshalling logic.
 *
 * @param boolean $safe Whether or not this marshaller
 *   should be in safe mode.
 * @return Cake\ORM\Marhsaller;
 * @see Cake\ORM\Marshaller
 * @throws Exception Method 'marshaller' not implemented
 */
	public function marshaller($safe = false) {
		throw new Exception("Method 'marshaller' not implemented");
	}

/**
 * Returns a new instance of an EntityValidator that is configured to be used
 * for entities generated by this table. An EntityValidator can be used to
 * process validation rules on a single or multiple entities and any of its
 * associated values.
 *
 * @return EntityValidator
 * @throws Exception Method 'entityValidator' not implemented
 */
	public function entityValidator() {
		throw new Exception("Method 'entityValidator' not implemented");
	}

/**
 * Create a new entity + associated entities from an array.
 *
 * This is most useful when hydrating request data back into entities.
 * For example, in your controller code:
 *
 * {{{
 * $article = $this->Articles->newEntity($this->request->data());
 * }}}
 *
 * The hydrated entity will correctly do an insert/update based
 * on the primary key data existing in the database when the entity
 * is saved. Until the entity is saved, it will be a detached record.
 *
 * By default all the associations on this table will be hydrated. You can
 * limit which associations are built, or include deeper associations
 * using the associations parameter:
 *
 * {{{
 * $articles = $this->Articles->newEntity(
 *   $this->request->data(),
 *   ['Tags', 'Comments' => ['associated' => ['Users']]]
 * );
 * }}}
 *
 * @param array $data The data to build an entity with.
 * @param array $associations A whitelist of associations
 *   to hydrate. Defaults to all associations
 * @throws Exception Method 'newEntity' not fully implemented
 */
	public function newEntity(array $data, $associations = null) {
		if ($associations !== null) {
			throw new Exception("Method 'newEntity' not fully implemented");
		}

		$class = $this->entityClass();

		if (!class_exists($class)) {
			App::uses($class, 'Model/Entity');
			if (!class_exists($class)) {
				$class = 'Entity';
			}
		}

		$entity = new $class($data);
		return $entity;
	}

/**
 * Create a list of entities + associated entities from an array.
 *
 * This is most useful when hydrating request data back into entities.
 * For example, in your controller code:
 *
 * {{{
 * $articles = $this->Articles->newEntities($this->request->data());
 * }}}
 *
 * The hydrated entities can then be iterated and saved. By default
 * all the associations on this table will be hydrated. You can
 * limit which associations are built, or include deeper associations
 * using the associations parameter:
 *
 * {{{
 * $articles = $this->Articles->newEntities(
 *   $this->request->data(),
 *   ['Tags', 'Comments' => ['associated' => ['Users']]]
 * );
 * }}}
 *
 * @param array $data The data to build an entity with.
 * @param array $associations A whitelist of associations
 *   to hydrate. Defaults to all associations
 * @throws Exception Method 'newEntities' not implemented
 */
	public function newEntities(array $data, $associations = null) {
		throw new Exception("Method 'newEntities' not implemented");
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

		return $this->newEntity($data);
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
		if ($one instanceof Entity) {
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
					$result[] = $Model->newEntity($data);
				}
				$name = Inflector::pluralize($name);
				$value = $result;
			} else {
				$data = array($alias => $value);
				$value = $Model->newEntity($data);
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

		if ($Model && $Model instanceof Table) {
			return $Model;
		}

		return null;
	}

}
