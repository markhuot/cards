<?php namespace App\ModelTraits;

trait Auditable {

  protected $disableAudit = false;

  protected $auditHistory = [];

  public static function bootAuditable()
  {
    static::saving(function($model) {
      $model->auditSaving();
    });
  }

  protected function auditSaving()
  {
    if ($this->disableAudit == true) { return; }

    foreach ($this->getDirty() as $key => $newValue) {
      $this->auditHistory[] = [
        'key' => $key,
        'removed' => $this->getOriginal($key),
        'added' => $newValue,
      ];
    }
  }

  protected function auditSyncing($key, array $newValues)
  {
    if ($this->disableAudit == true) { return; }

    $original = $this->{$key}->pluck('id');
    $new = collect($newValues);

    $removed = $original->diff($new);
    $added = $new->diff($original);

    if (count($removed) || count($added)) {
      $this->auditHistory[] = [
        'key' => $key,
        'removed' => $removed,
        'added' => $added,
      ];
    }
  }

  public function getAuditHistory()
  {
    return $this->auditHistory;
  }

}
