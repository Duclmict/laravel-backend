<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;

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
        Validator::extend('base64',function($attribute, $value, $params, $validator) {
            $image = base64_decode($value);
            $f = finfo_open();
            $result = finfo_buffer($f, $image, FILEINFO_MIME_TYPE);
            return ($result == 'image/png' || $result == 'image/jpg' || $result == 'image/jpeg');
        }, 'Base64 is invalid');

        Validator::extend('katakana', function($attribute, $value, $parameters)
        {
            if (preg_match("/^[ァ-ヶｦ-ﾟー]+$/u", $value)) {
                return true;
            }
        });

        Validator::extend('decimal_digits', function($attribute, $value, $parameters)
        {
            if (strpos($value, '.') !== false) {
                if (preg_match("/^[0-9]{1,13}[.][0-9]{0,".$parameters[0]."}+$/u", $value)) {
                    return true;
                }
            }
            else{
                if (preg_match("/^[0-9]{1,13}+$/u", $value)) {
                    return true;
                }
            }
        });

        Validator::replacer('decimal_digits', function($message, $attribute, $rule, $parameters) {
            $ageLimit = $parameters[0];
        
            return str_replace(':decimal_digits', $ageLimit, $message);
        });

        Validator::extend('chiban', function($attribute, $value, $parameters)
        {
            if (preg_match('/^[0-9-]*$/', $value)) {
                return true;
            }
        });

        Validator::extend('date_multi_format', function($attribute, $value, $formats) {
            // iterate through all formats
            foreach($formats as $format) {
      
              // parse date with current format
              $parsed = date_parse_from_format($format, $value);
      
              // if value matches given format return true=validation succeeded 
              if ($parsed['error_count'] === 0 && $parsed['warning_count'] === 0) {
                return true;
              }
            }
      
            // value did not match any of the provided formats, so return false=validation failed
            return false;
        });

        Validator::replacer('date_multi_format', function($message, $attribute, $rule, $parameters) {
            return str_replace(':date_multi_format',' '.collect($parameters)->implode(' , '). ' ', $message);
        });
    }
}
