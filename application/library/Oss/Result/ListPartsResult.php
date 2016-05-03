<?php
defined('APPLICATION_PATH') OR exit('No direct script access allowed');

/**
 * Class ListPartsResult
 * @package OSS\Result
 */
class Oss_Result_ListPartsResult extends Oss_Result_Result
{
    /**
     * 解析ListParts接口返回的xml数据
     *
     * @return ListPartsInfo
     */
    protected function parseDataFromResponse()
    {
        $content = $this->rawResponse->body;
        $xml = simplexml_load_string($content);
        $bucket = isset($xml->Bucket) ? strval($xml->Bucket) : "";
        $key = isset($xml->Key) ? strval($xml->Key) : "";
        $uploadId = isset($xml->UploadId) ? strval($xml->UploadId) : "";
        $nextPartNumberMarker = isset($xml->NextPartNumberMarker) ? intval($xml->NextPartNumberMarker) : "";
        $maxParts = isset($xml->MaxParts) ? intval($xml->MaxParts) : "";
        $isTruncated = isset($xml->IsTruncated) ? strval($xml->IsTruncated) : "";
        $partList = array();
        if (isset($xml->Part)) {
            foreach ($xml->Part as $part) {
                $partNumber = isset($part->PartNumber) ? intval($part->PartNumber) : "";
                $lastModified = isset($part->LastModified) ? strval($part->LastModified) : "";
                $eTag = isset($part->ETag) ? strval($part->ETag) : "";
                $size = isset($part->Size) ? intval($part->Size) : "";
                $partList[] = new Oss_Model_PartInfo($partNumber, $lastModified, $eTag, $size);
            }
        }
        return new Oss_Model_ListPartsInfo($bucket, $key, $uploadId, $nextPartNumberMarker, $maxParts, $isTruncated, $partList);
    }
}