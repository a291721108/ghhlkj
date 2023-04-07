<?php

namespace App\Http\Controllers\Common;

use App\Models\Encryption;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class UploadController extends BaseController
{

    /**
     * status状态（默认1关闭2）
     */
   protected $status;

    /**
     * 文件类型
     */
    protected $fileType =[];

    /**
     * 限制文件大小
     * 单位为zB
     */
    protected $limitSize = 0;

    /**
     * 初始化
     */
    public function __construct()
    {
        $query           = Encryption::where('id', '>', 0)->first();
        $this->status    = $query->status;
        $this->fileType  = explode(',', $query->type);
        $this->limitSize = $query->file_size * 1024;
    }

    /***
     * showdoc
     * @catalog API/公共
     * @title 图片上传
     * @description 图片上传
     * @method post
     * @url 47.92.82.25/api/saveFile
     *
     * @param file 必选 string 图片地址
     *
     * @return {"code":200,"msg":"成功","data":[]}
     *
     * @return_param code int 状态吗(200:请求成功,404:请求失败)
     * @return_param msg string 返回信息
     * @return_param data array 返回数据
     * @return_param url string 图片路径
     *
     * @remark
     * @number 6
     */
    public function saveFile(Request $request)
    {
        $this->validate($request, [
            'file' => 'required|file'
        ]);

        $file = $request->file('file');
        // 监测上传文件是否合法
        if (!$file->isValid()) {
            $this->error('error');
        }
        // 获取文件名自带后缀
        $filename = $file->getClientOriginalName();
        // 重命名
        $fileNewName = date("YmdHms_", time()) . $filename;
        // 保存位置
        $dir = env("UPLOAD_DIR");

        $pathName= '';
        // 进行文件加密
        if ($this->status == Encryption::ENCRYPTION_STATUS_ONE && in_array($file->getClientOriginalExtension(), $this->fileType)) {

            if ($file->getSize() <= $this->limitSize) {
                // 加密文件
                $encryptFile = Crypt::encrypt(file_get_contents($file->getRealPath()));
                $pathName = $dir . "/" . $fileNewName;
                // 生成加密文件
                file_put_contents($pathName, $encryptFile);
            }
        }
        else {
            // 转移文件
            $pathName = $file->move($dir, $fileNewName);
        }

        // 标准格式回惨
        if ($request->type == 2){
            return $this->success('success', '200', ['url' => env("APP_URL") . $pathName]);
        }

        if (is_file($pathName)) {
            return json_encode(['url' => env("APP_URL") . $pathName]);
        }

        $this->error('error');
    }


    /**
     * @catalog 后台/公共
     * @title 文件下载
     * @description 文件下载
     * @method get
     * @url 39.105.183.79/admin/download
     *
     * @param file 必选 string 文件地址
     *
     * @return {"code":200,"msg":"成功","data":[]}
     *
     * @return_param code int 状态吗(200:请求成功,404:请求失败)
     * @return_param msg string 返回信息
     * @return_param data array 返回数据
     * @return_param url string 图片路径
     *
     * @remark
     * @number 2
     */
    public function download(Request $request)
    {
        $filename = $request->file;
        $filename = mb_convert_encoding($filename,'GBK','UTF-8');
        $filepath = env("UPLOAD_DIR") . '/' . $filename;

        if (file_exists($filepath)) {
            $filesize = filesize($filepath);

            header("Content-type:application/octet-stream");
            header("Accept-Ranges:bytes");
            header("Accept-Length:" . $filesize);
            header("Content-Disposition: attachment; filename=" . $filename);

            try {
                echo Crypt::decrypt(file_get_contents($filepath));
            } catch (DecryptException $e) {
                echo file_get_contents($filepath);
            }
        }
    }
}
