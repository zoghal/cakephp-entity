<?php

/**
 * Class UserFixture
 *
 */
class UserFixture extends CakeTestFixture {

	public $name = 'User';

/**
 * fields property
 *
 * @var array
 */
	public $fields = [
		'id' => ['type' => 'integer', 'null' => false, 'default' => null, 'key' => 'primary'],
		'username' => ['type' => 'string', 'null' => true],
		'password' => ['type' => 'string', 'null' => true],
		'created' => ['type' => 'datetime', 'null' => true],
		'updated' => ['type' => 'datetime', 'null' => true],
		'indexes' => ['PRIMARY' => ['column' => 'id', 'unique' => 1]],
	];

/**
 * records property
 *
 * @var array
 */
	public $records = [
		['username' => 'mariano', 'password' => '$2a$10$u05j8FjsvLBNdfhBhc21LOuVMpzpabVXQ9OpC2wO3pSO0q6t7HHMO', 'created' => '2007-03-17 01:16:23', 'updated' => '2007-03-17 01:18:31'],
		['username' => 'nate', 'password' => '$2a$10$u05j8FjsvLBNdfhBhc21LOuVMpzpabVXQ9OpC2wO3pSO0q6t7HHMO', 'created' => '2008-03-17 01:18:23', 'updated' => '2008-03-17 01:20:31'],
		['username' => 'larry', 'password' => '$2a$10$u05j8FjsvLBNdfhBhc21LOuVMpzpabVXQ9OpC2wO3pSO0q6t7HHMO', 'created' => '2010-05-10 01:20:23', 'updated' => '2010-05-10 01:22:31'],
		['username' => 'garrett', 'password' => '$2a$10$u05j8FjsvLBNdfhBhc21LOuVMpzpabVXQ9OpC2wO3pSO0q6t7HHMO', 'created' => '2012-06-10 01:22:23', 'updated' => '2012-06-12 01:24:31'],
	];
}
