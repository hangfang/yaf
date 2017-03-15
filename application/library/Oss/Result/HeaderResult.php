<?php
defined('BASE_PATH') OR exit('No direct script access allowed');


/**
 * Class HeaderResult
 * @package OSS\Result
 * @link https://docs.aliyun.com/?spm=5176.383663.13.7.HgUIqL#/pub/oss/api-reference/object&GetObjectMeta
 */
class Oss_Result_HeaderResult extends Oss_Result_Result
{
    /**
     * 把返回的ResponseCore中的header作为返回数据
     *
     * @return array
     */
    protected function parseDataFromResponse()
    {
        return empty($this->rawResponse->header) ? array() : $this->rawResponse->header;
    }

}