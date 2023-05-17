<?php

namespace App\Service\Front;


use App\Models\Institution;

class MerchantService
{

    /**
     * 获取机构电话
     */
    public static function getInstitutionTel($request)
    {
        $institutionMsg = Institution::where('id',$request->institution)
            ->where('status','>',Institution::INSTITUTION_SYS_STATUS_TWO)
            ->value('institution_tel');

        return ['tel' => $institutionMsg];
    }

}

