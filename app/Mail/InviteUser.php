<?php namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Invite;

class InviteUser extends Mailable
{
  use Queueable, SerializesModels;

  /**
   * The invite being sent
   * @var App\Invite
   */
  public $invite;

  /**
   * Create a new message instance.
   *
   * @return void
   */
  public function __construct(Invite $invite)
  {
    $this->invite = $invite;
  }

  /**
   * Build the message.
   *
   * @return $this
   */
  public function build()
  {
    return $this->view('emails.invite.user');
  }
}
