<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Common\BaseController;
use App\Service\Front\HomeImgService;
use Illuminate\Http\Request;

class HomeImgController extends BaseController
{

    /**
     * @catalog app端/轮播图
     * @title 首页轮播图
     * @description 获取首页轮播图
     * @method get
     * @url 47.92.82.25/api/slideshow
     *
     * @param
     *
     * @return {"meta":{"status":200,"msg":"成功"},"data":[{"id":1,"img":"https:\/\/picsum.photos\/200\/300"},{"id":2,"img":"https:\/\/picsum.photos\/id\/237\/200\/300"},{"id":3,"img":"https:\/\/picsum.photos\/seed\/picsum\/200\/300"}]}
     *
     * @return_param code int 状态吗(200:请求成功,404:请求失败)
     * @return_param msg string 返回信息
     * @return_param id string id
     * @return_param img string 图片路径
     *
     * @remark
     * @number 1
     */
    public function slideshow()
    {

        $data = HomeImgService::slideshow();

        if ($data) {
            return $this->success('success', '200', $data);
        }

        return $this->error('error');
    }



}
