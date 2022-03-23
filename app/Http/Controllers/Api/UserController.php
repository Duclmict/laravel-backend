<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use JWTAuth;
use JWTAuthException;
use App\Services\CustomLogService;
use App\Helpers\Helper;
use App\Models\Token;
use Validator;
use DB;
use Lang;
use Socialite;
use Illuminate\Support\Str;

/**
 * Class UserController
 *
 * @package App\Http\Controllers\API
 * @SWG\Tag(name="users", description="user all action")
 */
class UserController extends BaseController
{
    /** Overwrite this model
     *
     * @var string
     */
    protected $modal = 'App\Models\User';

    /** Overwrite this resource
     *
     * @var string
     */
    protected $resource = 'App\Http\Resources\User\UserResource';

    /** This collection
     *
     * @var string
     */
    protected $collection = 'App\Http\Resources\User\UserCollection';

    /** Overwrite this resource
     *
     * @var string
     */
    protected $resource_index = 'App\Http\Resources\User\UserResource';

    /** This collection
     *
     * @var string
     */
    protected $collection_index = 'App\Http\Resources\User\UserCollection';

        /** Overwrite this resource
     *
     * @var string
     */
    protected $resource_profile = 'App\Http\Resources\User\UserSimpleResource';

     /**
     * @SWG\Get(
     *   path="/api/users",
     *   tags={"users"},
     *   summary="index",
     *   operationId="listuser",
     *   @SWG\Parameter(
     *     name="Authorization",
     *     in="header",
     *     description="Token Authorization (Bearer -token-)",
     *     required=true,
     *     type="string",
     *     default="Bearer {token}"
     *   ),
     *     @SWG\Parameter(
     *     name="page",
     *     in="query",
     *     description="page of data",
     *     required=false,
     *     type="integer"
     *   ),
     *     @SWG\Parameter(
     *     name="limit",
     *     in="query",
     *     description="limit data foreach page",
     *     required=false,
     *     enum={10,20,30,50,100},
     *     type="integer"
     *   ),
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=500, description="internal server error")
     * )
     */

     /**
     * @SWG\Get(
     *   path="/api/users/search",
     *   tags={"users"},
     *   summary="検索",
     *   operationId="ユーザー検索",
     *   @SWG\Parameter(
     *     name="Authorization",
     *     in="header",
     *     description="Token Authorization (Bearer -token-)",
     *     required=true,
     *     type="string",
     *     default="Bearer {token}"
     *   ),
     *     @SWG\Parameter(
     *     name="email",
     *     in="query",
     *     description=" メールアドレス",
     *     required=false,
     *     type="string"
     *   ),
     *     @SWG\Parameter(
     *     name="username",
     *     in="query",
     *     description="",
     *     required=false,
     *     type="string"
     *   ),
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=500, description="internal server error")
     * )
     */

