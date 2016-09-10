<?php namespace App\Twig;

use Twig_Node_Expression_Array;
use Twig_Compiler;
use Twig_Node;
use Cache;

class CacheNode extends Twig_Node {

  public function __construct($key, $body, $line, $tag = null)
  {
    parent::__construct(array('body' => $body), array('key' => $key), $line, $tag);
  }

  public function compile(Twig_Compiler $compiler) 
  {
    $compiler
      ->addDebugInfo($this)
      ->write('if ($this->env->getExtension("cache")->shouldShowDebug()) { echo "<link href=\"https://fonts.googleapis.com/css?family=VT323\" rel=\"stylesheet\">"; }'."\n")
      ->write('if ($this->env->getExtension("cache")->shouldShowDebug()) { echo "<style type=\"text/css\">.cache-region.hit .cache-region { border: none !important; } .cache-region.hit .cache-region .cache-region__title { display: none !important; }</style>"; }'."\n")
      ->write('$cacheValue = $this->env->getExtension("cache")->getValueForKey(')
      ->subcompile($this->getAttribute('key'))
      ->write(');'."\n")
      ->write('if ($cacheValue === null) {'."\n")
      ->indent()
      ->write('ob_start();'."\n")
      ->subcompile($this->getNode('body'))
      ->write('$cacheValue = ob_get_contents(); ob_end_clean();'."\n")
      ->write('if ($this->env->getExtension("cache")->shouldShowDebug()) { echo "<span class=\"cache-region miss\" style=\"display: inline-block; border:1px solid red;\">"; }'."\n")
      ->write('if ($this->env->getExtension("cache")->shouldShowDebug()) { echo "<span class=\"cache-region__title\" style=\"display: block; font-family:VT323; font-size:10px; color: white; background-color: red; padding: 5px;\">"; }'."\n")
      ->write('if ($this->env->getExtension("cache")->shouldShowDebug()) { echo $this->env->getExtension("cache")->renderKey(')
      ->subcompile($this->getAttribute('key'))
      ->write('); }'."\n")
      ->write('if ($this->env->getExtension("cache")->shouldShowDebug()) { echo "</span>"; }'."\n")
      ->write('echo $cacheValue;'."\n")
      ->write('if ($this->env->getExtension("cache")->shouldShowDebug()) { echo "</span>"; }'."\n")
      ->write('$this->env->getExtension("cache")->putValueForKeyForever(')
      ->subcompile($this->getAttribute('key'))
      ->write(', $cacheValue);'."\n")
      ->outdent()
      ->write('} else {'."\n")
      ->indent()
      ->write('if ($this->env->getExtension("cache")->shouldShowDebug()) { echo "<span class=\"cache-region hit\" style=\"display: inline-block; border:1px solid green;\">"; }'."\n")
      ->write('if ($this->env->getExtension("cache")->shouldShowDebug()) { echo "<span class=\"cache-region__title\" style=\"display: block; font-family:VT323; font-size:10px; color: white; background-color: green; padding: 5px;\">"; }'."\n")
      ->write('if ($this->env->getExtension("cache")->shouldShowDebug()) { echo $this->env->getExtension("cache")->renderKey(')
      ->subcompile($this->getAttribute('key'))
      ->write('); }'."\n")
      ->write('if ($this->env->getExtension("cache")->shouldShowDebug()) { echo "</span>"; }'."\n")
      ->write('echo $cacheValue;'."\n")
      ->write('if ($this->env->getExtension("cache")->shouldShowDebug()) { echo "</span>"; }'."\n")
      ->outdent()
      ->write('}'."\n")
    ;

    // dd($compiler);
  }

}