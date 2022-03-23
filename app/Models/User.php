<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Lang;
use DB;

class User extends BaseModel implements AuthenticatableContract, CanResetPasswordContract, JWTSubject
{
    use Authenticatable, CanResetPassword, Notifiable;

    // The overwrite variable and function
    // ------------------------------------------------

    protected $primaryKey = 'id';

    protected $table = 'users';

    protected $fillable = ['id', 'created_at' , 'updated_at', 
        'is_deleted', 'created_by', 'updated_by',
        'username', 'email', 'password', 'first_name',
        'last_name', 'first_name_kana', 'last_name_kana',
        'gender', 'birthday', 'nick_name',
        'avatar', 'role_id', 'description','udp_ver',
    ];

    protected $search_field = array (
        array('email', 2),
        array('username', 12, array('first_name', 'last_name')),
        array('role_id', 1)
    );

    protected static function check_store($input)
    {
        if($input['email']) {
            if(self::checkDeletedUser($input['email'])) {
                return [
                    'status' => false,
                    'message' => [
                        'email' => Lang::get('api.error_message.deleted_user_login')
                    ]
                ];
            }
        }

        return parent::check_store($input);
    }

    protected static function before_store($request, $input)
    {
        if(isset($input['avatar_image']))
        {
            $image_data = self::uploadFileS3("users", $input['avatar_image'],
                $request->file('avatar_image')->getClientOriginalName());
            if($image_data != null)
            {
                $input['avatar'] = $image_data['file_path'];
            }
            else
            {
                $input['avatar'] = null;
            }
        }

        unset($input['avatar_image']);
        return $input;
    }

    protected static function after_store($object, $input, $request)
    {
        // 通知情報保存
        if(!empty($input['notify'])) {
            foreach($input['notify'] as $key => $data)
            {
                Notify::default_store(
                    [
                        'user_id'           => $object['id'],
                        'notify_type'       => $key,      
                        'mail_notify'       => $data['mail_notify'],           
                        'linework_notify'   => $data['linework_notify'],
                        'created_by'        => $object['updated_by'],
                        'updated_by'        => $object['updated_by']
                    ],
                    'Notify',
                    ''
                );
            }
        }
    }
    

    public function fieldSetValidate($id = null)
    {
        $result['first_name'] =  "required|string";
        $result['last_name'] =  "required|string";
        $result['email'] = "required|string|email|max:100|unique:users,email,$id,id,is_deleted,0";
        $result['first_name_kana'] = 'nullable|katakana';
        $result['last_name_kana'] = 'nullable|katakana';
        $result['phone'] = 'nullable|digits:10';
        $result['birthday'] = 'nullable|date_format:'.$this::DATE_FORMAT;
        $result['avatar_image'] = 'nullable|image|mimes:jpeg,png,jpg,jpe,jfif,pjpeg,pjp,HEIF,HEIC,JPG,JPEG|max:20480';

        return $result;
    }

    /**
     *  Change request data to update object
     * 
     * @param $request, $input, $object
     * @return mixed
     */
    protected static function before_update($request, $input, $object)
    {
        if(isset($input['avatar_image']))
        {
            $image_data = self::uploadFileS3("users", $input['avatar_image'],
                $request->file('avatar_image')->getClientOriginalName());
            if($image_data != null)
            {
                $input['avatar'] = $image_data['file_path'];
            }
            else
            {
                $input['avatar'] = null;
            }
        }

        unset($input['avatar_image']);

        // increase udp_ver number
        $input['udp_ver'] = $object['udp_ver'] + 1;
        
        return $input;
    }

    protected static function after_update($object, $input, $request)
    {
        // 通知情報保存
        if(!empty($input['notify'])) {
            foreach($input['notify'] as $key => $data)
            {
                if(Notify::where('user_id',$object['id'])
                           ->where('notify_type', $key)
                           ->where('is_deleted', false)
                           ->count() == 0) {
                    Notify::default_store(
                        [
                            'user_id'           => $object['id'],
                            'notify_type'       => $key,      
                            'mail_notify'       => $data['mail_notify'],           
                            'linework_notify'   => $data['linework_notify'],
                            'created_by'        => $object['updated_by'],
                            'updated_by'        => $object['updated_by']   
                        ],
                        'Notify',
                        ''
                    );
                }
                else {
                    Notify::where('user_id',$object['id'])
                    ->where('notify_type', $key)
                    ->where('is_deleted', false)
                    ->update([
                        'mail_notify'       => $data['mail_notify'],           
                        'linework_notify'   => $data['linework_notify'],
                        'created_by'        => $object['updated_by'],
                        'updated_by'        => $object['updated_by']  
                    ]);
                }
                
            }
        }

        return;
    }

    // The custom function
    // ------------------------------------------------

    /**
	 * One to Many relation
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
    public function role()
    {
        return $this->belongsTo('App\Models\Role')->withDefault();
    }

    /**
     * getNameAttribute
     *
     * @return null|string
     */
    public function getNameAttribute()
    {
        return $this->attributes['first_name'] . ' ' . $this->attributes['last_name'];
    }

    /**
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    protected static function updatePasswd($object, $password)
    {
        $object['password'] = bcrypt($password);
        $object->save();
    }

    protected static function confirmPasswd($password, $user)
    {
        return Hash::check($password, $user['password']);
    }

    protected static function checkGoogleUserExits($mail)
    {
        $user = self::where('email', $mail)
                      ->where('is_deleted', false)->first();

        return empty($user) ? false : $user;
    }
    
    protected static function createUserByGoogleToken($g_user, $password)
    {
        $user = self::where('email', $g_user->getEmail())
                    ->where('is_deleted', false)->first();

        if(empty($user)) {
            $user = self::create([
                'email' => $g_user->getEmail(),
                'first_name' => $g_user->getName(),
                'username' => $g_user->getName(),
                'password' => bcrypt($password),
                'role_id' => Role::READER,
                'avatar' => $g_user->getAvatar(),
                'nick_name' =>  $g_user->getNickname(),
                'created_by' => 0,
                'updated_by' => 0
            ]);
        }

        return empty($user) ? false : $user;
    }

    protected static function checkDeletedUser($mail) {

        $user = self::where('email', $mail)
                      ->where('is_deleted', true)->first();

        return empty($user) ? false : true;
    }

    protected static function getNotifyListUser($setting_type, $notify_type) {

        $user = DB::table('users')
                ->join('notify_setting', 'users.id', '=', 'notify_setting.user_id')
                ->select('email')
                ->where('notify_setting.notify_type', $setting_type)
                ->where('notify_setting.is_deleted', false)
                ->where('users.is_deleted', false);

        switch($notify_type) {
            case "MAIL":
                return $user->where('notify_setting.mail_notify', 1)->get()->toArray();
            case "LINEWORKS":
                return $user->where('notify_setting.linework_notify', 1)->get()->toArray();
            default:
                break;
        }
        
        return [];
    }

    /**
     * hasmany relation Notify
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function notify()
    {
        return $this->hasMany('App\Models\Notify', 'user_id')
                ->where('notify_setting.is_deleted', false);
    }
}
