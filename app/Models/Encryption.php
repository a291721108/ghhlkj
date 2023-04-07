<?php

namespace App\Models;

/**
 * App\Models\Encryption
 *
 * @property int $id
 * @property int|null $status 状态（默认1关闭2）
 * @property string|null $type 文件类型限制（，隔开）
 * @property int|null $file_size 文件大小
 * @property int|null $encryption_way 加密方式
 * @property int|null $encryption_des 加密算法
 * @property \Illuminate\Support\Carbon|null $created_at 创建时间
 * @property \Illuminate\Support\Carbon|null $updated_at 修改时间
 * @mixin \Eloquent
 */
class Encryption extends Common
{

    protected $table = 'gh_encryption_sys';


    public $timestamps = true;

    const  ENCRYPTION_STATUS_ONE = 1;  // 开启
    const  ENCRYPTION_STATUS_TWO = 2; // 关闭

    const  ENCRYPTION_WAY_ONE = 1;  // 部分加密
    const  ENCRYPTION_WAY_TWO = 2; // 全部加密



}
