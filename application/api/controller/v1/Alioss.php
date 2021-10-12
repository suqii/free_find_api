<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/4/13
 * Time: 11:51
 */

namespace app\api\controller\v1;


use think\Controller;
use OSS\OssClient;
use OSS\Core\OssException;



class Oss extends Controller
{
    static $acsClient = null;
    protected $accessKeyId = null;
    protected $accessKeySecret = null;
    protected $endpoint = null;
    protected $bucket = null;

    public function _initialize()
    {
    	//根据自己的SDK路由定义
        // require_once EXTEND_PATH . '/../vendor/oss/autoload.php';
        // require_once EXTEND_PATH . '/';

        // AccessKeyId
        $this->accessKeyId = 'LTAI5tB5S5bak4Q325o4EgcR';
        // AccessKeySecret
        $this->accessKeySecret = 'o0CvstEPN7a1hOSugTlaNhAfjnKdvH';
        // Endpoint以杭州为例，其它Region请按实际情况填写。
        $this->endpoint = "Endpoint外网访问节点";
        // 存储空间名称
        $this->bucket = "suqiqi";

    }

    /**
     * 删除OSS相关链接文件
     *$object  链接
     * @return DefaultAcsClient
     */
    public function delOss($object)
    {

//链接举例   $object = 'upload/video/2020-04-15%2009%3A13%3A54-83767f8bd59dea5acddb379210398028.mp4';
        try{
            $ossClient = new OssClient($this->accessKeyId, $this->accessKeySecret,$this->endpoint);

            $res = $ossClient->deleteObject($this->bucket, $object);
        } catch(OssException $e) {
            printf(__FUNCTION__ . ": FAILED\n");
            printf($e->getMessage() . "\n");
            // Log::record('删除失败原因:'.$e->getMessage(),'notice');
            return false;
        }
        print(__FUNCTION__ . ": OK" . "\n");
    }

    /**
     * 上传文件
     * @param string $filename 文件名
     * @param $file 文件
     * @return bool|null
     */
    public function uploadFile($filename = '', $file)
    {

        // 设置文件名称。
        $object = 'upload/video/' . date("Y-m-d h:i:s") . '-' . $filename;
// <yourLocalFile>由本地文件路径加文件名包括后缀组成，例如/users/local/myfile.txt。

        try {
            $ossClient = new OssClient($this->accessKeyId, $this->accessKeySecret, $this->endpoint);

            $res = $ossClient->uploadFile($this->bucket, $object, $file['tmp_name']);

            return $res;
        } catch (OssException $e) {
            // Log::record('上传失败原因:'.$e->getMessage(),'notice');
            return false;
        }

    }
}
