<?php namespace App\Twig;

use Twig_Extension;
use Twig_SimpleFilter;
use Twig_Markup;

class UnderscoreExtension extends Twig_Extension {

  public function getName()
  {
    return 'underscore';
  }

  public function getFilters()
  {
    return [
      new Twig_SimpleFilter('wrap', [$this, 'wrap']),
    ];
  }

  public function wrap($arr, $tag)
  {
    foreach ($arr as &$val) {
      $val = new Twig_Markup('<strong>'.$val.'</strong>', 'utf-8');
    }

    return $arr;
  }

}
