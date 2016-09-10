<?php namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Twig\MarkdownExtension;
use App\Twig\UnderscoreExtension;
use App\Twig\CacheExtension;

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
    app('twig')->addExtension(new CacheExtension);
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
