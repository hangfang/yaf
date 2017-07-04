<?php
defined('APPLICATION_PATH') OR exit('No direct script access allowed');
/**
 * Class GetWebsiteResult
 * @package OSS\Result
 */
class Oss_Result_GetWebsiteResult extends Oss_Result_Result
{
    /**
     * 解析WebsiteConfig数据
     *
     * @return WebsiteConfig
     */
    protected function parseDataFromResponse()
    {
        $content = $this->rawResponse->body;
        $config = new Oss_Model_WebsiteConfig();
        $config->parseFromXml($content);
        return $config;
    }

    /**
     * 根据返回http状态码判断，[200-299]即认为是OK, 获取bucket相关配置的接口，404也认为是一种
     * 有效响应
     *
     * @return bool
     */
    protected function isResponseOk()
    {
        $status = $this->rawResponse->status;
        if ((int)(intval($status) / 100) == 2 || (int)(intval($status)) === 404) {
            return true;
        }
        return false;
    }
}