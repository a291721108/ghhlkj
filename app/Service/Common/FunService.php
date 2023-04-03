<?php
/**
 * Created by LJL.
 * Date: 2022/6/20
 * Time: 18:54
 */

namespace App\Service\Common;

use App\Models\AttendanceRecord;
use App\Models\ClaimexpenseSys;
use App\Models\Company;
use App\Models\CompanyDept;
use App\Models\CompanyPosition;
use App\Models\CompanyRole;
use App\Models\Contract;
use App\Models\Customer;
use App\Models\Institution;
use App\Models\InstitutionHome;
use App\Models\Payment;
use App\Models\Project;
use App\Models\ProjectMark;
use App\Models\ProjectType;
use App\Models\Task;
use App\Models\TaskCategory;
use App\Models\User;
use App\Models\UserExt;
use Illuminate\Support\Facades\Crypt;

class FunService
{
    /**
     * 机构数据
     * @return array
     */
    public static function getInstitutionData(): array
    {
        $homeData = Institution::where('status', '>' , InstitutionHome::Home_SYS_STATUS_TWO)->select('id', 'institution_name', 'institution_address', 'institution_img','institution_detail')->get()->toArray();
        return  array_column($homeData, null, 'id');
    }

    /**
     * 房间数据
     * @return array
     */
    public static function getHomeData(): array
    {
        $homeData = InstitutionHome::where('status', '>' , InstitutionHome::Home_SYS_STATUS_TWO)->select('id', 'institution_id', 'home_type', 'home_img','home_pic')->get()->toArray();
        return  array_column($homeData, null, 'id');
    }

}
