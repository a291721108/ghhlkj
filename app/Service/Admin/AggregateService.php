<?php

namespace App\Service\Admin;

use App\Models\Institution;
use App\Models\InstitutionAdmin;
use App\Models\InstitutionHome;
use App\Models\InstitutionHomeType;
use App\Models\Order;
use App\Models\OrderRefunds;
use App\Models\UserSend;
use App\Service\Common\RedisService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;

class AggregateService
{

    public static function overview($request)
    {
        $adminInfo = InstitutionAdmin::getAdminInfo();

        // 获取今天开始时间   截止时间
        $startTime  = strtotime('today');
        $endTime    = strtotime('tomorrow') - 1;
        $dataArr    = [$startTime,$endTime];
//        $dataArr    = ['1653148800','1747843200'];

        // 获取前一天的开始时间   获取前一天的截止时间
        $startTime = strtotime('yesterday');
        $endTime = strtotime('today') - 1;
        $yesterday = [$startTime,$endTime];


        //获取改账号下的机构
        $institutionId = Institution::where('admin_id',$adminInfo->id)->first();
        $ids = $institutionId->id;

        // 获取该公司今日总收入
        $todayIncome = self::todayMoney($ids,$dataArr);

        // 获取该公司今日总支出
        $todayPaid = self::todayAmountPaid($ids,$dataArr);

        // 已预定
        $beenBooked = self::reserved($ids,$dataArr,$yesterday);

        // 已入住
        $haveChecked = self::checkIn($ids,$dataArr,$yesterday);

        //  剩余房间
        $spareRoom = self::numSpareRoom($ids,$dataArr,$yesterday);

        //  今日订单
        $orderToday = self::todayOrder($ids,$dataArr,$yesterday);

        //  成交顾客
        $turnover = self::transactionCustomer($ids,$dataArr,$yesterday);

        //  续费顾客
        $renewal = self::renewalCustomer($ids,$dataArr,$yesterday);

        //  浏览量
        $pageView = self::pageView($ids,$institutionId->page_view);

        //  本月数据

        // 获取当前日期   获取上一个月的月初日期    获取上一个月的月底日期
        $currentDate = date('Y-m-d');
        $firstDayOfPreviousMonth = strtotime(date('Y-m-01', strtotime('-1 month', strtotime($currentDate))));
        $lastDayOfPreviousMonth = strtotime(date('Y-m-t', strtotime('-1 month', strtotime($currentDate))));
        $lastMonthData = [$firstDayOfPreviousMonth,$lastDayOfPreviousMonth];

        // 获取本月初日期   获取今天的日期
        $firstDayOfMonth    = strtotime(date('Y-m-01'));
        $thisData = [$firstDayOfMonth,$currentDate];

        // 订单总数
        $monthOrderNum = self::monthOrderNum($ids,$lastMonthData,$thisData);

        //  顾客总数
        $peopleNum = self::peopleNum($ids,$lastMonthData,$thisData);

        //  总收入
        $generalIncome = self::generalIncome($ids,$lastMonthData,$thisData);

        // 总支出
        $expendCount = self::expendCount($ids,$lastMonthData,$thisData);

        //  总收益
        $totalRevenue = self::total($generalIncome,$expendCount);

        return [
            'todayIncome'   =>$todayIncome,
            'todayPaid'     =>$todayPaid,
            'beenBooked'    =>$beenBooked,
            'haveChecked'   =>$haveChecked,
            'spareRoom'     =>$spareRoom,
            'orderToday'    =>$orderToday,
            'turnover'      =>$turnover,
            'renewal'       =>$renewal,
            'pageView'      =>$pageView,
            'monthOrderNum' =>$monthOrderNum,
            'peopleNum'     =>$peopleNum,
            'generalIncome' =>$generalIncome,
            'expendCount'   =>$expendCount,
            'totalRevenue'  =>$totalRevenue,
        ];

    }

    /**
     * 总收益
     * @return array
     */
    public static function total($generalIncome,$expendCount)
    {
        $diff = $generalIncome["incomeSum"] - $expendCount["incomeSum"];

        if ($generalIncome["incomeSum"] == 0 && $expendCount["incomeSum"] == 0){
            return [
                'incomeSum'      => $diff,
                'lastIncomeSum'  => 0
            ];
        }

        $lastDiff = ($generalIncome["incomeSum"] - $expendCount["incomeSum"]) / $expendCount["incomeSum"];
        return [
            'incomeSum'      => $diff,
            'lastIncomeSum'  => number_format($lastDiff, 2)
        ];
    }