    /**
     * @SWG\Post(
     *   path="/api/users",
     *   tags={"users"},
     *   summary="Create User",
     *   operationId="createUser",
     *   @SWG\Parameter(
     *     name="Authorization",
     *     in="header",
     *     description="Token Authorization (Bearer -token-)",
     *     required=true,
     *     type="string",
     *     default="Bearer {token}"
     *   ),
     *     @SWG\Parameter(
     *     name="email",
     *     in="formData",
     *     description="email of User",
     *     required=false,
     *     type="string"
     *   ),
     *     @SWG\Parameter(
     *     name="username",
     *     in="formData",
     *     description="username of User",
     *     required=true,
     *     type="string"
     *   ),
     *     @SWG\Parameter(
     *     name="first_name",
     *     in="formData",
     *     description="first name of User",
     *     required=false,
     *     type="string"
     *   ),
     *     @SWG\Parameter(
     *     name="last_name",
     *     in="formData",
     *     description="last name of User",
     *     required=false,
     *     type="string"
     *   ),
     *     @SWG\Parameter(
     *     name="first_name_kana",
     *     in="formData",
     *     description="first name of User (katakana)",
     *     required=false,
     *     type="string"
     *   ),
     *     @SWG\Parameter(
     *     name="last_name_kana",
     *     in="formData",
     *     description="last name of User (katakana)",
     *     required=false,
     *     type="string"
     *   ),
     *     @SWG\Parameter(
     *     name="gender",
     *     in="formData",
     *     description="gender (0:Male or 1:Female)",
     *     required=false,
     *     enum={0,1},
     *     type="integer"
     *   ),
     *     @SWG\Parameter(
     *     name="birthday",
     *     in="formData",
     *     description="Birth day",
     *     required=false,
     *     type="string"
     *   ),
     *     @SWG\Parameter(
     *     name="avatar_image",
     *     in="formData",
     *     description="avatar",
     *     required=false,
     *     type="string"
     *   ),
     *     @SWG\Parameter(
     *     name="password",
     *     in="formData",
     *     description=" password of user",
     *     required=true,
     *     type="string"
     *   ),
     *     @SWG\Parameter(
     *     name="password_confirmation",
     *     in="formData",
     *     description=" password confirmation of user",
     *     required=true,
     *     type="string"
     *   ),
     *     @SWG\Parameter(
     *     name="notify[0][mail_notify]",
     *     in="formData",
     *     description="未払い案件通知 メール(1: 有効、0：無効)",
     *     required=false,
     *     enum={0,1},
     *     type="integer"
     *   ),
     *     @SWG\Parameter(
     *     name="notify[0][linework_notify]",
     *     in="formData",
     *     description="未払い案件通知　lineworks(1: 有効、0：無効)",
     *     required=false,
     *     enum={0,1},
     *     type="integer"
     *   ),
     *     @SWG\Parameter(
     *     name="notify[1][mail_notify]",
     *     in="formData",
     *     description="契約終了通知　メール(1: 有効、0：無効)",
     *     required=false,
     *     enum={0,1},
     *     type="integer"
     *   ),
     *     @SWG\Parameter(
     *     name="notify[1][linework_notify]",
     *     in="formData",
     *     description="契約終了通知　lineworks (1: 有効、0：無効)",
     *     required=false,
     *     enum={0,1},
     *     type="integer"
     *   ),
     *     @SWG\Parameter(
     *     name="role_id",
     *     in="formData",
     *     description="権限(1: 管理者、２：編集者、３：閲覧者)",
     *     required=true,
     *     enum={1,2,3},
     *     type="integer"
     *   ),
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=400, description="bad request"),
     *   @SWG\Response(response=500, description="internal server error")
     * )
     *
     */

