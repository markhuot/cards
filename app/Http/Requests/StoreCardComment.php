<?php namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Validator;

class StoreCardComment extends FormRequest
{

  protected function getValidatorInstance()
  {
    $validator = parent::getValidatorInstance();

    $validator->addImplicitExtension('required_when_unchanged', function($attribute, $value, $parameters, $validator) {
      if (@$parameters[0] !== 'card') {
        return true;
      }

      $card = \Route::current()->getParameter('card');
      $card->stack_id = \Request::input('card.stack_id');

      $unchangedAssignees = true;
      $cardAssignees = $card->assignees->pluck('id')->toArray();
      $requestAssignees = \Request::input('card.assignee_id');
      $unchangedAssignees = $requestAssignees == $cardAssignees;

      $noAttachments = count(array_filter(\Request::file('comment.attachment', []))) == 0;

      $unchangedTags = false;

      if ($value == false && $card->isClean() && $unchangedAssignees && $noAttachments && $unchangedTags) {
        return false;
      }

      return true;
    });

    return $validator;
  }

  /**
   * Determine if the user is authorized to make this request.
   *
   * @return bool
   */
  public function authorize()
  {
    return true;
  }

  /**
   * Get the validation rules that apply to the request.
   *
   * @return array
   */
  public function rules()
  {
    return [
      'comment.content' => 'required_when_unchanged:card'
    ];
  }

}