    /**
     * 总支出
     * @return array
     */
    public static function expendCount($ids,$lastMonthData,$thisData)
    {
        $expendData = Order::where('institution_id',$ids)
            ->whereBetween('created_at',$thisData)
            ->where('refundNot',Order::ORDER_CHECK_OUT_ONE)
            ->select('id')
            ->get()->toArray();
        $amountArr = array_column($expendData, "id");

        $expendRefundArr = [];
        foreach ($amountArr as $k => $v){
            $expendRefundData = OrderRefunds::where('order_id',$v)->select('amount')->get()->toArray();
            foreach ($expendRefundData as $kev => $val){
                $expendRefundArr[] = $val['amount'];
            }
        }
        $expendSum = array_sum($expendRefundArr);

        $lastExpendData = Order::where('institution_id',$ids)
            ->whereBetween('created_at',$lastMonthData)
            ->where('refundNot',Order::ORDER_CHECK_OUT_ONE)
            ->select('id')
            ->get()->toArray();
        $lastAmountArr = array_column($lastExpendData, "id");

        $lastExpendRefundArr = [];
        foreach ($lastAmountArr as $k => $v){
            $lastExpendRefundData = OrderRefunds::where('order_id',$v)->select('amount')->get()->toArray();
            foreach ($lastExpendRefundData as $kev => $val){
                $lastExpendRefundArr[] = $val['amount'];
            }
        }
        $lastExpendSum = array_sum($lastExpendRefundArr);

        if ($lastExpendSum == 0) {
            return [
                'incomeSum'      => $expendSum,
                'lastIncomeSum'  => 0
            ];
        }

        $data = ($expendSum - $lastExpendSum) / $lastExpendSum;
        return [
            'incomeSum'      => $expendSum,
            'lastIncomeSum'  => number_format($data, 2)
        ];
    }


    /**
     * 总收入
     * @return array
     */
    public static function generalIncome($ids,$lastMonthData,$thisData)
    {
        $incomeData = Order::where('institution_id',$ids)->whereBetween('created_at',$thisData)->select('id','amount_paid')->get()->toArray();
        $amountArr = array_column($incomeData, "amount_paid");
        $incomeSum = array_sum($amountArr);

        $lastIncomeData = Order::where('institution_id',$ids)->whereBetween('created_at',$lastMonthData)->select('id','amount_paid')->get()->toArray();
        $lastAmountArr = array_column($lastIncomeData, "amount_paid");
        $lastIncomeSum = array_sum($lastAmountArr);

        if ($lastIncomeSum == 0){
            return [
                'orderNum'  => $incomeSum,
                'lastOrderNum'  => 0
            ];
        }

        $data = ($incomeSum - $lastIncomeSum) / $lastIncomeSum;
        return [
            'incomeSum'      => $incomeSum,
            'lastIncomeSum'  => number_format($data, 2)
        ];
    }

    /**
     * 获取顾客总数
     * @return array
     */
    public static function peopleNum($ids,$lastMonthData,$thisData)
    {
        $peopleData = Order::where('institution_id',$ids)->whereBetween('created_at',$thisData)->select('id','user_id')->get()->toArray();
        $peoPleArr = array_column($peopleData, "user_id");
        $num = count(array_unique($peoPleArr));

        $lastOrderNum = Order::where('institution_id',$ids)->whereBetween('created_at',$lastMonthData)->select('id','user_id')->get()->toArray();
        $lastPeoPleArr = array_column($lastOrderNum, "user_id");
        $lastNum = count(array_unique($lastPeoPleArr));

        if ($num == 0 && $lastNum == 0){
            return [
                'orderNum'  => $num,
                'lastOrderNum'  => 0
            ];
        }
        $data = ($num - $lastNum) / $lastNum;

        return [
            'peopleNum'      => $num,
            'lastPeopleNum'  => number_format($data, 2)
        ];
    }

    /**
     * 获取本月订单总数
     * @return array
     */
    public static function monthOrderNum($ids,$lastMonthData,$thisData)
    {
        $orderNum = Order::where('institution_id',$ids)->whereBetween('created_at',$thisData)->count();

        $lastOrderNum = Order::where('institution_id',$ids)->whereBetween('created_at',$lastMonthData)->count();

        if ($orderNum ==0 && $lastOrderNum == 0){
            return [
                'orderNum'  => $orderNum,
                'lastOrderNum'  => 0
            ];
        }
        $data = ($orderNum - $lastOrderNum) / $lastOrderNum;
        return [
            'orderNum'  => $orderNum,
            'lastOrderNum'  => number_format($data, 2)
        ];
    }

    /**
     * 获取今日总金额
     * @return array
     */
    public static function todayMoney($ids,$dataArr)
    {
        $order = Order::where('institution_id',$ids)->whereBetween('created_at',$dataArr)->get()->toArray();
        $ids = array_column($order, "amount_paid");

        $nonEmptyValues = array_filter($ids, function ($value) {
            return $value !== null;
        });

        $todayMoney = array_sum($nonEmptyValues);

        return $todayMoney;
    }

