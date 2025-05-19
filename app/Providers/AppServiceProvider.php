<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $data['themeTrue'] = template(true);
        $data['basic'] = (object) config('basic');

        View::share($data);

        Blade::directive('activeLink', function ($routes) {
            return "<?php echo in_array(request()->route()->getName(), explode(',', $routes)) ? 'active' : ''; ?>";
        });
    }
}
