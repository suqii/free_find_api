<?php

namespace app\common\model;

use think\Model;

class Topic extends Model

{

  // 自动写入时间
  protected $autoWriteTimestamp = true;
    // 获取热门话题列表
    public function gethotlist()
    {
        return $this->where('type', 1)->withCount(['post','todaypost'])->limit(10)->select()->toArray();
    }

    // 关联文章
    public function post()
    {
        return $this->belongsToMany('Post', 'topic_post');
    }

    // 关联今日文章
    public function todaypost()
    {
        return $this->belongsToMany('Post', 'topic_post')->whereTime('post.create_time', 'today');
    }
  
    // 获取指定话题下的文章（分页）
    public function getPost()
    {
        // 获取所有参数
        $param = request()->param();
        // 当前用户id
        $userId = request()->userId ? request()->userId : 0;
        $posts = self::get($param['id'])->post()->page($param['page'], 10)->select();
        $arr = [];
        for ($i=0; $i < count($posts); $i++) {
            $arr[] = \app\common\model\Post::with([
            'user'=>function ($query) use ($userId) {
                return $query->field('id,username,userpic')->with([
                    'fens'=>function ($query) use ($userId) {
                        return $query->where('user_id', $userId)->hidden(['password']);
                    },'userinfo'
                ]);
            },'images'=>function ($query) {
                return $query->field('url');
            },'share',
            'support'=>function ($query) use ($userId) {
                return $query->where('user_id', $userId);
            }])->withCount(['Ding','Cai','comment'])->get($posts[$i]->id)->toArray();
        }
        return $arr;
    }

    // 根据标题搜索话题
    public function Search()
    {
        // 获取所有参数
        $param = request()->param();
        return $this->where('title', 'like', '%'.$param['keyword'].'%')->withCount(['post','todaypost'])->page($param['page'], 10)->select();
    }

    // 编辑热门话题列表
    public function editHotlist()
    {
        // 获取所有参数
        $params = request()->param();
        // 获取用户id
        $topicid = request()->param('topic_id');
        // 修改昵称
        $topic = $this->get($topicid);
        if(isset($params['title'])){
          $topic->title = $params['title'];
        }
        if(isset($params['titlepic'])){
          $topic->titlepic = $params['titlepic'];
        }
        if(isset($params['desc'])){
          $topic->desc = $params['desc'];
        }
        $topic->save();
        return $topic;
    }
    // 判断话题是否存在
    public function isExist($arr=[]){
      if(!is_array($arr)) return false;
      
      // 话题名称
      if (array_key_exists('title',$arr)) { 
          return $this->where('title',$arr['title'])->find();
      }
     
      return false;
  }
    // 编辑热门话题列表
    public function addTopic()
    {
        // 获取所有参数
        $param = request()->param();
        // 验证用户是否存在
        $topic = $this->isExist(['title'=>$param['title']]);
        $title = request()->param('title');
        $desc = request()->param('desc');
        $type = request()->param('type');
        $topic_class_id = request()->param('topic_class_id');
        $titlepic = request()->param('titlepic');
        // 用户不存在，直接注册
        if (!$topic) {
            $topic = self::create([
            'title'=>$title,
            'desc'=>$desc,
            'type'=>$type,
            'topic_class_id'=>$topic_class_id,
            'titlepic'=>$titlepic,
            ]);
        }
        return $topic;
    }
}