    /**
     * 获取今日总支出
     * @return array
     */
    public static function todayAmountPaid($ids,$dataArr)
    {
        $order = Order::where('institution_id',$ids)->whereBetween('created_at',$dataArr)->where('refundNot',Order::ORDER_CHECK_OUT_ONE)->get()->toArray();
        $orderList = array_column($order, "id");

        $amountPaid = OrderRefunds::whereIn('order_id',$orderList)->get()->toArray();

        $ids = array_column($amountPaid, "amount");

        $nonEmptyValues = array_filter($ids, function ($value) {
            return $value !== null;
        });

        $todayMoney = array_sum($nonEmptyValues);

        return $todayMoney;
    }

    /**
     * 已预订
     * @return array
     */
    public static function reserved($ids,$dataArr,$yesterday)
    {
        $order = Order::where('institution_id',$ids)->whereBetween('created_at',$dataArr)->where('status',Order::ORDER_SYS_TYPE_FOUR)->count();

        $yesOrder = Order::where('institution_id',$ids)->whereBetween('created_at',$yesterday)->where('status',Order::ORDER_SYS_TYPE_FOUR)->count();

        return [
            'reserved'          =>$order,
            'booked yesterday'  =>$yesOrder
        ];
    }

    /**
     * 已入住
     * @return array
     */
    public static function checkIn($ids,$dataArr,$yesterday)
    {
        $order = Order::where('institution_id',$ids)->whereBetween('created_at',$dataArr)->where('status',Order::ORDER_SYS_TYPE_TWO)->count();

        $yesOrder = Order::where('institution_id',$ids)->whereBetween('created_at',$yesterday)->where('status',Order::ORDER_SYS_TYPE_TWO)->count();

        return [
            'reserved'          =>$order,
            'bookedYesterday'  =>$yesOrder
        ];
    }

    /**
     * 剩余房间数量
     * @return array
     */
    public static function numSpareRoom($ids,$dataArr,$yesterday)
    {
        $orderRoom = Order::where('institution_id',$ids)->whereBetween('created_at',$dataArr)->get()->toArray();
        $orderList = array_column($orderRoom, "roomNum");
        $filteredArray = array_filter($orderList);
        $numRoom = InstitutionHome::whereIn('id',$filteredArray)->count();

        $yesOrder = Order::where('institution_id',$ids)->whereBetween('created_at',$yesterday)->get()->toArray();
        $orderList = array_column($yesOrder, "roomNum");
        $yesterdayArray = array_filter($orderList);
        $yesterdaynumRoom = InstitutionHome::whereIn('id',$yesterdayArray)->count();

        return [
            'reserved'          =>$numRoom,
            'bookedYesterday'   =>$yesterdaynumRoom
        ];
    }

    /**
     * 今日订单
     * @return array
     */
    public static function todayOrder($ids,$dataArr,$yesterday)
    {
        $order = Order::where('institution_id',$ids)->whereBetween('created_at',$dataArr)->count();
        $yesOrder = Order::where('institution_id',$ids)->whereBetween('created_at',$yesterday)->count();

        return [
            'reserved'          =>$order,
            'bookedYesterday'   =>$yesOrder
        ];
    }

    /**
     * 成交顾客
     * @return array
     */
    public static function transactionCustomer($ids,$dataArr,$yesterday)
    {
        $order = Order::where('institution_id',$ids)->whereBetween('created_at',$dataArr)->where('amount_paid','!=','0')->count();
        $yesOrder = Order::where('institution_id',$ids)->whereBetween('created_at',$yesterday)->where('amount_paid','!=','0')->count();

        return [
            'reserved'          =>$order,
            'bookedYesterday'   =>$yesOrder
        ];
    }

    /**
     * 成交顾客
     * @return array
     */
    public static function renewalCustomer($ids,$dataArr,$yesterday)
    {
        $order = Order::where('institution_id',$ids)->whereBetween('created_at',$dataArr)->where('renewalNot','1')->count();
        $yesOrder = Order::where('institution_id',$ids)->whereBetween('created_at',$yesterday)->where('renewalNot','1')->count();

        return [
            'reserved'          =>$order,
            'bookedYesterday'   =>$yesOrder
        ];
    }

    /**
     * 浏览量
     * @return array
     */
    public static function pageView($ids,$page_view)
    {
        // 传入的键
        $requestedKey = 'gh_view_'.$ids;

        // 获取存储在 Redis 中的值
        Redis::select('1');
        $storedValue = RedisService::get($requestedKey);

        return [
            'page_view'         =>$page_view,
            'bookedYesterday'   =>$storedValue ?? 0
        ];
    }
}









