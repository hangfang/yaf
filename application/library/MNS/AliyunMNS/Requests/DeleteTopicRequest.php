<?php
namespace MNS\AliyunMNS\Requests;

use MNS\AliyunMNS\Constants;
use MNS\AliyunMNS\Requests\BaseRequest;
use MNS\AliyunMNS\Model\TopicAttributes;

class DeleteTopicRequest extends BaseRequest
{
    private $topicName;

    public function __construct($topicName)
    {
        parent::__construct('delete', 'topics/' . $topicName);
        $this->topicName = $topicName;
    }

    public function getTopicName()
    {
        return $this->topicName;
    }

    public function generateBody()
    {
        return NULL;
    }

    public function generateQueryString()
    {
        return NULL;
    }
}
?>