    /**
     * @SWG\Put(
     *   path="/api/users/{id}",
     *   tags={"users"},
     *   summary="Update user's information",
     *   operationId="update user",
     *   @SWG\Parameter(
     *     name="Authorization",
     *     in="header",
     *     description="Token Authorization (Bearer -token-)",
     *     required=true,
     *     type="string",
     *     default="Bearer {token}"
     *   ),
     *     @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     description="User id action",
     *     required=false,
     *     type="integer"
     *   ),
     *     @SWG\Parameter(
     *     name="email",
     *     in="formData",
     *     description="email of User",
     *     required=false,
     *     type="string"
     *   ),
     *     @SWG\Parameter(
     *     name="first_name",
     *     in="formData",
     *     description="first name of User",
     *     required=false,
     *     type="string"
     *   ),
     *     @SWG\Parameter(
     *     name="last_name",
     *     in="formData",
     *     description="last name of User",
     *     required=false,
     *     type="string"
     *   ),
     *     @SWG\Parameter(
     *     name="first_name_kana",
     *     in="formData",
     *     description="first name of User (katakana)",
     *     required=false,
     *     type="string"
     *   ),
     *     @SWG\Parameter(
     *     name="last_name_kana",
     *     in="formData",
     *     description="last name of User (katakana)",
     *     required=false,
     *     type="string"
     *   ),
     *     @SWG\Parameter(
     *     name="gender",
     *     in="formData",
     *     description="gender (0:Male or 1:Female)",
     *     required=false,
     *     enum={0,1},
     *     type="integer"
     *   ),
     *     @SWG\Parameter(
     *     name="birthday",
     *     in="formData",
     *     description="Birth day",
     *     required=false,
     *     type="string"
     *   ),
     *     @SWG\Parameter(
     *     name="avatar_image",
     *     in="formData",
     *     description="avatar",
     *     required=false,
     *     type="string"
     *   ),
     *     @SWG\Parameter(
     *     name="notify[0][mail_notify]",
     *     in="formData",
     *     description="未払い案件通知 メール(1: 有効、0：無効)",
     *     required=false,
     *     enum={0,1},
     *     type="integer"
     *   ),
     *     @SWG\Parameter(
     *     name="notify[0][linework_notify]",
     *     in="formData",
     *     description="未払い案件通知　lineworks(1: 有効、0：無効)",
     *     required=false,
     *     enum={0,1},
     *     type="integer"
     *   ),
     *     @SWG\Parameter(
     *     name="notify[1][mail_notify]",
     *     in="formData",
     *     description="契約終了通知　メール(1: 有効、0：無効)",
     *     required=false,
     *     enum={0,1},
     *     type="integer"
     *   ),
     *     @SWG\Parameter(
     *     name="notify[1][linework_notify]",
     *     in="formData",
     *     description="契約終了通知　lineworks (1: 有効、0：無効)",
     *     required=false,
     *     enum={0,1},
     *     type="integer"
     *   ),
     *     @SWG\Parameter(
     *     name="role_id",
     *     in="formData",
     *     description="権限(1: 管理者、２：編集者、３：閲覧者)",
     *     required=true,
     *     enum={1,2,3},
     *     type="integer"
     *   ),
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=400, description="bad request"), 
     *   @SWG\Response(response=404, description="user not found"),
     *   @SWG\Response(response=500, description="internal server error")
     * )
     *
     */

    /**
     * @SWG\Delete(
     *   path="/api/users/{id}",
     *   summary="Delete users",
     *   tags={"users"},
     *   operationId="Manger delete user",
     *   @SWG\Parameter(
     *     name="Authorization",
     *     in="header",
     *     description="Token Authorization (Bearer -token-)",
     *     required=true,
     *     type="string",
     *     default="Bearer {token}"
     *   ),
     *     @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     description="User id action",
     *     required=false,
     *     type="integer"
     *   ),
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=500, description="internal server error")
     * )
     *
     */

    /**
     * @SWG\Get(
     *   path="/api/users/{id}",
     *   summary="Show users",
     *   tags={"users"},
     *   operationId="Manger delete user",
     *   @SWG\Parameter(
     *     name="Authorization",
     *     in="header",
     *     description="Token Authorization (Bearer -token-)",
     *     required=true,
     *     type="string",
     *     default="Bearer {token}"
     *   ),
     *     @SWG\Parameter(
     *     name="id",
     *     in="path",
     *     description="User id action",
     *     required=false,
     *     type="integer"
     *   ),
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=404, description="user not found"),
     *   @SWG\Response(response=500, description="internal server error")
     * )
     *
     */

