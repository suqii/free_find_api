<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

use PHPMailer\PHPMailer;
// 应用公共文件
function SendEmail($title, $Address, $body){
  $mail = new PHPMailer\PHPMailer();//实例化
  $mail->IsSMTP();// 启用SMTP
  $mail->Host = "smtp.qq.com";//SMTP服务器 以qq邮箱为例子 还可以是smtp.163.com 等等其它的smtp服务地址
  $mail->Port = 465;//邮件发送端口 一般为465 不需要修改
  $mail->SMTPAuth = true;//启用SMTP认证
  $mail->SMTPSecure = "ssl";// 设置安全验证方式为ssl
  $mail->CharSet = "UTF-8";//字符集
  $mail->Encoding = "base64";//编码方式
  $mail->Username = "3219844509@qq.com";//你的邮箱(是开启了smtp服务的邮箱，随便写是无效的)
  $mail->Password = "flelodvspliqdfbf";//你的smtp服务密码（是服务密码不是登陆密码，写登陆密码也是无效的）
  $mail->From = "3219844509@qq.com";//发件人邮箱地址(这里也填smtp服务邮箱就好)
  $mail->FromName = "趣寻";//发件人的名字(这个就随便了，什么阿猫阿狗都行)  //下面这些不需要修改  
  $mail->Subject = $title;//邮件标题
  $mail->AddAddress($Address);//收件人邮箱
  $mail->IsHTML(true);//支持html格式内容 
  $mail->Body = $body;//邮件主体内容
  if ($mail->Send()) {
      return true;
  }else{
      return true; "Mailer Error: ".$mail->ErrorInfo;// 输出错误信息 
  }
}
// 异常类输出函数
function TApiException($msg = '异常', $errorCode = 999, $code = 400){
    throw new \app\lib\exception\BaseException(['code'=>$code,'msg'=>$msg,'errorCode'=>$errorCode]);
}

// 获取文件完整url
function getFileUrl($url='')
{
    if (!$url) return;
    if (filter_var($url, FILTER_VALIDATE_URL) === false) {
        return config('app.app_host').$url;
    }
    return $url;
}

function httpWurl($url, $params, $method = 'GET', $header = array(), $multi = false){
    date_default_timezone_set('PRC');
    $opts = array(
        CURLOPT_TIMEOUT        => 30,
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_HTTPHEADER     => $header,
        CURLOPT_COOKIESESSION  => true,
        CURLOPT_FOLLOWLOCATION => 1,
        CURLOPT_COOKIE         =>session_name().'='.session_id(),
    );
    /* 根据请求类型设置特定参数 */
    switch(strtoupper($method)){
        case 'GET':
            // $opts[CURLOPT_URL] = $url . '?' . http_build_query($params);
            // 链接后拼接参数  &  非？
            $opts[CURLOPT_URL] = $url . '?' . http_build_query($params);
            break;
        case 'POST':
            //判断是否传输文件
            $params = $multi ? $params : http_build_query($params);
            $opts[CURLOPT_URL] = $url;
            $opts[CURLOPT_POST] = 1;
            $opts[CURLOPT_POSTFIELDS] = $params;
            break;
        default:
        TApiException('不支持的请求方式！');
    }
    /* 初始化并执行curl请求 */
    $ch = curl_init();
    curl_setopt_array($ch, $opts);
    $data  = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);
    if($error) TApiException('请求发生错误：' . $error);
    return  $data;
}
