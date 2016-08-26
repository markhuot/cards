<?php

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * Original code based on the CommonMark JS reference parser (https://bitly.com/commonmark-js)
 *  - (c) John MacFarlane
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Markdown;

use League\CommonMark\Block\Element\AbstractBlock;
use League\CommonMark\Block\Element\ListItem;
use League\CommonMark\ElementRendererInterface;
use League\CommonMark\HtmlElement;
use League\CommonMark\Block\Renderer\BlockRendererInterface;

class ListItemRenderer implements BlockRendererInterface
{
    /**
     * @param ListItem                 $block
     * @param ElementRendererInterface $htmlRenderer
     * @param bool                     $inTightList
     *
     * @return string
     */
    public function render(AbstractBlock $block, ElementRendererInterface $htmlRenderer, $inTightList = false)
    {
        if (!($block instanceof ListItem)) {
            throw new \InvalidArgumentException('Incompatible block type: ' . get_class($block));
        }

        $contents = $htmlRenderer->renderBlocks($block->children(), $inTightList);
        if (substr($contents, 0, 1) === '<') {
            $contents = "\n" . $contents;
        }
        if (substr($contents, -1, 1) === '>') {
            $contents .= "\n";
        }

        if ($block->data('isTask')) {
          $id = uniqid();
          $checked = $block->data('isTaskComplete') ? 'checked' : '';
          $contents = "<input type='checkbox' name='line' value='{$block->getStartLine()}' id='{$id}' {$checked} /> <label for='{$id}'>{$contents}</label>";
        }

        $attrs = [];
        foreach ($block->getData('attributes', []) as $key => $value) {
            $attrs[$key] = $htmlRenderer->escape($value, true);
        }

        $li = new HtmlElement('li', $attrs, $contents);

        return $li;
    }
}
