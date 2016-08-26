<?php namespace App\Markdown;

use League\CommonMark\Block\Element\ListItem as BaseListItem;

class ListItem extends BaseListItem {

  public function data($key=null)
  {
    if ($key === null) {
      return $this->listData;
    }

    if (isset($this->listData->{$key})) {
      return $this->listData->{$key};
    }

    return false;
  }

}
