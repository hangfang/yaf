<?php
defined('BASE_PATH') OR exit('No direct script access allowed');


/**
 * Class AclResult getBucketAcl接口返回结果类，封装了
 * 返回的xml数据的解析
 *
 * @package OSS\Result
 */
class Oss_Result_AclResult extends Oss_Result_Result
{
    /**
     * @return string
     * @throws OssException
     */
    protected function parseDataFromResponse()
    {
        $content = $this->rawResponse->body;
        if (empty($content)) {
            throw new Oss_Core_Exception("body is null");
        }
        $xml = simplexml_load_string($content);
        if (isset($xml->AccessControlList->Grant)) {
            return strval($xml->AccessControlList->Grant);
        } else {
            throw new Oss_Core_Exception("xml format exception");
        }
    }
}