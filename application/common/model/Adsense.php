<?php

namespace app\common\model;

use think\Model;

class Adsense extends Model
{
    // 自动写入时间
    protected $autoWriteTimestamp = true;
    // 获取广告列表
    public function getList()
    {
        $param = request()->param();
        return $this->where('type', $param['type'])->select();
    }
    // 判断话题是否存在
    public function isExist($arr=[])
    {
        if (!is_array($arr)) {
            return false;
        }
       
        // 话题名称
        if (array_key_exists('src', $arr)) {
            return $this->where('src', $arr['src'])->find();
        }
      
        return false;
    }
    // 添加轮播图
    public function addAdsense()
    {
        // 获取所有参数
        $param = request()->param();
        $adsense = $this->isExist(['src'=>$param['src']]);
        $src = request()->param('src');
        $type = request()->param('type');
        // 轮播图不存在，直接添加
        if (!$adsense) {
            $adsense = self::create([
          'src'=>$src,
          'type'=>$type,
          'url'=>'#',
          ]);
        }
        $adsense->save();
        return $adsense;
    }
    // 编辑轮播图
    public function editAdsense()
    {
        // 获取所有参数
        $params = request()->param();
        $adsenseid = request()->param('adsense_id');
        $adsense = $this->get($adsenseid);
        if (isset($params['src'])) {
            $adsense->src = $params['src'];
        }
        $adsense->save();
        return $adsense;
    }
    // 删除轮播图
    public function deleteAdsense()
    {
        $adsense_id = request()->param('adsense_id');
        $result = $this->where('id', $adsense_id)->delete();
        if ($result==1) {
            $msg = '删除成功';
        } else {
            $msg = '删除失败';
        }
        return $msg;
    }
}
