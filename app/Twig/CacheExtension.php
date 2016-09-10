<?php namespace App\Twig;

use Cache;
use Twig_Extension;
use Twig_SimpleFilter;
use Twig_Markup;
use App\Twig\CacheTokenParser;

class CacheExtension extends Twig_Extension {

  public function getName()
  {
    return 'cache';
  }

  public function getTokenParsers()
  {
    return [
      new CacheTokenParser()
    ];
  }

  public function getValueForKey($key)
  {
    if (is_array($key)) {
      $key = implode('-', $key);
    }

    return Cache::get($key);
  }

  public function renderKey($key) {
    if (is_array($key)) {
      $key = implode('-', $key);
    }

    return $key;
  }

  public function putValueForKeyForever($key, $value)
  {
    if (is_array($key)) {
      $key = implode('-', $key);
    }

    return Cache::forever($key, $value);
  }

  public function putValueForKey($key, $value, $ttl)
  {
    return Cache::forever($key, $value, $ttl);
  }

  public function shouldShowDebug()
  {
    return config('view.debug_cache');
  }

}
