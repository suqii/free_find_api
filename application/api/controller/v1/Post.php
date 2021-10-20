<?php

namespace app\api\controller\v1;

use think\Controller;
use think\Request;
use app\common\controller\BaseController;
use app\common\validate\PostValidate;
use app\common\model\Post as PostModel;

class Post extends BaseController
{
    // 发布文章
    public function create()
    {
        (new PostValidate())->goCheck('create');
        $data = (new PostModel) -> createPost();
        return self::showResCode('发布成功', ['detail'=>$data]);
    }

    // 文章详情
    public function index()
    {
        // 验证文章id
        (new PostValidate())->goCheck('detail');
        $detail = (new PostModel) -> getPostDetail();
        return self::showResCode('获取成功', ['detail'=>$detail]);
    }

    // 文章评论列表
    public function comment()
    {
        // 验证文章id
        (new PostValidate())->goCheck('detail');
        $list = (new PostModel) -> getComment();
        return self::showResCode('获取成功', ['list'=>$list]);
    }
  
  
    // 获取关注的人的公开文章列表
    public function followPost()
    {
        (new PostValidate())->goCheck('list');
        $list = (new PostModel) -> getfollowPost();
        return self::showResCode('获取成功', ['list'=>$list]);
    }
    // 删除文章
    public function deletePost()
    {
      $list = (new PostModel) -> deletePost();
      return self::showAdminResCode(20000, ['data'=>$list]);
    }
    // 词云内容
    public function wordCloud()
    {
      $list = (new PostModel) -> wordCloud();
      return self::showAdminResCode(20000, ['data'=>$list]);
    }
}
