<?php namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Collection;

class AppServiceProvider extends ServiceProvider
{
  /**
   * Bootstrap any application services.
   *
   * @return void
   */
  public function boot()
  {
    Collection::macro('wrap', function($tag) {
      return collect($this->items)->map(function($item) use ($tag) {
        return "<{$tag}>".$item."</{$tag}>";
      });
    });

    Collection::macro('link', function($label, $href) {
      return collect($this->items)->map(function($item) use ($label, $href) {
        return '<a href="'.array_get($item, $href).'">'.array_get($item, $label).'</a>';
      });
    });
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
