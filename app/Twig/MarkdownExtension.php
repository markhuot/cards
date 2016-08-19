<?php namespace App\Twig;

use Twig_Extension;
use Twig_SimpleFilter;
use Twig_Markup;
use cebe\markdown\GithubMarkdown as MarkdownParser;

class MarkdownExtension extends Twig_Extension {

  public function getName()
  {
    return 'markdown';
  }

  public function getFilters()
  {
    return [
      new Twig_SimpleFilter('markdown', [$this, 'parse']),
    ];
  }

  public function parse($content)
  {
    $parser = new MarkdownParser();
    return new Twig_Markup($parser->parse($content), 'utf-8');
  }

}
