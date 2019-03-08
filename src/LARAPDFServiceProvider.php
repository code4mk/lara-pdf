<?php

namespace Code4mk\LARAPDF;

/**
* @author    @code4mk <hiremostafa@gmail.com>
* @author    @0devco <with@0dev.co>
* @since     2019
* @copyright 0dev.co (https://0dev.co)
*/

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\AliasLoader;
use Code4mk\LARAPDF\PDF\PDF as PDF;

class LARAPDFServiceProvider extends ServiceProvider
{
  /**
   * Bootstrap any application services.
   *
   * @return void
   */
   public function boot()
   {

     // publish config
      $this->publishes([
       __DIR__ . '/../config/lara-pdf.php' => config_path('lara-pdf.php'),
      ], 'config');


      //load alias
      AliasLoader::getInstance()->alias('LARAPDF', 'Code4mk\LARAPDF\Facades\PDF');


   }

  /**
   * Register any application services.
   *
   * @return void
   */
   public function register()
   {
     $this->app->bind('larapdf', function () {
      return new PDF();
     });
   }
}
