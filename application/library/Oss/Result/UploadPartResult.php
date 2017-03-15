<?php
defined('BASE_PATH') OR exit('No direct script access allowed');

/**
 * Class UploadPartResult
 * @package OSS\Result
 */
class Oss_Result_UploadPartResult extends Oss_Result_Result
{
    /**
     * 结果中part的ETag
     *
     * @return string
     * @throws OssException
     */
    protected function parseDataFromResponse()
    {
        $header = $this->rawResponse->header;
        if (isset($header["etag"])) {
            return $header["etag"];
        }
        throw new Oss_Core_Exception("cannot get ETag");

    }
}