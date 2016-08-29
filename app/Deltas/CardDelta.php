<?php namespace App\Deltas;

use App\Card;

class CardDelta {

  protected $properties = [];

  public function __construct(Card $card, array $parameters)
  {
    if ($card->assignees->pluck('id')->toArray() != @$parameters['assignee_id']) {
      $this->properties['assignee_id'] = @$parameters['assignee_id'];
    }
  }

  public function __toString()
  {
    return json_encode([
      'version' => 1,
      'properties' => $this->properties,
    ]);
  }

}
