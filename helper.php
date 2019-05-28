<?php
use think\facade\Request;

if (!function_exists('script_path')) {
    function script_path()
    {
        if ('cli' == PHP_SAPI) {
            $scriptName = realpath($_SERVER['argv'][0]);
        } else {
            $scriptName = $_SERVER['SCRIPT_FILENAME'];
        }

        return realpath(dirname($scriptName)).'/';
    }
}

if (!function_exists('app_path')) {
    function app_path($path = '')
    {
        return env('app_path').ltrim($path, '/');
    }
}

if (!function_exists('public_path')) {
    function public_path($path = '')
    {
        return script_path().ltrim($path, '/');
        // return app_path('../public/').ltrim($path, '/');
    }
}

if (!function_exists('admin_path')) {
    function admin_path($path = '')
    {
        return __DIR__.'/'.ltrim($path, '/');
    }
}

if (!function_exists('admin_config_path')) {
    function admin_config_path($path = '')
    {
        return admin_path('config/').ltrim($path, '/');
    }
}

if (!function_exists('admin_route_path')) {
    function admin_route_path($path = '')
    {
        return admin_path('route/').ltrim($path, '/');
    }
}

if (!function_exists('admin_view_path')) {
    function admin_view_path($path = '')
    {
        return admin_path('resource/view/').ltrim($path, '/');
    }
}

// if (!function_exists('site_config')) {
//     function site_config($key)
//     {
//         return Config::get($key);
//     }
// }

if (!function_exists('array_deep_merge')) {
    function array_deep_merge(array $a, array $b)
    {
        foreach ($a as $key => $val) {
            if (isset($b[$key])) {
                if (gettype($a[$key]) != gettype($b[$key])) {
                    continue;
                }
                if (is_array($a[$key])) {
                    $a[$key] = array_deep_merge($a[$key], $b[$key]);
                } else {
                    $a[$key] = $b[$key];
                }
            }
        }

        return $a;
    }
}

if (!function_exists('current_request_route')) {
    function current_request_route()
    {
        $route_info = request()->routeInfo();
        if(empty($route_info) || empty($route_info['route'])){
            return '';
        }
        $route = str_replace(['\\', '@'], '/', $route_info['route']);
        $route = str_replace('update', 'edit', $route);
        $route = str_replace('create', 'save', $route);
        return  strtolower($route);
    }
}

if (!function_exists('left_menu_item_class')) {
    function left_menu_item_class(array $current_ancestor_ids, array $menu_item){
        $class_name = "";
        if(in_array($menu_item["id"], $current_ancestor_ids)){
            if(empty($menu_item["children"])){
                $class_name = "active";
            }else{
                $class_name = "active open";
            }
        }
        return $class_name;
    }
}

if (!function_exists('format_show_time')) {
    function format_show_time($int_time, $type = "")
    {
        if(empty($int_time)){
            return "";
        }else if(is_numeric($int_time)){
            switch ($type) {
            case 'zh_time':
                $format_str = "m月d日 H:i:s";
                break;
            case 'no_second':
                $format_str = "Y-m-d H:i";
                break;
            case 'date_node':
                $format_str = "Y.m.d";
                break;
            case 'small_date_node':
                $format_str = "y.m.d";
                break;
            case 'no_year_node':
                $format_str = "m.d H:i:s";
                break;
            case 'short_node':
                $format_str = "m.d";
                break;
            case 'only_date':
                $format_str = "Y-m-d";
                break;
            case 'month_day':
                $format_str = "m-d";
                break;
            case 'date_dir':
                $format_str = "Y/m/d";
                break;
            case 'ymd_dir':
                $format_str = "Ymd";
                break;
            case 'weekday':
                $format_str = "D";
                break;
            default:
                $format_str = "Y-m-d H:i:s";
                break;
        }
            return date($format_str, $int_time);
        }else{
            return $int_time;
        }
    }
}

\think\Console::addDefaultCommands([
    'tpadmin:init' => \tpadmin\command\Init::class,
    'tpadmin:seed' => \tpadmin\command\Seed::class,
]);

// Route::group('admin/auth', function () {
//     Route::get('passport/login', '\\tpadmin\\Config@login')->name('admin.auth.passport.login');
//     Route::post('passport/login', 'tpadmin\\Passport@loginAuth')->name('admin.auth.passport.dologin');

//     // Route::get('/admin/passport/logout', '\\tpadmin\\auth\\Passport@logout')->name('admin.auth.passport.logout')->middleware('tpadmin.admin');
//     // Route::get('/admin/passport/user', '\\tpadmin\\auth\\Passport@user')->name('admin.auth.passport.user')->middleware('tpadmin.admin');
// });