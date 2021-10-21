<?php

namespace app\common\model;

use think\Model;

class Post extends Model
{
    // 自动写入时间戳
    protected $autoWriteTimestamp = true;
  
    // 关闭自动写入update_time字段
    protected $updateTime = false;
  
    // 关联图片表
    public function images()
    {
        return $this->belongsToMany('Image', 'post_image');
    }

    // 关联顶踩表
    public function support()
    {
        return $this->hasMany('Support');
    }
  
    // 关联话题表
    public function topics()
    {
        return $this->belongsToMany('Topic', 'topic_post');
    }
    
    // 关联顶数
    public function Ding()
    {
        return $this->hasMany('Support')->where('type', 0);
    }

    // 关联踩数
    public function Cai()
    {
        return $this->hasMany('Support')->where('type', 1);
    }
  
  
  
  
    // 发布文章
    public function createPost()
    {
        // 获取所有参数
        $params = request()->param();
        $userModel = new User();
        // 获取用户id
        $user_id = request()->userId;
        $currentUser = $userModel->get($user_id);
        $path = $currentUser->userinfo->path;
        // 发布文章
        $title = mb_substr($params['text'], 0, 30);
        $post = $this->create([
            'user_id'=>$user_id,
            'title'=>$title,
            'titlepic'=>'',
            'content'=>$params['text'],
            'path'=>$path ? $path : '未知',
            'type'=>0,
            'post_class_id'=>$params['post_class_id'],
            'share_id'=>0,
            'isopen'=>$params['isopen']
        ]);
        if (!$post) {
            TApiException();
        }
        // 关联图片
        $params['imglist'] = json_decode($params['imglist'], true);
        $imglistLength = count($params['imglist']);
        if ($imglistLength > 0) {
            $ImageModel = new Image();
            $imgidarr = [];
            for ($i=0; $i < $imglistLength; $i++) {
                // 验证图片是否存在，是否是当前用户上传的
                $imagemodel = $ImageModel->isImageExist($params['imglist'][$i]['id'], $user_id);
                if ($imagemodel) {
                    // 设置第一张为封面图
                    if ($i === 0) {
                        $post->titlepic = getFileUrl($imagemodel->url);
                        $post->save();
                    }
                    $imgidarr[] = $params['imglist'][$i]['id'];
                }
            }
            // 发布关联
            if (count($imgidarr)>0) {
                $post->images()->attach($imgidarr, ['create_time'=>time()]);
            }
        }
      
        // 更新话题文章列表
        if ($params['topic_id'] > 0) {
            $post->topics()->attach($params['topic_id'], ['create_time'=>time()]);
        }
        // 返回成功
        $data = $this->with(['user'=>function ($query) {
            return $query->field('id,username,userpic')->with(['userinfo']);
        },'images'=>function ($query) {
            return $query->field('url');
        },'share'])->find($post->id);
        return $data;
    }

    // 关联用户表
    public function user()
    {
        return $this->belongsTo('User');
    }

    // 关联分享
    public function share()
    {
        return $this->belongsTo('Post', 'share_id', 'id');
    }

    // 获取文章详情
    public function getPostDetail()
    {
        // 获取所有参数
        $param = request()->param();
        return $this->with(['user'=>function ($query) {
            return $query->field('id,username,userpic')->with(['userinfo']);
        },'images'=>function ($query) {
            return $query->field('url');
        },'share'])->find($param['id']);
    }

    // 根据标题搜索文章
    public function Search()
    {
        // 获取所有参数
        $param = request()->param();
        // 当前用户id
        $userId = request()->userId ? request()->userId : 0;
        return $this->where('title', 'like', '%'.$param['keyword'].'%')->with([
             'user'=>function ($query) use ($userId) {
                 return $query->field('id,username,userpic')->with([
                    'fens'=>function ($query) use ($userId) {
                        return $query->where('user_id', $userId)->hidden(['password']);
                    },'userinfo'
                ]);
             },'images'=>function ($query) {
                 return $query->field('url');
             },'share'
        ,'support'=>function ($query) use ($userId) {
            return $query->where('user_id', $userId);
        }])->withCount(['Ding','Cai','comment'])->page($param['page'], 10)->order('create_time', 'desc')->select();
    }

