<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\User;
use App\Project;
use App\Stack;
use App\Card;
use App\Comment;
use App\Tag;

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

    $project = new Project;
    $project->name = 'Project Manhattan';
    $project->save();

    $user = new User;
    $user->name = 'Liz Lemon';
    $user->email = 'liz@tgs.com';
    $user->password = Hash::make('password');
    $user->save();
    $project->users()->attach($user);

    $user = new User;
    $user->name = 'Tracy Morgan';
    $user->email = 'tracy@tgs.com';
    $user->password = Hash::make('password');
    $user->save();
    $project->users()->attach($user);

    $user = new User;
    $user->name = 'Jack Donaghy';
    $user->email = 'jack@nbcuniversal.com';
    $user->password = Hash::make('password');
    $user->save();
    $project->users()->attach($user);

    $user = new User;
    $user->name = 'Mark Huot';
    $user->email = 'mark@markhuot.com';
    $user->password = Hash::make('password');
    $user->save();
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

    $complete = new Stack;
    $complete->project_id = $project->getKey();
    $complete->name = 'Complete';
    $complete->order = 2;
    $complete->save();

    $card = new Card;
    $card->stack_id = $complete->getKey();
    $card->user_id = $user->getKey();
    $card->title = 'Cards need a completion state';
    $card->description = "There needs to be a way to close cards so years of history doesn't clutter the UI.\n\n- [ ] add a `complete` boolean to the Card model\n- [ ] hide `complete` cards from the ui, unless filtered\n\nOptionally,\n\n- [ ] specify Stacks that should automatically open or close a Card when moved";
    $card->complete = true;
    $card->order = 0;
    $card->save();
    $user->follow($card);
    $card->tag_string = 'ux';

    $card = new Card;
    $card->stack_id = $backlog->getKey();
    $card->user_id = $user->getKey();
    $card->title = 'Minimize stacks';
    $card->description = "For long workflows a user may not be interested in _every_ Stack. The ability to minimize stacks would allow a user to optimize the view for their use case.";
    $card->order = 0;
    $card->save();
    $user->follow($card);
    $card->tag_string = 'ui';

    $card = new Card;
    $card->stack_id = $backlog->getKey();
    $card->user_id = $user->getKey();
    $card->title = 'Javascript notifications';
    $card->description = "This could be most helpful to move and create new cards on other devices without reloading.";
    $card->order = 0;
    $card->save();
    $user->follow($card);
    $card->tag_string = 'javascript';

    $card = new Card;
    $card->stack_id = $backlog->getKey();
    $card->user_id = $user->getKey();
    $card->title = 'Stack view';
    $card->description = "Clicking a stack header should show just the cards in that stack at full width. This could be a nice way to work through a project backlog.";
    $card->order = 0;
    $card->save();
    $user->follow($card);
    $card->tag_string = 'ui';

    $comment = new Comment;
    $comment->source_type = get_class($card);
    $comment->source_id = $card->getKey();
    $comment->user_id = $user->getKey();
    $comment->content = 'This might be nice to use the Desktop Neo style of "long card rows" to retain the card feel.';
    $comment->save();

    $card = new Card;
    $card->stack_id = $backlog->getKey();
    $card->user_id = $user->getKey();
    $card->title = 'Invite email could be better worded';
    $card->description = "The current language does not help the user know what to do once invited.";
    $card->order = 1;
    $card->save();
    $user->follow($card);
    $card->tag_string = 'copy ui';

    $card = new Card;
    $card->stack_id = $complete->getKey();
    $card->user_id = $user->getKey();
    $card->title = 'Drag and drop';
    $card->description = "Cards should be able to drag/drop across stacks.";
    $card->order = 1;
    $card->save();
    $user->follow($card);
    $card->tag_string = 'javascript ui';

    $card = new Card;
    $card->stack_id = $inProgress->getKey();
    $card->user_id = $user->getKey();
    $card->title = 'Comment meta';
    $card->description = "Comments should keep track of what changed about a card with each save.\n\n- [x] add `comments.meta` field\n- [ ] store json of the change\n- [ ] add a `CommentMetaRenderer` class that prints a string version of the change";
    $card->order = 1;
    $card->save();
    $user->follow($card);
    $card->tag_string = 'ux function';

    $card->assignees()->sync([$user->getKey()]);

    $comment = new Comment;
    $comment->source_type = get_class($card);
    $comment->source_id = $card->getKey();
    $comment->user_id = $user->getKey();
    $comment->content = "This is a test of the comment system? there's not a lot to it, just the text of the comment. Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.";
    $comment->save();

    $comment = new Comment;
    $comment->source_type = get_class($card);
    $comment->source_id = $card->getKey();
    $comment->user_id = $user->getKey();
    $comment->content = "This is a comment with a task list, you can check things now.\n\n- [ ] this is unchecked\n- [x] this is checked";
    $comment->save();

    $comment = new Comment;
    $comment->source_type = get_class($card);
    $comment->source_id = $card->getKey();
    $comment->user_id = $user->getKey();
    $comment->content = "Laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.\n\n- this is just a regular old list, it's not very fancy. aboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.\n- there are short list items\n-and longer list items aboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.";
    $comment->save();

    $comment = new Comment;
    $comment->source_type = get_class($card);
    $comment->source_id = $card->getKey();
    $comment->user_id = $user->getKey();
    $comment->content = "This has a block quote,\r\n\r\n> Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.\r\n\r\nWee, that was a fun quote!";
    $comment->save();

    $comment = new Comment;
    $comment->source_type = get_class($card);
    $comment->source_id = $card->getKey();
    $comment->user_id = $user->getKey();
    $comment->content = "What about numbered lists?\n\n1. this is something that goes first\n2.Then a second thing\n3. finally a third thing\n\nAnd then some closing text.";
    $comment->save();

    $card = new Card;
    $card->stack_id = $complete->getKey();
    $card->user_id = $user->getKey();
    $card->title = 'Card ID';
    $card->description = "Cards should be easily identified by a numeric id.";
    $card->order = 1;
    $card->save();
    $user->follow($card);
    $card->tag_string = 'ux';

    $card = new Card;
    $card->stack_id = $releasePlanning->getKey();
    $card->user_id = $user->getKey();
    $card->title = 'Markdown parsing needs syntax highlighting';
    $card->description = "Currently it's just a `pre` with nothing else. Some syntax coloring would be nice.";
    $card->order = 1;
    $card->save();
    $user->follow($card);
    $card->tag_string = 'wishlist';

    $card = new Card;
    $card->stack_id = $complete->getKey();
    $card->user_id = $user->getKey();
    $card->title = 'Javascript?';
    $card->description = "Currently the app doesn't use _any_ Javascript. It may be worth adding some to ease the comment leaving experience.";
    $card->order = 1;
    $card->save();
    $user->follow($card);
    $card->tag_string = 'javascript';

    $card = new Card;
    $card->stack_id = $backlog->getKey();
    $card->user_id = $user->getKey();
    $card->title = 'Activity view';
    $card->description = "There's no way to sort or see cards that have been recently updated.\n\n- [ ] new \"activity\" UI";
    $card->order = 1;
    $card->save();
    $user->follow($card);
    $card->tag_string = 'ux';

    $card = new Card;
    $card->stack_id = $inProgress->getKey();
    $card->user_id = $user->getKey();
    $card->title = 'Arbitrary card details';
    $card->description = "I'd like to add card estimates or story points, but it seems overly specific. Maybe, instead, add a settings screen where you can ask for any number of additional fields on a card. Could be integer, boolean, or text fields… maybe some day enhanced fields like star ratings and chat logs.";
    $card->order = 1;
    $card->save();
    $user->follow($card);
    $card->tag_string = 'ux';

    $card = new Card;
    $card->stack_id = $complete->getKey();
    $card->user_id = $user->getKey();
    $card->title = 'Drag should use proxy';
    $card->description = "Currently dragging a card uses the actual card element, causing a jump in content. A proxy element should be used so a drag does not cause a reflow of content.";
    $card->order = 1;
    $card->save();
    $user->follow($card);
    $card->tag_string = 'javascript ui';

    $tag = Tag::where('name', '=', 'javascript')->first();
    $tag->color = 'ffcc3f';
    $tag->save();
  }
}
