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

        // Add this view composer for partner sidebar
        View::composer('partner.layouts.sidebar', function ($view) {
            $timezones = \DateTimeZone::listIdentifiers();
            $data = [];
            foreach ($timezones as $timezone) {
                $carbon = \Carbon\Carbon::now($timezone);
                $offsetInSeconds = $carbon->getOffset();
                $offsetInHours = $offsetInSeconds / 3600;
                $data[] = [
                    'timezone' => $timezone,
                    'offset' => sprintf("UTC %s%02d:%02d", $offsetInHours < 0 ? '-' : '+', abs($offsetInHours), (abs($offsetInHours) * 60) % 60)
                ];
            }
            $user = \Auth::guard('partner')->user();
            $usertimezone = $user ? $user->timezone : config('app.timezone');
            $view->with(compact('data', 'usertimezone'));
        });

        Blade::directive('activeLink', function ($routes) {
            return "<?php echo in_array(request()->route()->getName(), explode(',', $routes)) ? 'active' : ''; ?>";
        });
    }
}
