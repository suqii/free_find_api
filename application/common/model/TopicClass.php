<?php

namespace app\common\model;

use think\Model;

class TopicClass extends Model

{

  // 自动写入时间
  protected $autoWriteTimestamp = true;
    // 获取所有话题分类
    public function getTopicClassList(){
        return $this->field('id,classname')->where('status',1)->select();
    }
    

    // 关联话题
    public function topic(){
        return $this->hasMany('Topic');
    }

    // 获取指定话题分类下的话题（分页）
    public function getTopic(){
        // 获取所有参数
        $param = request()->param();
        return self::get($param['id'])->topic()->withCount(['post','todaypost'])->page($param['page'],10)->select();
    }

    // 判断话题是否存在
    public function isExist($arr=[]){
      if(!is_array($arr)) return false;
      
      // 话题名称
      if (array_key_exists('classname',$arr)) { 
          return $this->where('classname',$arr['classname'])->find();
      }
     
      return false;
  }

    // 新增话题分类
    public function topicClassAdd(){
      // 获取所有参数
      $param = request()->param();
      // 验证用户是否存在
      $topicClass = $this->isExist(['classname'=>$param['classname']]);
      $classname = request()->param('classname');
      $status = request()->param('status');
     
      // 分类不存在，直接添加
      if (!$topicClass) {
          $topicClass = self::create([
          'classname'=>$classname,
          'status'=>$status,
          ]);
      }
      $topicClass->save();
      return $topicClass;
  }
}
