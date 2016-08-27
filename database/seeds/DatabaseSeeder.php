<?php

use Illuminate\Database\Seeder;
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
    $user->password = 'password';
    $user->save();

    $project = new Project;
    $project->name = 'Project Manhattan';
    $project->save();

    $project->users()->attach($user);

    $backlog = new Stack;
    $backlog->project_id = $project->getKey();
    $backlog->name = 'Backlog';
    $backlog->save();

    $card = new Card;
    $card->stack_id = $backlog->getKey();
    $card->title = 'Cards need a completion state';
    $card->description = 'There needs to be a way to close cards so years of history doesn\'t clutter the UI.\n\n- [ ] add a `complete` boolean to the Card model\n- [ ] hide `complete` cards from the ui, unless filtered\n\nOptionally,\n\n- [ ] specify Stacks that should automatically open or close a Card when moved';
    $card->order = 0;
    $card->save();
  }
}
