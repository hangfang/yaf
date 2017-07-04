<?php
defined('APPLICATION_PATH') OR exit('No direct script access allowed');

/**
 * Class BodyResult
 * @package OSS\Result
 */
class Oss_Result_BodyResult extends Oss_Result_Result
{
    /**
     * @return string
     */
    protected function parseDataFromResponse()
    {
        return empty($this->rawResponse->body) ? "" : $this->rawResponse->body;
    }
}