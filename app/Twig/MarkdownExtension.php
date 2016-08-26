<?php namespace App\Twig;

use Twig_Extension;
use Twig_SimpleFilter;
use Twig_Markup;
// use App\Markdown\MyMarkdown as MarkdownParser;
// use cebe\markdown\GithubMarkdown as MarkdownParser;
use League\CommonMark\Environment as CommonMarkEnvironment;
use League\CommonMark\CommonMarkConverter;
use App\Markdown\ListParser;
use App\Markdown\ListItem;
use App\Markdown\ListItemRenderer;

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
    $environment = CommonMarkEnvironment::createCommonMarkEnvironment();
    $environment->addBlockParser(new ListParser);
    $environment->addBlockRenderer(ListItem::class, new ListItemRenderer);

    $config = [];

    $converter = new CommonMarkConverter($config, $environment);

    return new Twig_Markup($converter->convertToHtml($content), 'utf-8');
  }

}
