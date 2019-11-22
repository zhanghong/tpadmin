<?php
use think\facade\Request;
use think\facade\Console;

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
        $action = request()->action(true);
        $action = str_replace('update', 'edit', $action);
        $action = str_replace('create', 'save', $action);
        return  strtolower($action);
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

if (!function_exists('adminer_check')) {
    function adminer_check($name, $adminer)
    {
        if(empty($adminer)){
            return false;
        }else if($adminer->is_default){
            return true;
        }
        return auth_check($name, $adminer->id);
    }
}