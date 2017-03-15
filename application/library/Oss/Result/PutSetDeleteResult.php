<?php
defined('BASE_PATH') OR exit('No direct script access allowed');


/**
 * Class PutSetDeleteResult
 * @package OSS\Result
 */
class Oss_Result_PutSetDeleteResult extends Oss_Result_Result
{
    /**
     * @return null
     */
    protected function parseDataFromResponse()
    {
        return null;
    }
}