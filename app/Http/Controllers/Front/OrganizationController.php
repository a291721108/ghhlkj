<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Common\BaseController;
use App\Service\Front\OrganizationService;
use Illuminate\Http\Request;

class OrganizationController extends BaseController
{

    /**
     * @catalog app端/机构
     * @title 机构列表
     * @description 获取所有机构列表
     * @method get
     * @url 47.92.82.25/api/organizationList
     *
     * @param page 必选 string 页数
     * @param page_size 必选 string 条数
     * @param institution_serarch 非必选 string 搜索(根据关键字搜索)
     * @param page_view 非必选 string 搜索(根据浏览量搜索)
     * @param price_serarch 非必选 string 搜索(价格从低到高)
     *
     * @return {"meta":{"status":200,"msg":"成功"},"data":{"total":3,"current_page":"1","page_size":"5","pages":1,"data":[{"id":1,"institution_name":"太原市小店区第1机构","institution_address":"小店区晋阳街500号","institution_img":"https:\/\/picsum.photos\/seed\/picsum\/200\/300","price":"1000.00","created_at":"2023-04-10 19:55"},{"id":3,"institution_name":"太原市小店区第2机构","institution_address":"小店区晋阳街56号","institution_img":"https:\/\/picsum.photos\/id\/870\/200\/300?grayscale&blur=2","price":"","created_at":"2023-04-10 17:08"},{"id":2,"institution_name":"太原市迎泽区XXX机构","institution_address":"迎泽区桃园南路20号","institution_img":"https:\/\/picsum.photos\/200\/300\/?blur=2","price":"500.00","created_at":"2023-04-10 22:41"}]}}
     *
     * @return_param code int 状态吗(200:请求成功,404:请求失败)
     * @return_param msg string 返回信息
     * @return_param total int 总条数
     * @return_param current_page int 当前页
     * @return_param page_size int 每页请求条数
     * @return_param institution_name string 机构名称
     * @return_param institution_address string 机构地址
     * @return_param price string 最低价格
     *
     * @remark
     * @number 1
     */
    public function organizationList(Request $request)
    {

        $this->validate($request, [
            'page'      => 'required|numeric',
            'page_size' => 'required|numeric',
        ]);

        $data = OrganizationService::organizationList($request);

        if ($data) {
            return $this->success('success', '200', $data);
        }

        return $this->error('error');
    }


    /**
     * @catalog app端/机构
     * @title 获取机构浏览量前五
     * @description 获取机构浏览量前五
     * @method get
     * @url 47.92.82.25/api/tissueCount
     *
     * @param
     *
     * @return {"meta":{"status":200,"msg":"成功"},"data":{"total":2,"current_page":"1","page_size":"2","pages":1,"data":[{"id":2,"institution_name":"太原市小店区XXX机构","institution_address":"太原市小店区121号","institution_img":null,"institution_detail":"listlistlistlistlistlistlistlistlistlistlistlistlistlistlistlistlistlistlistlistlistlistlistlistlistlistlistlistlistlistlistlistlistlistlistlistlistlist","institution_tel":"17821211068","institution_type":2,"page_view":126,"status":1,"created_at":"2023-04-0309:00"},{"id":1,"institution_name":"太原市万柏林区XXX机构","institution_address":"太原市万柏林区12号","institution_img":null,"institution_detail":"详情详情详情详情详情详情详情详情详情详情详情详情详情详情详情详情详情详情详情详情详情详情详情详情详情详情详情详情详情详情详情","institution_tel":"17821211068","institution_type":1,"page_view":450,"status":1,"created_at":"2023-04-0309:00"}]}}
     *
     * @return_param code int 状态吗(200:请求成功,404:请求失败)
     * @return_param msg string 返回信息
     * @return_param total int 总条数
     * @return_param current_page int 当前页
     * @return_param page_size int 每页请求条数
     * @return_param institution_name [] 机构名称
     * @return_param institution_address [] 机构地址
     * @return_param institution_img [] 机构图片
     * @return_param institution_detail [] 机构详情
     * @return_param institution_tel string 机构电话
     * @return_param institution_type string 类型（1民办2政府）
     * @return_param page_view string 浏览次数
     * @return_param status string 状态（1正常，-1禁用）
     *
     * @remark
     * @number 1
     */
    public function tissueCount()
    {

        $data = OrganizationService::tissueCount();

        if ($data) {
            return $this->success('success', '200', $data);
        }

        return $this->error('error');
    }

    /**
     * @catalog app端/机构
     * @title 通过id获取机构详情列表
     * @description 通过id获取机构详情列表
     * @method post
     * @url 47.92.82.25/api/organizationDetails
     *
     * @header api_token 必选 string api_token放到authorization中
     *
     * @return {"meta":{"status":200,"msg":"成功"},"data":[{"id":1,"institution_name":"太原市小店区第1机构","institution_address":"小店区晋阳街500号","institution_img":"https:\/\/picsum.photos\/seed\/picsum\/200\/300","institution_detail":"listlistlistlistlistlistlistlistlistlistlistlistlistlistlistlistlistlistlistlistlistlistlistlistlistlistlistlistlistlistlistlistlistlistlistlistlistlistlistlistlistlistlistlistlist","institution_tel":"12345678912","institution_type":"1","page_view":"500","status":1,"institytion_type":[{"id":1,"home_type":"单人房"},{"id":2,"home_type":"双人房"},{"id":3,"home_type":"三人房"}],"created_at":"2023-04-10 19:55"}]}
     *
     * @return_param code int 状态吗(200:请求成功,404:请求失败)
     * @return_param msg string 返回信息
     * @return_param total int 总条数
     * @return_param current_page int 当前页
     * @return_param page_size int 每页请求条数
     * @return_param institution_name [] 机构名称
     * @return_param institution_address [] 机构地址
     * @return_param institution_img [] 机构图片
     * @return_param institution_detail [] 机构详情
     * @return_param institution_tel string 机构电话
     * @return_param institution_type string 类型（1民办2政府）
     * @return_param page_view string 浏览次数
     * @return_param status string 状态（1正常，-1禁用）
     * @return_param institytion_type [] 房间类型
     *
     * @remark
     * @number 1
     */
    public function organizationDetails(Request $request)
    {

        $this->validate($request, [
            'id'      => 'required|numeric',
        ]);

        $data = OrganizationService::organizationDetails($request);

        if ($data) {
            return $this->success('success', '200', $data);
        }

        return $this->error('error');
    }

    public function test()
    {

        echo "123";
    }
}
