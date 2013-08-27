# CakePHP Entity Plugin [![Build Status](https://travis-ci.org/josegonzalez/cakephp-entity.png?branch=master)](https://travis-ci.org/josegonzalez/cakephp-entity)

## Background

- find() now returns array of objects instead of arrays of arrays.
- 100% compatible with the standard Model.
- Open source. Available on GitHub. MIT Lisense.
- CakePHP 2.4+, PHP 5.3+

Originally: https://github.com/kanshin/CakeEntity

## Requirements

* CakePHP 2.4
* PHP 5.3
* Patience

## Installation

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

In 2.0 you need to enable the plugin your `app/Config/bootstrap.php` file:

    CakePlugin::load('Entity');

If you are already using `CakePlugin::loadAll();`, then this is not necessary.

## Usage

CakeEntity doesn't change anything with your current the installation.
You have to enables the functionality by indicating to use it.
This is for compatibility reasons.

Use `EntityModel` as the super class of models where you would like to activate the plugin.

```php
<?php
App::uses('EntityModel', 'Entity.Model');

class Post extends EntityModel {

}

```

Then in the options of the `find`, specify `entity` => true:

```php
<?php
$entity = $this->Post->find('all', array(
    'conditions' => ...
    'order' => ...
    'entity' => true,
));
```
Now the `$result` includes the array of objects (entities).


### Entity class

The `Entity` class is the default class used as the result of objects.
If there is a class with the model's name + 'Entity', that class is
uses instead. (i.e. For model "Post", the class "PostEntity" is used)

```php
<?php
App::uses('EntityModel', 'Entity.Model');
App::uses('PostEntity', 'Model/Entity');

class Post extends EntityModel {
}
```

```php
<?php
App::uses('Entity', 'Model/Entity');

class PostEntity extends Entity {
    // Your custom logic here
}
```

### Array access for Entity object


Entity's property can be accessed using array syntax:

    echo $post['title']; // == $post->title

Array access can also be used with Smarty:

    Hello, my name is {$post.author.name|h}.

Array access introduces two important feature:

- access control for security.
- cache for performance.

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
