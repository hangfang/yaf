<?php
defined('BASE_PATH') OR exit('No direct script access allowed');

/**
 * Class BucketListInfo
 *
 * ListBuckets接口返回的数据类型
 *
 * @package OSS\Model
 */
class Oss_Model_BucketListInfo
{
    /**
     * BucketListInfo constructor.
     * @param array $bucketList
     */
    public function __construct(array $bucketList)
    {
        $this->bucketList = $bucketList;
    }

    /**
     * 得到BucketInfo列表
     *
     * @return BucketInfo[]
     */
    public function getBucketList()
    {
        return $this->bucketList;
    }

    /**
     * BucketInfo信息列表
     *
     * @var array
     */
    private $bucketList = array();
}