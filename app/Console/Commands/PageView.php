<?php

namespace App\Console\Commands;

use App\Models\Institution;
use App\Service\Common\RedisService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

class PageView extends Command
{
    protected $signature = 'PageView:ok';

    protected $description = 'Update Redis data';

    /**
     * 订单已完成状态修改
     * @return void
     */
    public function handle()
    {
        try {
            // 将浏览量存入缓存
            $institution = Institution::select('id','page_view')->get()->toArray();

            Redis::select('1');

            foreach ($institution as $k => $v){
                RedisService::set('gh_view_'.$v['id'], $v['page_view']);
            }
        }catch (\Exception $e){
            $this->error($e);
        }


    }

}
