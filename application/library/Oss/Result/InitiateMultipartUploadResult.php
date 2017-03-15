<?php
defined('BASE_PATH') OR exit('No direct script access allowed');


/**
 * Class initiateMultipartUploadResult
 * @package OSS\Result
 */
class Oss_Result_InitiateMultipartUploadResult extends Oss_Result_Result
{
    /**
     * 结果中获取uploadId并返回
     *
     * @throws OssException
     * @return string
     */
    protected function parseDataFromResponse()
    {
        $content = $this->rawResponse->body;
        $xml = simplexml_load_string($content);
        if (isset($xml->UploadId)) {
            return strval($xml->UploadId);
        }
        throw new Oss_Core_Exception("cannot get UploadId");
    }
}