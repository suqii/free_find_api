<?php

namespace app\common\model;

use think\Model;

class TopicClass extends Model
{

  // 自动写入时间
    protected $autoWriteTimestamp = true;
    // 获取所有话题分类
    public function getTopicClassList()
    {
        return $this->field('id,classname,create_time,classpic,longitude,latitude')->where('status', 1)->select();
    }
    

    // 关联话题
    public function topic()
    {
        return $this->hasMany('Topic');
    }

    // 获取指定话题分类下的话题（分页）
    public function getTopic()
    {
        // 获取所有参数
        $param = request()->param();
        return self::get($param['id'])->topic()->withCount(['post','todaypost'])->page($param['page'], 10)->select();
    }

    // 判断话题是否存在
    public function isExist($arr=[])
    {
        if (!is_array($arr)) {
            return false;
        }
      
        // 话题名称
        if (array_key_exists('classname', $arr)) {
            return $this->where('classname', $arr['classname'])->find();
        }
     
        return false;
    }

    // 新增分类
    public function topicClassAdd()
    {
        // 获取所有参数
        $param = request()->param();
        $topicClass = $this->isExist(['classname'=>$param['classname']]);
        $classname = request()->param('classname');
        $classpic = request()->param('classpic');
        $longitude = request()->param('longitude');
        $latitude = request()->param('latitude');
        $status = request()->param('status');
        // 分类不存在，直接添加
        if (!$topicClass) {
            $topicClass = self::create([
          'classname'=>$classname,
          'classpic'=>$classpic,
          'status'=>$status,
          'longitude'=>$longitude,
          'latitude'=>$latitude,
          ]);
        }
        $topicClass->save();
        return $topicClass;
    }
    // 编辑分类
    public function topicClassEdit()
    {
        // 获取所有参数
        $params = request()->param();
        $topicClassid = request()->param('topic_class_id');
        $topicClass = $this->get($topicClassid);
        if (isset($params['classname'])) {
            $topicClass->classname = $params['classname'];
        }
        if (isset($params['classpic'])) {
            $topicClass->classpic = $params['classpic'];
        }
        if (isset($params['longitude'])) {
            $topicClass->longitude = $params['longitude'];
        }
        if (isset($params['latitude'])) {
            $topicClass->latitude = $params['latitude'];
        }
        $topicClass->save();
        return $topicClass;
    }
    // 删除分类
    public function topicClassDelete()
    {
        $topic_class_id = request()->param('topic_class_id');
        $result = $this->where('id', $topic_class_id)->delete();
        if ($result==1) {
            $msg = '删除成功';
        } else {
            $msg = '删除失败';
        }
        return $msg;
    }
}
