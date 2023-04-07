<?php

namespace App\Service\Front;

use App\Models\HomeImg;
use App\Models\Institution;
use App\Models\InstitutionHome;
use App\Models\InstitutionHomeFacilities;
use App\Models\InstitutionHomeType;
use App\Service\Common\FunService;

class HomeImgService
{

    /**
     * 获取首页轮播图
     */
    public static function slideshow()
    {
        $data = HomeImg::select('id','img')->get()->toArray();
        return $data;
    }

}

