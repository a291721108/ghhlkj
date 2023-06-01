<?php

namespace App\Service\Front;

use App\Events\MsgPushEvent;
use App\Exceptions\MessageRemind;
use App\Models\Booking;
use App\Models\Institution;
use App\Models\InstitutionHomeType;
use App\Models\Order;
use App\Models\User;
use App\Models\UserExt;
use App\Service\Common\FunService;
use StdClass;

class BookingService
{
    /**
     * 预约订单
     */
    public static function agencyAppointment($request)
    {
        $userInfo = User::getUserInfo();

        $data = [
            'user_id'           => $userInfo->id,                               //用户id
            'contacts'          => UserExt::getMsgByUserName($userInfo->id),    //联系人
            'order_phone'       => $request->order_phone,                       //联系方式
            'contacts_card'     => UserExt::getMsgByUserCard($userInfo->id),    //身份证
            'institution_id'    => $request->institution_id,                    //机构id
            'institution_type'  => $request->institution_type,                  //房型id
            'visitDate'         => strtotime($request->visitDate),              //看房时间
            'status'            => Order::ORDER_SYS_TYPE_FOUR,                  //状态
            'order_no'          => FunService::orderNumber(),                   //订单编号
            'order_remark'      => $request->remark,                      //备注
            'created_at'        => time()
        ];
        $orderId = Order::insertGetId($data);
        if ($orderId){

            $list   = [
                '{time}'
            ];
            $result = [
                ytdTampTime(strtotime($request->visitDate))
            ];

            // 时间监听 处理登录完成之后的逻辑
            $obj                    = new StdClass();
            $obj->form              = $userInfo->id;                //发送人id
            $obj->institution_id    = $request->institution_id;     //关联机构id
            $obj->order_id          = $orderId;                     //关联订单id
            $obj->name              = MessageRemind::ORDER_REMIND_ONE;
            $obj->content           = str_replace($list, $result, MessageRemind::WX_REMIND_MSG[1]);
            $obj->time              = time();
            event(new MsgPushEvent($obj));

            return true;
        }

        return false;
    }

    /**
     * @param $request
     * @return array
     */
    public static function reservationList($request)
    {
        $userInfo = User::getUserInfo();
        $userId = $userInfo->id;

        $query    = Order::where('userId',$userId)->get()->toArray();

        foreach ($query as $k => $v) {
            // 处理回参
            $data[$k] = [
                'id'                => $v['id'],
                'userId'           => $userInfo->id,
                'orderName'         => $userInfo->name,
                'orderPhone'        => $v['orderPhone'],
                'orderIDcard'       => $v['orderIDcard'],
                'institutionId'     => Institution::getInstitutionId($v['institutionId']),
                'typeId'            => InstitutionHomeType::getInstitutionIdByName($v['typeId']),
                'arrireDate'        => ytdTampTime($v['arrireDate']),
                'orderState'        => Booking::INS_MSG_ARRAY[$v['orderState']],
                'roomId'            => $v['roomId'],
                'remark'            => $v['remark'],
                'created_at'        => hourMinuteSecond(time())
            ];
        }

        return $data;
    }

    /**
     * 预约订单
     */
    public static function getBookOneMsg($request)
    {
        $userInfo = User::getUserInfo();
        $bookingId = $request->bookingId;

        $bookingMsg = Booking::where('id',$bookingId)->first();

        // 返回用户信息
        return [
            "id"                    => $bookingMsg->id,
            'orderName'             => UserExt::getMsgByUserName($userInfo->id),
            'orderIDcard'           => UserExt::getMsgByUserCard($userInfo->id),
            'orderPhone'            => $bookingMsg->orderPhone,
            'institution_name'      => Institution::getInstitutionId($bookingMsg->institutionId),
            'typeId'                => InstitutionHomeType::getInstitutionIdByName($bookingMsg->typeId),
            'arrireDate'            => hourMinuteSecond($bookingMsg->arrireDate),
            'remark'                => $bookingMsg->remark,
            'roomId'                => $bookingMsg->roomId,
            'orderState'            => Booking::INS_MSG_ARRAY[$bookingMsg->orderState],
            'created_at'            => hourMinuteSecond(strtotime($bookingMsg->created_at)),
        ];

    }

}

