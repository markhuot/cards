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

use League\CommonMark\Block\Element\ListData as BaseListData;

class ListData extends BaseListData
{
    /**
     * @var boolean|false
     */
    public $isTask = false;

    /**
     * @var boolean|false
     */
    public $isTaskComplete = false;

}
