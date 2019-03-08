<?php

namespace Code4mk\LARAPDF\Facades;

/**
 * @author    @code4mk <hiremostafa@gmail.com>
 * @author    @0devco <with@0dev.co>
 * @since     2019
 * @copyright 0dev.co (https://0dev.co)
 */

use Illuminate\Support\Facades\Facade;

class PDF extends Facade
{
  /**
   * Get the registered name of the component.
   *
   * @return string
   */
  protected static function getFacadeAccessor()
  {
      return 'larapdf';
  }
}