    /**
     * @SWG\Post(
     *   path="/api/login",
     *   tags={"authen"},
     *   summary="User login",
     *   operationId="login",
     *    @SWG\Parameter(
     *     name="email",
     *     in="formData",
     *     description="Email of user",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Parameter(
     *     name="password",
     *     in="formData",
     *     description="Password of user",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="UnAuthorized"),
     *   @SWG\Response(response=500, description="internal server error")
     * )
     *
     */
    public function login(Request $request)
    {
        CustomLogService::info(__FILE__, __LINE__,__CLASS__, "login start with email: ".$request->email);

        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:100',
            'password' => 'required|string|max:100'
        ]);
        
        if ($validator->fails()) {
            CustomLogService::error(__FILE__, __LINE__,__CLASS__, "validate data error");
            return $this->sendErrorBadRequest($validator->errors());
        }

        if ($this->modal::checkDeletedUser($request['email'])) {
            return $this->sendErrorUnAuthorized($this->deleted_user_msg);
        }

        $user = $this->modal::where('email', $request['email'])
                    ->where('is_deleted', false)->first();

        if (empty($user)) {
            return $this->sendErrorUnAuthorized($this->login_error_msg);
        }

        $data = array();
        $token = null;
        $credentials = $request->only('email', 'password');
        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                CustomLogService::info(__FILE__, __LINE__,__CLASS__, "failed login with email :".$request->email);
                return $this->sendErrorUnAuthorized($this->login_error_msg);
            }
        } catch (JWTAuthException $e) {
            CustomLogService::error(__FILE__, __LINE__,__CLASS__, $e);
            return $this->sendErrorUnAuthorized($this->create_token_error_msg);
        }

        $data['token'] = $token;

        CustomLogService::info(__FILE__, __LINE__,__CLASS__, "login success with email: ".$request->email);

        return $this->sendResponse($data, 201);
    }

    /**
     * @SWG\Post(
     *   path="/api/user/changePassword",
     *   tags={"authen"},
     *   summary="change password action",
     *   operationId="changepw",
     *   @SWG\Parameter(
     *     name="Authorization",
     *     in="header",
     *     description="Token Authorization (Bearer -token-)",
     *     required=true,
     *     type="string",
     *     default="Bearer {token}"
     *   ),
     *     @SWG\Parameter(
     *     name="old_password",
     *     in="formData",
     *     description="現在のパスワード",
     *     required=true,
     *     type="string"
     *   ),
     *     @SWG\Parameter(
     *     name="npassword",
     *     in="formData",
     *     description="新規パスワード",
     *     required=true,
     *     type="string"
     *   ),
     *     @SWG\Parameter(
     *     name="npassword_confirmation",
     *     in="formData",
     *     description="新規パスワード（確認用）",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=404, description="user not found"),
     *   @SWG\Response(response=500, description="internal server error")
     * )
     *
     */
    public function changePassword(Request $request)
    {
        CustomLogService::info(__FILE__, __LINE__,__CLASS__, "change password start");

        $validator = Validator::make($request->all(), [
            'npassword' => 'required|string|confirmed|min:8',
            'npassword_confirmation' => 'required|string',
            'old_password' => 'required|string|different:npassword',
        ]);
        
        if ($validator->fails()) {
            CustomLogService::info(__FILE__, __LINE__,__CLASS__, "validate data error");
            return $this->sendErrorBadRequest($validator->errors());
        }

        $user = Helper::getUserByJWTToken();
        
        // check passwor
        if(!$this->modal::confirmPasswd($request['old_password'], $user))
        {
            CustomLogService::info(__FILE__, __LINE__,__CLASS__, "old password not match");
            $msg = [
                'old_password' => Lang::get('api.error_message.old_pass_not_match'),
            ];
            return $this->sendErrorBadRequest($msg);
        }

        DB::beginTransaction();
        try {
            $this->modal::updatePasswd($user, $request['npassword']);
            DB::commit();
        } 
        catch (\Exception $ex) {
            DB::rollback();
            CustomLogService::error(__FILE__, __LINE__,__CLASS__, $ex->getMessage());
            return $this->sendErrorServerInternal($ex->getMessage());
        }

        CustomLogService::info(__FILE__, __LINE__,__CLASS__, "change password success");

        return $this->sendResponse('', 200);
    }

    /**
     * @SWG\Post(
     *   path="/api/user/resetPassword",
     *   tags={"authen"},
     *   summary="reset password action",
     *   operationId="resetpw",
     *     @SWG\Parameter(
     *     name="email",
     *     in="formData",
     *     description="email of user",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=404, description="user not found"),
     *   @SWG\Response(response=500, description="internal server error")
     * )
     *
     */
    public function resetPassword(Request $request)
    {
        CustomLogService::info(__FILE__, __LINE__,__CLASS__, "reset password start");
    
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
        ]);
        
        if ($validator->fails()) {
            CustomLogService::info(__FILE__, __LINE__,__CLASS__, "validate data error");
            return $this->sendErrorBadRequest($validator->errors());
        }

        $user = $this->modal::where('email', $request['email'])
                    ->where('is_deleted', false)->first();
        
        if (empty($user)) {
            CustomLogService::info(__FILE__, __LINE__,__CLASS__, "email not exits");
            $msg = [
                'email' => Lang::get('api.error_message.mail_not_exits')
            ];
            return $this->sendErrorBadRequest($msg);
        }

        $token = null;

        DB::beginTransaction();
        try {
            Token::disableToken($user['id'], Token::TYPE_RESET_PASSWORD);

            // create token for verify user
            $token = Token::createToken($user['id'], Token::TYPE_RESET_PASSWORD);

            if (empty($token)) {
                CustomLogService::info(__FILE__, __LINE__,__CLASS__, "get user by id error");
                return $this->sendErrorServerInternal(Lang::get('api.error_message.create_pw_token_error'));
            }
    
            $send_data = [
                'email' => $request['email'],
                'link' => config('urls.web.reset_pwd').'?token='.$token['token']
            ];
    
            // send mail
            if(!Helper::sendEmail(config('mail.resetpw'), $send_data))
            {
                return $this->sendErrorServerInternal($this->mail_error_msg);
            }

            DB::commit();
        } 
        catch (\Exception $ex) {
            DB::rollback();
            CustomLogService::error(__FILE__, __LINE__,__CLASS__, $ex->getMessage());
            return $this->sendErrorServerInternal($ex->getMessage());
        }

        CustomLogService::info(__FILE__, __LINE__,__CLASS__, "reset password success");

        return $this->sendResponse('', 200);
    }

    /**
     * @SWG\Post(
     *   path="/api/user/confirmReset",
     *   tags={"authen"},
     *   summary="reset password action",
     *   operationId="confirmresetpw",
     *     @SWG\Parameter(
     *     name="token",
     *     in="formData",
     *     description="token reset password of user",
     *     required=true,
     *     type="string"
     *   ),
     *     @SWG\Parameter(
     *     name="password",
     *     in="formData",
     *     description="New User password",
     *     required=true,
     *     type="string"
     *   ),
     *     @SWG\Parameter(
     *     name="password_confirmation",
     *     in="formData",
     *     description="New User password confirmation",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=404, description="user not found"),
     *   @SWG\Response(response=500, description="internal server error")
     * )
     *
     */
    public function confirmReset(Request $request)
    {
        CustomLogService::info(__FILE__, __LINE__,__CLASS__, "confirm reset password start");

        $validator = Validator::make($request->all(), [
            'token'   => 'required|string',
            'password' => 'required|string|confirmed|min:8',
            'password_confirmation' => 'required|string'
        ]);
        
        if ($validator->fails()) {
            CustomLogService::info(__FILE__, __LINE__,__CLASS__, "validate data error");
            return $this->sendErrorBadRequest($validator->errors());
        }

        $verify = Token::verifyToken($request['token'], Token::TYPE_RESET_PASSWORD);

        if (empty($verify)) {
            CustomLogService::info(__FILE__, __LINE__,__CLASS__, "check token error");
            return $this->sendErrorNotFound(Lang::get('api.error_message.disable_link'));
        }

        CustomLogService::debug(__FILE__, __LINE__,__CLASS__, "user_id after verify:".$verify['user_id']);

        $user = $this->modal::getById($verify['user_id']);

        if (empty($user)) {
            CustomLogService::info(__FILE__, __LINE__,__CLASS__, "user has been deleted");
            return $this->sendErrorNotFound(Lang::get('api.error_message.disable_link'));
        }

        DB::beginTransaction();
        try {
            Token::disableToken($user['id'], Token::TYPE_RESET_PASSWORD);

            $this->modal::updatePasswd($user, $request['password']);

            $send_data = [
                'email' => $user['email']
            ];
    
            // send mail
            if(!Helper::sendEmail(config('mail.confirm_resetpw'), $send_data))
            {
                return $this->sendErrorServerInternal($this->mail_error_msg);
            }

            DB::commit();
        } 
        catch (\Exception $ex) {
            DB::rollback();
            CustomLogService::error(__FILE__, __LINE__,__CLASS__, $ex->getMessage());
            return $this->sendErrorServerInternal($ex->getMessage());
        }

        CustomLogService::info(__FILE__, __LINE__,__CLASS__, "confirm reset password success");

        return $this->sendResponse('', 200);
    }

    /**
     * @SWG\Get(
     *   path="/api/user/logout",
     *   tags={"users"},
     *   summary="User logout",
     *   operationId="logoutuser",
     *   @SWG\Parameter(
     *     name="Authorization",
     *     in="header",
     *     description="Token Authorization (Bearer -token-)",
     *     required=true,
     *     type="string",
     *     default="Bearer {token}"
     *   ),
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="UnAuthorized"),
     *   @SWG\Response(response=500, description="internal server error")
     * )
     *
     */
    public function logout()
    {
        CustomLogService::info(__FILE__, __LINE__,__CLASS__, "log out user start");
        try {
            JWTAuth::invalidate(JWTAuth::getToken());
            CustomLogService::info(__FILE__, __LINE__,__CLASS__, "log out user success");
            return $this->sendResponse($this->logout_success_msg, 200);
        } catch (JWTException $e) {
            CustomLogService::error(__FILE__, __LINE__,__CLASS__, "log out user error: ".$e);
            return $this->sendErrorUnAuthorized($this->logout_fail_msg);
        }
    }

    /**
     * @SWG\Get(
     *   path="/api/user/profile/",
     *   tags={"users"},
     *   summary="get profile user",
     *   operationId="get profile user",
     *   @SWG\Parameter(
     *     name="Authorization",
     *     in="header",
     *     description="Token Authorization (Bearer -token-)",
     *     required=true,
     *     type="string",
     *     default="Bearer {token}"
     *   ),
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="UnAuthorized"),
     *   @SWG\Response(response=500, description="internal server error")
     * )
     *
     */
    public function getProfile()
    {
        $object = Helper::getUserByJWTToken();
        CustomLogService::info(__FILE__, __LINE__,__CLASS__, 
        "start show profile by id: ".$object['id']." ,modal: ".$this->modal);

        if (empty($object)) {
            CustomLogService::info(__FILE__, __LINE__,__CLASS__, "get data by id error");
            return $this->sendErrorNotFound($this->no_object_error_msg);
        }

        CustomLogService::info(__FILE__, __LINE__,__CLASS__, "show profile by id success");

        return $this->sendResponse(new $this->resource_profile($object), 200);
    }

    /**
     * @SWG\Post(
     *   path="/api/user/profile",
     *   tags={"users"},
     *   summary="update profile users",
     *   operationId="update profile users",
     *   @SWG\Parameter(
     *     name="Authorization",
     *     in="header",
     *     description="Token Authorization (Bearer -token-)",
     *     required=true,
     *     type="string",
     *     default="Bearer {token}"
     *   ),
     *     @SWG\Parameter(
     *     name="first_name",
     *     in="formData",
     *     description="first name of User",
     *     required=false,
     *     type="string"
     *   ),
     *     @SWG\Parameter(
     *     name="email",
     *     in="formData",
     *     description="email of User",
     *     required=false,
     *     type="string"
     *   ),
     *     @SWG\Parameter(
     *     name="last_name",
     *     in="formData",
     *     description="last name of User",
     *     required=false,
     *     type="string"
     *   ),
     *     @SWG\Parameter(
     *     name="first_name_kana",
     *     in="formData",
     *     description="first name of User (katakana)",
     *     required=false,
     *     type="string"
     *   ),
     *     @SWG\Parameter(
     *     name="last_name_kana",
     *     in="formData",
     *     description="last name of User (katakana)",
     *     required=false,
     *     type="string"
     *   ),
     *     @SWG\Parameter(
     *     name="gender",
     *     in="formData",
     *     description="gender (0:Male or 1:Female)",
     *     required=false,
     *     enum={0,1},
     *     type="integer"
     *   ),
     *     @SWG\Parameter(
     *     name="birthday",
     *     in="formData",
     *     description="Birth day",
     *     required=false,
     *     type="string"
     *   ),
     *     @SWG\Parameter(
     *     name="nick_name",
     *     in="formData",
     *     description="Nick name",
     *     required=false,
     *     type="string"
     *   ),
     *     @SWG\Parameter(
     *     name="avatar_image",
     *     in="formData",
     *     description="avatar",
     *     required=false,
     *     type="file"
     *   ),
     *     @SWG\Parameter(
     *     name="description",
     *     in="formData",
     *     description="User description",
     *     required=false,
     *     type="string"
     *   ),
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=404, description="user not found"),
     *   @SWG\Response(response=500, description="internal server error")
     * )
     *
     */
    public function updateProfile(Request $request)
    {
        CustomLogService::info(__FILE__, __LINE__,__CLASS__, "start update profile data");

        // prevent update field
        $input = $request->only('email', 'first_name', 'last_name','avatar','avatar_image');

        $object = Helper::getUserByJWTToken();
        $input['udp_ver'] = $object['udp_ver'];

        if (empty($object)) {
            CustomLogService::info(__FILE__, __LINE__,__CLASS__, "get data by id error");
            return $this->sendErrorNotFound($this->no_object_error_msg);
        }

        $object['updated_by'] = $object['id'];

        $rule = (new $this->modal)->fieldSetValidate($object['id']);

        $validator = Validator::make($input, (new $this->modal)->customValidate($request, $rule));
        
        if ($validator->fails()) {
            CustomLogService::info(__FILE__, __LINE__,__CLASS__, "validate data error");
            return $this->sendErrorBadRequest($validator->errors());
        }

        // check relation data before update
        $checked_data = $this->modal::check_update($input, $object);
        if(!$checked_data['status'])
        {
            return $this->sendErrorBadRequest($checked_data['message'], $checked_data['code'] ?? null);
        }

        DB::beginTransaction();
        try {
            // change $input request after update data
            $changed_input = $this->modal::before_update($request, $input, $object);

            // update data base
            $updated_object = $this->modal::default_update($object, $changed_input, 
                (new $this->modal)->get_table_name(), (new $this->modal)->get_code_field());

            // call after updated database
            $this->modal::after_update($updated_object, $input, $request);
            DB::commit();
        } 
        catch (\Exception $ex) {
            DB::rollback();
            CustomLogService::error(__FILE__, __LINE__,__CLASS__, $ex->getMessage());
            return $this->sendErrorServerInternal($ex->getMessage());
        }

        CustomLogService::info(__FILE__, __LINE__,__CLASS__, "update data success");

        return $this->sendResponse(new $this->resource_profile($object), 201);
    }

    public function store(Request $request)
    {
        CustomLogService::info(__FILE__, __LINE__,__CLASS__, "start create new data");

        $input = $request->all();

        // prevent update field
        $input = $this->modal::prevent_update($input);

        $rule = (new $this->modal)->fieldSetValidate();

        $validator = Validator::make($input, (new $this->modal)->customValidate($request, $rule));
        
        if ($validator->fails()) {
            CustomLogService::error(__FILE__, __LINE__,__CLASS__, "validate data error");
            return $this->sendErrorBadRequest($validator->errors());
        }

        $user = Helper::getUserByJWTToken();
        if(empty($user)) {
            $input['created_by'] = 0; // 0 is system ID
            $input['updated_by'] = 0; // 0 is system ID
        }
        else {
            $input['created_by'] = $user['id'];
            $input['updated_by'] = $user['id'];
        }

        // check relation data before store
        $checked_data = $this->modal::check_store($input);
        if(!$checked_data['status'])
        {
            return $this->sendErrorBadRequest($checked_data['message']);
        }

        DB::beginTransaction();
        try {

            $password = Str::random(10);

            // change $input request after create data
            $changed_input = $this->modal::before_store($request, $input);

            $changed_input['password'] = bcrypt($password);

            // create data to database
            $field = $this->modal::default_store($changed_input, 
                (new $this->modal)->get_table_name(), (new $this->modal)->get_code_field());
        
            // call after created data
            $this->modal::after_store($field , $input, $request);

            // send mail
            $send_data = [
                'email' => $field['email'],
                'password' => $password,
                'login_url' => config('urls.web.login')
            ];

            if(!Helper::sendEmail(config('mail.create_user'), $send_data))
            {
                return $this->sendErrorServerInternal($this->mail_error_msg);
            }

            DB::commit();
        } 
        catch (\Exception $ex) {
            DB::rollback();
            CustomLogService::error(__FILE__, __LINE__,__CLASS__, $ex->getMessage());
            return $this->sendErrorServerInternal($ex->getMessage());
        }
        
        CustomLogService::info(__FILE__, __LINE__,__CLASS__, "create new data success");

        return $this->sendResponse(new $this->resource($field), 201);
    }

    /**
     * @SWG\Post(
     *   path="/api/user/g-login",
     *   tags={"authen"},
     *   summary="User login via google service",
     *   operationId="login via google service",
     *    @SWG\Parameter(
     *     name="access_token",
     *     in="formData",
     *     description="Google Authentication code",
     *     required=true,
     *     type="string"
     *   ),
     *   @SWG\Response(response=200, description="successful operation"),
     *   @SWG\Response(response=401, description="UnAuthorized"),
     *   @SWG\Response(response=500, description="internal server error")
     * )
     *
     */
    public function loginViaGoole(Request $request)
    {
        $g_user = Socialite::driver('google')->userFromToken($request->access_token);

        if ($this->modal::checkDeletedUser($g_user->getEmail())) {
            return $this->sendErrorUnAuthorized($this->deleted_user_msg);
        }

        $user = $this->modal::checkGoogleUserExits($g_user->getEmail());

        if(empty($user)) {

            DB::beginTransaction();
            try {
                
                    $password = Str::random(10);
                    $user = $this->modal::createUserByGoogleToken($g_user, $password);

                    // send mail
                    $send_data = [
                        'email' => $g_user->getEmail(),
                        'password' => $password,
                        'login_url' => config('urls.web.login')
                    ];
            
                    // send mail
                    if(!Helper::sendEmail(config('mail.create_user'), $send_data))
                    {
                        return $this->sendErrorServerInternal($this->mail_error_msg);
                    }
                DB::commit();
            } 
            catch (\Exception $ex) {
                DB::rollback();
                CustomLogService::error(__FILE__, __LINE__,__CLASS__, $ex->getMessage());
                return $this->sendErrorServerInternal($ex->getMessage());
            }

        }

        if(empty($user))
            return $this->sendErrorUnAuthorized($this->login_error_msg);

        $data['token'] = JWTAuth::fromUser($user);

        CustomLogService::info(__FILE__, __LINE__,__CLASS__, " [Google] login success with email: ".$request->email);

        return $this->sendResponse($data, 201);
    }
}
