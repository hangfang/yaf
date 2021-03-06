<?php
namespace MNS\AliyunMNS\Requests;

use MNS\AliyunMNS\Constants;
use MNS\AliyunMNS\Requests\BaseRequest;
use MNS\AliyunMNS\Model\QueueAttributes;

class DeleteQueueRequest extends BaseRequest
{
    private $queueName;

    public function __construct($queueName)
    {
        parent::__construct('delete', 'queues/' . $queueName);
        $this->queueName = $queueName;
    }

    public function getQueueName()
    {
        return $this->queueName;
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
