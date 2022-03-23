<?php
namespace App\Helpers;
use Tymon\JWTAuth\Facades\JWTAuth;
use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use DateTime;
class Helper
{
    public static function getDateTimeWithCarbon($timezone = 'Asia/Tokyo')
    {
        return (new Carbon())->setTimezone($timezone);
    }

    public static function carbonParseTime($time)
    {
        return self::getDateTimeWithCarbon()::parse($time);
    }

    public static function getDateTimeNow($timezone = 'Asia/Tokyo')
    {
        return Carbon::now()->setTimezone($timezone);
    }

    public static function getUserByJWTToken()
    {
        return JWTAuth::toUser(JWTAuth::getToken());
    }

    public static function getExportFileName($name)
    {
        return sprintf($name.'_%s.csv', Carbon::now()->format('YmdHis'));
    }

    public static function getExportExcelFileName($name)
    {
        return sprintf($name.'_%s.xlsx', Carbon::now()->format('YmdHis'));
    }

    public static function getLastEditUserName($user_id)
    {
        $user = User::find($user_id);
        if(!empty($user))
            return $user['first_name'] . ' ' . $user['last_name'];
        
        return "管理者";
    }

    public static function sendEmail($mail, $data)
    {
        info($data);
        Mail::send($mail['template'], compact('data'), function($message) use($mail, $data) {
            $message->from($mail['from'], $mail['from_name'])
                    ->to($data['email'])
                    ->subject($mail['subject']);
        });

        if( count(Mail::failures()) > 0 ) {
            return false;
        }

        return true;
    }

    public static function checkDateFormat($date, $format)
    {
        switch($format)
        {
            case "YYYY-MM-DD":
                if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$date))
                    return true;
                break;
            case "DD-MM-YYYY":
                if (preg_match("/^(0[1-9]|[1-2][0-9]|3[0-1])-(0[1-9]|1[0-2])-[0-9]{4}$/",$date))
                    return true;
                break;
            default:
                break;
        }
        return false;
    }

    public static function convertTimeSecondsUTC()
    {
        return Carbon::now()->timestamp;
    }

    public static function getTextMailEnvironment($environment)
    {
        switch($environment)
        {
            case "DEVELOPMENT":
                return "【開発環境テスト用】";
            case "STAGGING":
                return "【ステージング環境テスト用】";
            default:
                return "";
        }
    }

    public static function removeNotPrintableString($string)
    {
        return preg_replace('/[\x00-\x1F\x7F]/u', '', $string);
    }

    public static function validateDate($date, $format = 'Y/m/d')
    {
        $d = DateTime::createFromFormat($format, $date);
        // The Y ( 4 digits year ) returns TRUE for any integer with any number of digits so changing the comparison from == to === fixes the issue.
        return $d && $d->format($format) === $date;
    }

    public static function isWeekendDay($date, $format = 'Y/m/d') {
        $d = self::carbonParseTime($date.' 00:00:00');
        return (($d->dayOfWeek == Carbon::SUNDAY) || ($d->dayOfWeek == Carbon::SATURDAY)) ? true : false;
    }

    public static function convertDateFormat($date, $format ='Y/m/d') {
        return self::carbonParseTime($date.' 00:00:00')->format($format);
    }
}