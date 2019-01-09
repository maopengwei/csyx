<?php
/**
 * Created by Maopw
 * Todo: 升级会员
 * Date: 2018年12月21日
 * 
 */

namespace app\common\logic;

use think\Db;

class Up 
{
    /*
        ①贵宾会员推荐产品达到20套（直推），即可成为业务主管；
        业务主管下面产生10名业务主管（10名主管不在一条线上），即可升级为经理；
        经理下面产生6名经理即可升级为高级经理（6名经理不在一条线上）；
        （职级的身份是动态的，随着下一级的变化而自动变化。关于下级人数多少才能晋升，数字可以在后台设置）
    */
    public static function upZhu($id,$ll){
        $i = $ll + 1;
        $level = cache('level')[$ll];
        $dir = Db::name('user')->where('us_pid',$id)->where('us_is_vip','1')->where('us_level','>=',$ll)->count();
        if($dir>=$level['cal_condition']){
            Db::name('User')->where('id',$id)->setfield('us_level',$i);
            $pid = Db::name('User')->where('id',$id)->value('us_pid');
            if($pid){
                self::upZhu($pid,$i);
            }
        }
    }
}