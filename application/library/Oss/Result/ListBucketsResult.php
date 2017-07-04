<?php
defined('APPLICATION_PATH') OR exit('No direct script access allowed');
/**
 * Class ListBucketsResult
 *
 * @package OSS\Result
 */
class Oss_Result_ListBucketsResult extends Oss_Result_Result
{
    /**
     * @return BucketListInfo
     */
    protected function parseDataFromResponse()
    {
        $bucketList = array();
        $content = $this->rawResponse->body;
        $xml = new \SimpleXMLElement($content);
        if (isset($xml->Buckets) && isset($xml->Buckets->Bucket)) {
            foreach ($xml->Buckets->Bucket as $bucket) {
                $bucketInfo = new Oss_Model_BucketInfo(strval($bucket->Location),
                    strval($bucket->Name),
                    strval($bucket->CreationDate));
                $bucketList[] = $bucketInfo;
            }
        }
        return new Oss_Model_BucketListInfo($bucketList);
    }
}