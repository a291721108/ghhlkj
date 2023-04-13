<?php

namespace App\Models;


use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Model implements AuthenticatableContract, AuthorizableContract, JWTSubject
{
    use Authenticatable, Authorizable;

    protected $table = 'gh_user';

    /**
     * status
     */
    const  USER_STATUS_ONE = 1;  // 正常
    const  USER_STATUS_TWO = -1;  // 禁用

    const   USER_STATUS_MSG_ARRAY = [
        self::USER_STATUS_ONE      => "正常",
        self::USER_STATUS_TWO      => "禁用",
    ];

    /**
     * gender
     */
    const  GENDER_STATUS_ONE = 1;  // 男
    const  GENDER_STATUS_TWO = 2;  // 女
    const   GENDER_MSG_ARRAY = [
        self::GENDER_STATUS_ONE      => "男",
        self::GENDER_STATUS_TWO      => "女",
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['phone'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = ['password',];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     *  update password to  user_password.
     *
     */
    public function getAuthPassword()
    {
        return $this->attributes['password'];
    }

    /**
     * 获取当前登入用户信息
     */
    public static function getUserInfo(): ?AuthenticatableContract
    {
        return auth()->user();
    }

    /**
     * 退出登录
     */
    public static function logout()
    {
        return auth()->invalidate(true);
    }

    /**
     * 格式化时间
     * @param $value
     * @return false|int|string|null
     */
    public function fromDateTime($value)
    {
        return strtotime(parent::fromDateTime($value));
    }

    /**
     * 通过id获取名字
     */
    public static function getIdByname($id)
    {
        return self::where('id',$id)->select('id','name')->get()->toArray();
    }

    /**
     * get user info by id
     */
    public static function getUserInfoById($userId)
    {
        if (empty($userId)) {
            return '';
        }

        if (is_array($userId)) {
            return self::whereIn('id', $userId)->select('id', 'name', 'img', 'phone')->get()->toArray();
        }

        return self::where('id', $userId)->select('id', 'name', 'img', 'phone')->first()->toArray();
    }
}

