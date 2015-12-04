<?php

/**
 * SortedDirectoryIterator.class.phped with PhpStorm
 * User: Robin | Juraji
 * Date: 12 okt 2015
 * Time: 16:39
 */
namespace PBPanel\Util;

class SortedDirectoryIterator extends \SplHeap
{
  /**
   * @param string $path
   * @param bool $recursive
   */
  public function __construct($path, $recursive = true)
  {
    if ($recursive) {
      $iterator = new \RecursiveIteratorIterator(
          new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS),
          \RecursiveIteratorIterator::SELF_FIRST
      );
    } else {
      $iterator = new \IteratorIterator(
          new \DirectoryIterator($path)
      );
    }

    foreach ($iterator as $item) {
      $this->insert($item);
    }
  }

  /**
   * @param \SplFileInfo $value1
   * @param \SplFileInfo $value2
   * @return int
   */
  protected function compare($value1, $value2)
  {
    return strcasecmp($value2->getRealpath(), $value1->getRealpath());
  }
}