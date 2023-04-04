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


    /***
     * showdoc
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




}
