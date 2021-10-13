<?php

namespace app\api\controller\v1;

use think\Controller;
use think\Request;
use app\common\controller\BaseController;
use app\common\model\TopicClass as TopicClassModel;
use app\common\validate\TopicClassValidate;

class TopicClass extends BaseController
{
    public function index()
    {

        // 获取话题分类列表
        $list=(new TopicClassModel)->getTopicClassList();
        // return self::showResCode('获取成功',['list'=>$list]);
        return self::showAdminResCode(20000, ['list'=>$list]);
    }

    // 获取指定话题分类下的话题列表
    public function topic()
    {
        // 验证分类id和分页数
        (new TopicClassValidate())->goCheck();
        $list=(new TopicClassModel)->getTopic();
        // return self::showResCode('获取成功',['list'=>$list]);
        return self::showAdminResCode(20000, ['list'=>$list]);
    }

    // 新增分类
    public function topicClassAdd()
    {
        // 获取话题分类列表
        $list=(new TopicClassModel)->topicClassAdd();
        return self::showAdminResCode(20000, ['data'=>$list]);
    }
    // 编辑分类
    public function topicClassEdit()
    {
        $list=(new TopicClassModel)->topicClassEdit();
        return self::showAdminResCode(20000, ['data'=>$list]);
    }
    // 分类删除
    public function topicClassDelete()
    {
        $list=(new TopicClassModel)->topicClassDelete();
        return self::showAdminResCode(20000, ['data'=>$list]);
    }
}
