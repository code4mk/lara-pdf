<?php

namespace Code4mk\LARAPDF\PDF;

/**
* @author    @code4mk <hiremostafa@gmail.com>
* @author    @0devco <with@0dev.co>
* @since     2019
* @copyright 0dev.co (https://0dev.co)
*/

use View;
use File;
use Mpdf;
use Config;
use Storage;

class PDF
{
  protected $html;
  protected $isLoadHtml = false;

  protected $marginLeft = 5;
  protected $marginTop =  5;
  protected $marginRight = 5;
  protected $marginBottom = 5;
  protected $marginHeadr =  2;
  protected $marginFooter = 2;

  protected $setFormat = 'A4';

  protected $waterMarkText;
  protected $waterMarkTextOpacity;
  protected $waterMarkTextAngle;
  protected $waterMarkTextFont;
  protected $isWatermarkText = false;

  protected $waterMarkImage;
  protected $waterMarkImageOpacity;
  protected $waterMarkImageSize;
  protected $isWatermarkImage = false;

  protected $isLargeData = false;
  protected $memorySize;
  protected $timeLimit;

  public function __construct()
  {
    //lara-pdf
  }

  public function loadView($view, $data = [], $mergeData = [])
  {
    if(!$this->isLoadHtml){
      $this->isLoadHtml = true;
      $this->html = View::make($view, $data, $mergeData)->render();
      return $this;
    }
  }

  public function loadFile($file)
  {
    if(!$this->isLoadHtml){
      $this->isLoadHtml = true;
      $this->html = File::get($file);
      return $this;
    }
  }

  public function loadHtml($htmlCode)
  {
    if(!$this->isLoadHtml){
      $this->isLoadHtml = true;
      $this->html = $htmlCode;
      return $this;
    }
  }

  public function margin($left=5, $top=5, $right=5, $bottom=5, $header=2, $footer=2){
    $this->marginLeft = $left;
    $this->marginTop = $top;
    $this->marginRight = $right;
    $this->marginBottom = $bottom;
    $this->marginHeadr = $header;
    $this->marginFooter = $footer;
    return $this;

  }
  public function textWatermark($text, $opacity = 0.1, $angle = 45,$font_family='dejavusans')
  {
    $this->waterMarkText = $text;
    $this->waterMarkTextOpacity = $opacity;
    $this->waterMarkTextAngle = $angle;
    $this->waterMarkTextFont = $font_family;
    $this->isWatermarkText = true;
    return $this;
  }

  public function imageWatermark($image, $opacity = 0.1, $size='F')
  {
    $this->waterMarkImage = $image;
    $this->waterMarkImageOpacity = $opacity;
    $this->waterMarkImageSize = $size;
    $this->isWatermarkImage = true;
    return $this;
  }

  public function paper($size)
  {
     $this->setFormat = $size;
     return $this;
  }

  public function largeData($memory = "128M", $time = 1000000)
  {
    $this->isLargeData = true;
    $this->memorySize = $memory;
    $this->timeLimit = $time;
    return $this;
  }

  public function createPdf()
  {

    if($this->isLargeData){
      ini_set("memory_limit",$this->memorySize);
      set_time_limit($this->timeLimit);
    }

    $defaultConfig = (new Mpdf\Config\ConfigVariables())->getDefaults();
    $fontDirs = $defaultConfig['fontDir'];

    $defaultFontConfig = (new Mpdf\Config\FontVariables())->getDefaults();
    $fontData = $defaultFontConfig['fontdata'];

    $config = [
      'margin_left' => $this->marginLeft,
      'margin_right' => $this->marginRight,
      'margin_top' => $this->marginTop,
      'margin_bottom' => $this->marginBottom,
      'margin_header' => $this->marginHeadr,
      'margin_footer' => $this->marginFooter,
      'format' => $this->setFormat,
      'mode' => 'UTF-8',
      'default_font' => Config::get('lara-pdf.default_font'),
      'fontDir' => array_merge($fontDirs,Config::get('lara-pdf.fontDir')),
      'fontdata' => $fontData + Config::get('lara-pdf.fontData'),
      'autoMarginPadding' => 1
    ];



    $mpdf = new \Mpdf\Mpdf($config);
    $mpdf->SetDisplayMode('real');
    $mpdf->setAutoTopMargin = 'stretch';
    $mpdf->setAutoBottomMargin  = 'stretch';

    if($this->isWatermarkText){
      $mpdf->SetWatermarkText($this->waterMarkText,$this->waterMarkTextOpacity);
      $mpdf->showWatermarkText = true;
      $mpdf->watermark_font = $this->waterMarkTextFont;
      $mpdf->watermarkAngle = $this->waterMarkTextAngle;
    }

    if($this->isWatermarkImage){
      //https://brandmark.io/logo-rank/random/beats.png
      $mpdf->SetWatermarkImage($this->waterMarkImage,$this->waterMarkImageOpacity,$this->waterMarkImageSize);
      $mpdf->showWatermarkImage = true;
    }

    $mpdf->WriteHTML($this->html);

    return $mpdf;
  }

  public function show($filename = 'larapdf.pdf')
  {
    return $this->createPdf()->Output($filename,'I');
  }

  public function download($filename = 'larapdf.pdf')
  {
    return $this->createPdf()->Output($filename,'D');
  }

  public function saveLocal($filename)
  {

    $this->createPdf()->Output($filename,'F');
  }


  public function save($filename,$driver='local')
  {
    $filePdf = explode('.',$filename);
    Storage::disk($driver)->put($filePdf[0] . '.pdf', $this->createPdf()->Output('','S'));
  }

 /**
  *
  * pdf return as string
  * useage: store | email
  *@return string
  *
  */
  public function get()
  {
    return $this->createPdf()->Output('','S');
  }
}
