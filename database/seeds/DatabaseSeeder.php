<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\User;
use App\Project;
use App\Stack;
use App\Card;

class DatabaseSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    // $this->call(UsersTableSeeder::class);

    $user = new User;
    $user->name = 'Mark Huot';
    $user->email = 'mark@markhuot.com';
    $user->password = Hash::make('password');
    $user->save();

    $project = new Project;
    $project->name = 'Project Manhattan';
    $project->save();

    $project->users()->attach($user);

    $backlog = new Stack;
    $backlog->project_id = $project->getKey();
    $backlog->name = 'Backlog';
    $backlog->order = 0;
    $backlog->save();

    $releasePlanning = new Stack;
    $releasePlanning->project_id = $project->getKey();
    $releasePlanning->name = 'Release Planning';
    $releasePlanning->order = 1;
    $releasePlanning->save();

    $inProgress = new Stack;
    $inProgress->project_id = $project->getKey();
    $inProgress->name = 'In Progress';
    $inProgress->order = 2;
    $inProgress->save();

    $card = new Card;
    $card->stack_id = $backlog->getKey();
    $card->user_id = $user->getKey();
    $card->title = 'Cards need a completion state';
    $card->description = 'There needs to be a way to close cards so years of history doesn\'t clutter the UI.\n\n- [ ] add a `complete` boolean to the Card model\n- [ ] hide `complete` cards from the ui, unless filtered\n\nOptionally,\n\n- [ ] specify Stacks that should automatically open or close a Card when moved';
    $card->order = 0;
    $card->save();

    $card = new Card;
    $card->stack_id = $backlog->getKey();
    $card->user_id = $user->getKey();
    $card->title = 'Invite email could be better worded';
    $card->description = 'The current language does not help the user know what to do once invited.';
    $card->order = 1;
    $card->save();

    $card = new Card;
    $card->stack_id = $inProgress->getKey();
    $card->user_id = $user->getKey();
    $card->title = 'Drag and drop';
    $card->description = 'Cards should be able to drag/drop across stacks.';
    $card->order = 1;
    $card->save();

    $card = new Card;
    $card->stack_id = $releasePlanning->getKey();
    $card->user_id = $user->getKey();
    $card->title = 'Markdown parsing needs syntax highlighting';
    $card->description = 'Currently it\'s just a `pre` with nothing else. Some syntax coloring would be nice.';
    $card->order = 1;
    $card->save();

    $card = new Card;
    $card->stack_id = $releasePlanning->getKey();
    $card->user_id = $user->getKey();
    $card->title = 'Javascript?';
    $card->description = 'Currently the app doesn\'t use _any_ Javascript. It may be worth adding some to ease the comment leaving experience.';
    $card->order = 1;
    $card->save();

    $card = new Card;
    $card->stack_id = $backlog->getKey();
    $card->user_id = $user->getKey();
    $card->title = 'Activity view';
    $card->description = 'There\'s no way to sort or see cards that have been recently updated.\n\n- [ ] new "activity" UI';
    $card->order = 1;
    $card->save();

    $card = new Card;
    $card->stack_id = $backlog->getKey();
    $card->user_id = $user->getKey();
    $card->title = 'Arbitrary card details';
    $card->description = 'I\'d like to add card estimates or story points, but it seems overly specific. Maybe, instead, add a settings screen where you can ask for any number of additional fields on a card. Could be integer, boolean, or text fields… maybe some day enhanced fields like star ratings and chat logs.';
    $card->order = 1;
    $card->save();
  }
}
