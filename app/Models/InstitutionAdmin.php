<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Tymon\JWTAuth\Contracts\JWTSubject;

class InstitutionAdmin extends Model implements AuthenticatableContract, AuthorizableContract, JWTSubject
{
    use Authenticatable, Authorizable;

    protected $table = 'gh_institution_admin';

    protected $request;

    public $timestamps = false;


    const INSTITUTION_ADMIN_STATUS_ONE = 1;  // 启用
    const INSTITUTION_ADMIN_STATUS_TWO = -1;  // 禁用

    /**
     * 信息提示
     */
    const   INS_MSG_ARRAY = [
        self::INSTITUTION_ADMIN_STATUS_ONE    => "启用",
        self::INSTITUTION_ADMIN_STATUS_TWO    => "禁用",
    ];

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
     * 退出登录
     */
    public static function logout()
    {
        return auth()->invalidate(true);
    }

    /**
     * 获取当前管理信息
     */
    public static function getAdminInfo(): ?AuthenticatableContract
    {
        return \Illuminate\Support\Facades\Auth::guard('admin')->user();
    }

}
