<?php namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Twig\MarkdownExtension;
use App\Twig\UnderscoreExtension;

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
    app('twig')->addExtension(new UnderscoreExtension);
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
