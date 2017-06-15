<?php
namespace MNS\AliyunMNS;

use MNS\AliyunMNS\Http\HttpClient;
use MNS\AliyunMNS\AsyncCallback;
use MNS\AliyunMNS\Model\TopicAttributes;
use MNS\AliyunMNS\Model\SubscriptionAttributes;
use MNS\AliyunMNS\Model\UpdateSubscriptionAttributes;
use MNS\AliyunMNS\Requests\SetTopicAttributeRequest;
use MNS\AliyunMNS\Responses\SetTopicAttributeResponse;
use MNS\AliyunMNS\Requests\GetTopicAttributeRequest;
use MNS\AliyunMNS\Responses\GetTopicAttributeResponse;
use MNS\AliyunMNS\Requests\PublishMessageRequest;
use MNS\AliyunMNS\Responses\PublishMessageResponse;
use MNS\AliyunMNS\Requests\SubscribeRequest;
use MNS\AliyunMNS\Responses\SubscribeResponse;
use MNS\AliyunMNS\Requests\UnsubscribeRequest;
use MNS\AliyunMNS\Responses\UnsubscribeResponse;
use MNS\AliyunMNS\Requests\GetSubscriptionAttributeRequest;
use MNS\AliyunMNS\Responses\GetSubscriptionAttributeResponse;
use MNS\AliyunMNS\Requests\SetSubscriptionAttributeRequest;
use MNS\AliyunMNS\Responses\SetSubscriptionAttributeResponse;
use MNS\AliyunMNS\Requests\ListSubscriptionRequest;
use MNS\AliyunMNS\Responses\ListSubscriptionResponse;

class Topic
{
    private $topicName;
    private $client;

    public function __construct(HttpClient $client, $topicName)
    {
        $this->client = $client;
        $this->topicName = $topicName;
    }

    public function getTopicName()
    {
        return $this->topicName;
    }

    public function setAttribute(TopicAttributes $attributes)
    {
        $request = new SetTopicAttributeRequest($this->topicName, $attributes);
        $response = new SetTopicAttributeResponse();
        return $this->client->sendRequest($request, $response);
    }

    public function getAttribute()
    {
        $request = new GetTopicAttributeRequest($this->topicName);
        $response = new GetTopicAttributeResponse();
        return $this->client->sendRequest($request, $response);
    }

    public function generateQueueEndpoint($queueName)
    {
        return "acs:mns:" . $this->client->getRegion() . ":" . $this->client->getAccountId() . ":queues/" . $queueName;
    }

    public function generateMailEndpoint($mailAddress)
    {
        return "mail:directmail:" . $mailAddress;
    }

    public function generateSmsEndpoint($phone = null)
    {
        if ($phone)
        {
            return "sms:directsms:" . $phone;
        }
        else
        {
            return "sms:directsms:anonymous";
        }
    }

    public function generateBatchSmsEndpoint()
    {
        return "sms:directsms:anonymous";
    }

    public function publishMessage(PublishMessageRequest $request)
    {
        $request->setTopicName($this->topicName);
        $response = new PublishMessageResponse();
        return $this->client->sendRequest($request, $response);
    }

    public function subscribe(SubscriptionAttributes $attributes)
    {
        $attributes->setTopicName($this->topicName);
        $request = new SubscribeRequest($attributes);
        $response = new SubscribeResponse();
        return $this->client->sendRequest($request, $response);
    }

    public function unsubscribe($subscriptionName)
    {
        $request = new UnsubscribeRequest($this->topicName, $subscriptionName);
        $response = new UnsubscribeResponse();
        return $this->client->sendRequest($request, $response);
    }

    public function getSubscriptionAttribute($subscriptionName)
    {
        $request = new GetSubscriptionAttributeRequest($this->topicName, $subscriptionName);
        $response = new GetSubscriptionAttributeResponse();
        return $this->client->sendRequest($request, $response);
    }

    public function setSubscriptionAttribute(UpdateSubscriptionAttributes $attributes)
    {
        $attributes->setTopicName($this->topicName);
        $request = new SetSubscriptionAttributeRequest($attributes);
        $response = new SetSubscriptionAttributeResponse();
        return $this->client->sendRequest($request, $response);
    }

    public function listSubscription($retNum = NULL, $prefix = NULL, $marker = NULL)
    {
        $request = new ListSubscriptionRequest($this->topicName, $retNum, $prefix, $marker);
        $response = new ListSubscriptionResponse();
        return $this->client->sendRequest($request, $response);
    }
}

?>
