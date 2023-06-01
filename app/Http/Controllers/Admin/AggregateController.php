<?php

namespace App\Http\Controllers\Admin;

use App\Service\Admin\AggregateService;
use Illuminate\Http\Request;
use App\Http\Controllers\Common\BaseController;

class AggregateController extends BaseController
{
    /**
     * @catalog 商家端/总览
     * @title 总览
     * @description 总览
     * @method post
     * @url 39.105.183.79/admin/overview
     *
     * @header api_token 必选 string api_token放到authorization中
     *
     * @return {"meta":{"status":200,"msg":"成功"},"data":{"todayIncome":0,"todayPaid":0,"beenBooked":{"reserved":0,"booked yesterday":0},"haveChecked":{"reserved":0,"bookedYesterday":0},"spareRoom":{"reserved":0,"bookedYesterday":0},"orderToday":{"reserved":0,"bookedYesterday":0},"turnover":{"reserved":0,"bookedYesterday":0},"renewal":{"reserved":0,"bookedYesterday":0},"pageView":{"page_view":5000,"bookedYesterday":"3000"},"monthOrderNum":{"orderNum":0,"lastOrderNum":0},"peopleNum":{"orderNum":0,"lastOrderNum":0},"generalIncome":{"orderNum":0,"lastOrderNum":0},"expendCount":{"incomeSum":0,"lastIncomeSum":0},"totalRevenue":{"incomeSum":0,"lastIncomeSum":0}}}
     * @return_param status int status(200请求成功,404失败)
     * @return_param msg string 信息提示
     * @return_param todayIncome string 获取该公司今日总收入
     * @return_param todayPaid string 获取该公司今日总支出
     * @return_param beenBooked string 已预定
     * @return_param haveChecked string 已入住
     * @return_param spareRoom string 剩余房间
     * @return_param orderToday string 今日订单
     * @return_param turnover string 成交顾客
     * @return_param renewal string 续费顾客
     * @return_param pageView string 浏览量
     * @return_param monthOrderNum string 订单总数
     * @return_param peopleNum string 顾客总数
     * @return_param totalPageViews string 总浏览量
     * @return_param generalIncome string 总收入
     * @return_param expendCount string 总支出
     * @return_param totalRevenue string 总收益
     *
     * @remark
     * @number 2
     */
    public function overview(Request $request)
    {

        $userInfo = AggregateService::overview($request);

        if (is_array($userInfo)){
            return $this->success('success',200,$userInfo);
        }
        return $this->error('error');
    }


}
