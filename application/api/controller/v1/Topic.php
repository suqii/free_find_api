<?php

namespace app\api\controller\v1;

use think\Controller;
use think\Request;
use app\common\controller\BaseController;
use app\common\model\Topic as TopicModel;
use app\common\validate\TopicClassValidate;
class Topic extends BaseController
{
    // 获取10个话题
    public function index()
    {
        $list = (new TopicModel())->gethotlist();
        return self::showResCode('获取成功',['list'=>$list]);
    }

    // 获取指定话题下的文章列表
    public function post()
    {
        // 验证分类id和分页数
        (new TopicClassValidate())->goCheck();
        $list=(new TopicModel)->getPost();
        return self::showResCode('获取成功',['list'=>$list]);
        // return self::showAdminResCode(20000, ['list'=>$list]);
    }
    // 话题编辑
    public function topicEdit()
    {
       
      $list = (new TopicModel())->editHotlist();
      return self::showResCode('修改成功',$list);
    }
    // 话题新增
    public function topicAdd()
    {
       
      $list = (new TopicModel())->addTopic();
      return self::showResCode('新增成功',$list);
    }
}
