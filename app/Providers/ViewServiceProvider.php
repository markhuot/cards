<?php namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Twig\MarkdownExtension;

class ViewServiceProvider extends ServiceProvider
{
  /**
   * Bootstrap any application services.
   *
   * @return void
   */
  public function boot()
  {
    app('twig')->addExtension(new MarkdownExtension);
  }

  /**
   * Register any application services.
   *
   * @return void
   */
  public function register()
  {
    //
  }
}
