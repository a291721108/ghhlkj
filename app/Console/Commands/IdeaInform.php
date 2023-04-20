<?php

namespace App\Console\Commands;


use App\Models\Booking;
use App\Models\Idea;
use Illuminate\Console\Command;

class IdeaInform extends Command
{
    protected $signature = 'ideaInform:ok';

    /**
     * 意见反馈通知
     * @return void
     */
    public function handle()
    {

        $query = Idea::where('idea_status', '>', Idea::IDEA_STATUS_TWO)->select()->get();

        // 判断预约超期状态
        $arr = [];
        foreach ($query as $v) {
            if ($v->is_inform != Idea::IDEA_TYPE_TWO) {
                $arr[] = $v->id;
            }
        }

        if (!empty($arr)) {
                Idea::whereIn('id', $arr)->update([
                'is_inform' => Idea::IDEA_TYPE_TWO
            ]);
        }

    }

}
