<?php

namespace app\api\controller\v1;

use think\Controller;
use think\Request;
use app\common\controller\BaseController;
use app\common\validate\AdsenseValidate;
use app\common\model\Adsense as AdsenseModel;

class Adsense extends BaseController
{
    // 获取广告列表
    public function index(){
        (new AdsenseValidate())->goCheck();
        $list = (new AdsenseModel)->getList();
        // return self::showResCode('获取成功',['list'=>$list]);
        return self::showAdminResCode(20000, ['list'=>$list]);
    }
    // 轮播图编辑
    public function adsenseEdit()
    {
        $list = (new AdsenseModel())->editAdsense();
        return self::showAdminResCode(20000, ['data'=>$list]);
    }
    // 轮播图新增
    public function adsenseAdd()
    {
        $list = (new AdsenseModel())->addAdsense();
        return self::showAdminResCode(20000, ['data'=>$list]);
    }
    // 轮播图删除
    public function adsenseDelete()
    {
        $list = (new AdsenseModel())->deleteAdsense();
        return self::showAdminResCode(20000, ['data'=>$list]);
    }
}
