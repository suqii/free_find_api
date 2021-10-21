<?php

namespace app\api\controller\v1;

use think\Controller;
use think\Request;
use app\common\controller\BaseController;
use app\common\validate\UserValidate;
use app\common\model\User as UserModel;
use app\common\model\Post as PostModel;
use think\File;

class Admin extends BaseController
{
    // 后台登录
    public function login()
    {
        // 验证登录信息
        (new UserValidate())->goCheck('login');
        // 登录
        $user = (new UserModel())->login();
        // return self::showResCode('登录成功', $user);
        return self::showAdminResCode(20000, ['token'=> "admin-token",'userinfo' => $user]);
    }
    // 后台登出
    public function logout()
    {
        return self::showAdminResCode(20000, ['data'=> "success"]);
    }

    // info
    public function info()
    {
        // $data["roles"]=["admin"];
        // $data["introduction"]="I am a super administrator";
        // $data["avatar"]="https://wpimg.wallstcn.com/f778738c-e4f8-4870-b634-56703b4acafe.gif";
        // $data["name"]="Super Admin";
        // return self::showAdminResCode(20000, $data);
        // (new UserValidate())->goCheck('getuserinfo');
        // $data = (new UserModel())->getAdminUserInfo();
        // return self::showAdminResCode(20000,$data);

        $list=(new UserModel)->getAdminUserInfo();
        return self::showAdminResCode(20000, $list);
    }
    // list
    public function list()
    {
        // $a = [{"order_no": "bB85AFF5-CF27-F7f8-b251-eCBd4a454bEC",
        //   "timestamp": 610738427630,
        //   "username": "Laura Jackson",
        //   "price": 14033,
        //   "status": "pending"}];
        $data["total"]=20;
        // $data["items"]=[a];
        return self::showAdminResCode(20000, $data);
    }
    // 用户列表
    public function userList()
    {
        // 获取文章分类列表
        $list=(new UserModel)->userList();
        return self::showAdminResCode(20000, ['list'=>$list]);
    }
    // 新增用户
    public function userCreate()
    {
        // 验证登录信息
        // (new UserValidate())->goCheck('phonelogin');
        // 手机登录
        $user = (new UserModel())->userCreate();
        return self::showResCode('创建成功', $user);
    }
    // 删除用户
    public function userDelete()
    {
        // 验证登录信息
        (new UserValidate())->goCheck('getuserinfo');
        // 手机登录
        $user = (new UserModel())->userDelete();
        return self::showAdminResCode(20000, $user);
    }
    // 编辑用户
    public function userEdit()
    {
        // 验证登录信息
        (new UserValidate())->goCheck('getuserinfo');
        // 手机登录
        $user = (new UserModel())->userEdit();
        return self::showAdminResCode(20000, $user);
    }
    // 修改头像
    public function userHeadEdit()
    {
        (new UserValidate())->goCheck('getuserinfo');
        $src = (new UserModel())->userHeadEdit();
        return self::showAdminResCode(20000, $src);
    }
    // 获取新增10位用户
    public function newUser()
    {
        $list=(new UserModel)->getNewUser();
        return self::showAdminResCode(20000, ['newUserList'=>$list]);
    }
    // 获取新增10位用户
    public function charData()
    {
        $cardNumber=(new UserModel)->getCharData();
        return self::showAdminResCode(20000, ['line'=>$cardNumber]);
    }
    // 话题编辑
    public function topicEdit()
    {
        // $cardNumber=(new UserModel)->getCharData();
        // return self::showAdminResCode(20000, ['cardNumber'=>$cardNumber]);
        return 110;
    }
    // 日志记录
    public function getGitLog()
    {
        $freeFindLog = file_get_contents('freeFind-api-log.txt');
        $vueElAdLog = file_get_contents('vue-element-admin-log.txt');
        return self::showAdminResCode(20000, ['freeFind_api_log'=>$freeFindLog,'vue_element_admin_log'=>$vueElAdLog]);
    }
    // 获取首页折线图数据
    public function lineChartData()
    {
      $UserData=(new UserModel)->lineCharUserData();
      $PostData=(new PostModel)->lineCharPostData();
      return self::showAdminResCode(20000, ['PostData'=>$PostData,'UserData'=>$UserData]);
    }
}