    // 关联评论
    public function comment()
    {
        return $this->hasMany('Comment');
    }

    // 获取评论
    public function getComment()
    {
        $params = request()->param();
        return self::get($params['id'])->comment()->with([
            'user'=>function ($query) {
                return $query->field('id,username,userpic');
            }
        ])->select();
    }

    // 获取关注的人的公开文章列表
    public function getfollowPost()
    {
        // 获取所有参数
        $param = request()->param();
        $userId = request()->userId;
        if (!$userId) {
            return [];
        }
        // 获取当前用户所有关注的人
        $followList = model('User')->getFollowsList();
        $count = count($followList);
        if ($count<1) {
            return [];
        }
        $ids = [];
        for ($i=0; $i < $count; $i++) {
            $ids[] = $followList[$i]['id'];
        }
        // 获取文章列表
        return $this->where('user_id', 'in', $ids)->with([
            'user'=>function ($query) use ($userId) {
                return $query->field('id,username,userpic')->with([
                    'fens'=>function ($query) use ($userId) {
                        return $query->where('user_id', $userId)->hidden(['password']);
                    },'userinfo'
                ]);
            },'images'=>function ($query) {
                return $query->field('url');
            },'share'
            ,'support'=>function ($query) use ($userId) {
                return $query->where('user_id', $userId);
            }])->withCount(['Ding','Cai','comment'])->page($param['page'], 10)->order('create_time', 'desc')->select();
    }
    // 删除热门话题列表
    public function deletePost()
    {
        $post_id = request()->param('post_id');
        $result = $this->where('id', $post_id)->delete();
        if ($result==1) {
            $msg = '删除成功';
        } else {
            $msg = '删除失败';
        }
        return $msg;
    }
    // 词云内容获取
    public function wordCloud()
    {
        $data = $this->field('title')->select();
        $num = count($data);
        $con ='';
        for ($x = 0; $x < $num; $x++) {
            $con .= $data[$x]['title'];
        }
        return $con;
    }
    // 获取首页折线图数据
    public function lineCharPostData()
    {
      // 近七天新增用户数
        $numMan = array();
        $numWoman = array();
        $dataX = array();
        $num=-2;
        for ($i=0;$i<7;$i+=1) {
          $num+=1;
          $startDay = date('Y-m-d', strtotime('-'.($num+1).'days'));
          $endDay = date('Y-m-d', strtotime('-'.($num).'days'));
            // 统计男性用户
            $newPostMan = $this
            ->alias('p')
            ->join('userinfo i', 'i.user_id=p.user_id')
            ->where('create_time','between time',[$startDay,$endDay])
            ->where('i.sex', 1)
            ->field('p.title')
            ->select();
            array_unshift($numMan, count($newPostMan));
            // 统计女性用户
            $newPostWoman = $this
            ->alias('p')
            ->join('userinfo i', 'i.user_id=p.user_id')
            ->where('create_time','between time',[$startDay,$endDay])
            ->where('i.sex', 2)
            ->field('p.title')
            ->select();
            array_unshift($numWoman, count($newPostWoman));
            // 时间
            $time = date('m-d', strtotime('-'.($num+1).'days'));
            array_unshift($dataX, $time);
        }
        $newPostNum = $this
            ->alias('p')
            ->join('userinfo i', 'i.user_id=p.user_id')
            ->whereBetweenTime('create_time', '-6days','-0days')
            ->field('p.title')
            ->select();
        // array_splice($numMan, 0, 1);
        // array_splice($numWoman, 0, 1);
        $postCharData['totalNum'] = count($newPostNum);
        $postCharData['dataWoman'] = $numWoman;
        $postCharData['dataMan'] = $numMan;
        $postCharData['dataX'] = $dataX;
        return $postCharData;
    }
}
