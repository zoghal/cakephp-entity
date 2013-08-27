<?php

/**
 * All Entity Test
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 */
class AllEntityTest extends PHPUnit_Framework_TestSuite {

  public static function suite() {
    $suite = new CakeTestSuite('All Entity plugin tests');
    $path = App::pluginPath('Entity');
    $testPath = $path . DS . 'Test' . DS . 'Case';
    if (!is_dir($testPath)) {
      continue;
    }

    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($testPath), RecursiveIteratorIterator::CHILD_FIRST);
    foreach ($iterator as $folder) {
      $folder = (string)$folder;
      $folderName = basename($folder);

      if ($folderName === '.' || $folderName === '..') {
        continue;
      }
      $suite->addTestDirectory($folder);
    }

    $suite->addTestDirectory($testPath);
    return $suite;
  }

}
