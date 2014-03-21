[![Build Status](https://travis-ci.org/josegonzalez/cakephp-entity.png?branch=master)](https://travis-ci.org/josegonzalez/cakephp-entity) [![Coverage Status](https://coveralls.io/repos/josegonzalez/cakephp-entity/badge.png?branch=master)](https://coveralls.io/r/josegonzalez/cakephp-entity?branch=master) [![Total Downloads](https://poser.pugx.org/josegonzalez/cakephp-entity/d/total.png)](https://packagist.org/packages/josegonzalez/cakephp-entity) [![Latest Stable Version](https://poser.pugx.org/josegonzalez/cakephp-entity/v/stable.png)](https://packagist.org/packages/josegonzalez/cakephp-entity)

# CakePHP Entity Plugin

## Background

- find() now returns an array of objects instead of arrays of arrays.
- 100% compatible with the standard Model.
- Open source. Available on GitHub. MIT Lisense.
- CakePHP 2.4+, PHP 5.4+

Originally: https://github.com/kanshin/CakeEntity

## Requirements

* CakePHP 2.4
* PHP 5.3
* Patience

## Installation

_[Using [Composer](http://getcomposer.org/)]_

Add the plugin to your project's `composer.json` - something like this:

	{
		"require": {
			"josegonzalez/cakephp-entity": "dev-master"
		}
	}

Because this plugin has the type `cakephp-plugin` set in it's own `composer.json`, composer knows to install it inside your `/Plugins` directory, rather than in the usual vendors file. It is recommended that you add `/Plugins/Entity` to your .gitignore file. (Why? [read this](http://getcomposer.org/doc/faqs/should-i-commit-the-dependencies-in-my-vendor-directory.md).)

_[Manual]_

* Download this: [http://github.com/josegonzalez/cakephp-entity/zipball/master](http://github.com/josegonzalez/cakephp-entity/zipball/master)
* Unzip that download.
* Copy the resulting folder to `app/Plugin`
* Rename the folder you just copied to `Entity`

_[GIT Submodule]_

In your app directory type:

    git submodule add -b master git://github.com/josegonzalez/cakephp-entity.git Plugin/Entity
    git submodule init
    git submodule update

_[GIT Clone]_

In your `Plugin` directory type:

    git clone -b master git://github.com/josegonzalez/cakephp-entity.git Entity

_[Composer]_

Add the following to your `composer.json`:

    "cakephp-entity": "1.0.0"

And then run the `composer update` command to install the dependency.

### Enable plugin

In 2.0 you need to enable the plugin in your `app/Config/bootstrap.php` file:

    CakePlugin::load('Entity');

If you are already using `CakePlugin::loadAll();`, then this is not necessary.

## Usage

CakeEntity doesn't change anything with your current the installation.
You'll have to enable the functionality by indicating it be used.
This is for compatibility reasons.

Use `Table` as the super class of models where you'd like to activate the plugin.

```php
<?php
App::uses('Table', 'Entity.ORM');

class Post extends Table {

}
?>
```

Then, in the options of the `find`, specify `entity` => true:

```php
<?php
$entity = $this->Post->find('first', array('entity' => true));
?>
```

You may also set the `$entity` property on your model to true to return entities:

```php
<?php
$this->Post->entity = true; 
$entity = $this->Post->find('first');
?>
```

Now the `$result` includes the array of objects (entities).

### Entity class

The `Entity` class is the default class used as the result of objects.
If there is a class with the model's name + 'Entity', that class is
used instead. (i.e. For model "Post", the class "PostEntity" is used)

```php
<?php
App::uses('Table', 'Entity.ORM');
App::uses('PostEntity', 'Model/Entity');

class Post extends Table {
}
?>
```

```php
<?php
App::uses('Entity', 'Model/Entity');

class PostEntity extends Entity {
    // Your custom logic here
}
?>
```

### Array access for Entity object

Entity's property's can be accessed using array syntax:

    echo $post['title']; // == $post->title

Array access can also be used with Smarty:

    Hello, my name is {$post.author.name|h}.

Array access introduces two important features:

- Access control for security
- Cache for performance

### For more information

[Introducing CakeEntity (PHP study in Tokyo 10/1/2011)](http://www.slideshare.net/basuke/introducing-cakeentity-9496875)

## License

Copyright (c) 2012 Jose Diaz-Gonzalez

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
