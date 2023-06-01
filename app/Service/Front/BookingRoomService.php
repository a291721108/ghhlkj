<?php

namespace App\Service\Front;

use App\Models\BookingRoom;
use App\Models\Institution;
use App\Models\InstitutionHomeType;
use App\Models\Order;
use App\Models\User;
use App\Models\UserExt;
use App\Service\Common\FunService;

class BookingRoomService
{
    /**
     * 订房信息
     */
    public static function reservationInformation($request)
    {
        $userInfo = User::getUserInfo();

        $homeMoney = InstitutionHomeType::where('id',$request->typeId)->first();

        //计算相差月数
        $data = getMonthDiff($request->startDate,$request->leaveDate);


        // todo 待完善  定价支付  500

        $data = [
            'user_id'           => $userInfo->id,                                //用户id
            'contacts'          => UserExt::getMsgByUserName($userInfo->id),     //联系人
            'order_phone'       => $request->orderPhone,                         //联系方式
            'contacts_card'     => UserExt::getMsgByUserCard($userInfo->id),     //身份证
            'institution_id'    => $request->institutionId,                      //机构id
            'institution_type'  => $request->typeId,                             //房型id
            'start_date'        => $request->startDate,               //入住日期
            'end_date'          => $request->leaveDate,               //结束日期
            'total_amount'      => $data * $homeMoney->home_price,               //订单总金额
            'wait_pay'          => $data * $homeMoney->home_price - $request->payment,  //待支付金额
            'amount_paid'       => $request->payment,                            //定金500
            'status'            => Order::ORDER_SYS_TYPE_FOUR,                     //状态（ 1提交订单  2订房成功 0取消）
            'order_no'           => FunService::orderNumber(),                    //订单编号
            'order_remark'      => $request->remark,                             //备注
            'created_at'        => time()
        ];

        return Order::insert($data);
    }

    /**
     * 获取单条订房信息
     */
    public static function getBookRoomOneMsg($request)
    {
        $userInfo = User::getUserInfo();
        $bookingId = $request->bookingRoomId;

        $bookingRoomMsg = Order::where('id',$bookingId)->where('status',Order::ORDER_SYS_TYPE_FOUR)->first();
dd($bookingRoomMsg);

        if ($bookingRoomMsg->total_amount == '500'){

        }
        // 返回用户信息
        return [
            "id"                => $bookingRoomMsg->id,
            'orderName'         => UserExt::getMsgByUserName($userInfo->id),
            'orderIDcard'       => UserExt::getMsgByUserCard($userInfo->id),
            'orderPhone'        => $bookingRoomMsg->orderPhone,
            'institution_name'  => Institution::getInstitutionId($bookingRoomMsg->institutionId),
            'typeId'            => InstitutionHomeType::getInstitutionIdByName($bookingRoomMsg->typeId),
            'startDate'         => hourMinuteSecond($bookingRoomMsg->startDate),
            'leaveDate'         => hourMinuteSecond($bookingRoomMsg->startDate),
            'payment'           => $bookingRoomMsg->payment,
            'remark'            => $bookingRoomMsg->remark,
            'roomId'            => $bookingRoomMsg->roomId,
            'status'            => BookingRoom::INS_MSG_ARRAY[$bookingRoomMsg->orderState],
            'created_at'        => hourMinuteSecond(strtotime($bookingRoomMsg->created_at)),
        ];

    }
}

