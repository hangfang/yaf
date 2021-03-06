<?php
namespace MNS\AliyunMNS\Responses;

use MNS\AliyunMNS\Constants;
use MNS\AliyunMNS\Exception\MnsException;
use MNS\AliyunMNS\Exception\SubscriptionAlreadyExistException;
use MNS\AliyunMNS\Exception\InvalidArgumentException;
use MNS\AliyunMNS\Responses\BaseResponse;
use MNS\AliyunMNS\Common\XMLParser;

class SubscribeResponse extends BaseResponse
{
    public function parseResponse($statusCode, $content)
    {
        $this->statusCode = $statusCode;
        if ($statusCode == 201 || $statusCode == 204)
        {
            $this->succeed = TRUE;
        }
        else
        {
            $this->parseErrorResponse($statusCode, $content);
        }
    }

    public function parseErrorResponse($statusCode, $content, MnsException $exception = NULL)
    {
        $this->succeed = FALSE;
        $xmlReader = $this->loadXmlContent($content);
        try
        {
            $result = XMLParser::parseNormalError($xmlReader);

            if ($result['Code'] == Constants::INVALID_ARGUMENT)
            {
                throw new InvalidArgumentException($statusCode, $result['Message'], $exception, $result['Code'], $result['RequestId'], $result['HostId']);
            }
            if ($result['Code'] == Constants::SUBSCRIPTION_ALREADY_EXIST)
            {
                throw new SubscriptionAlreadyExistException($statusCode, $result['Message'], $exception, $result['Code'], $result['RequestId'], $result['HostId']);
            }
            throw new MnsException($statusCode, $result['Message'], $exception, $result['Code'], $result['RequestId'], $result['HostId']);
        }
        catch (\Exception $e)
        {
            if ($exception != NULL)
            {
                throw $exception;
            }
            elseif ($e instanceof MnsException)
            {
                throw $e;
            }
            else
            {
                throw new MnsException($statusCode, $e->getMessage());
            }
        }
        catch (\Throwable $t)
        {
            throw new MnsException($statusCode, $t->getMessage());
        }
    }
}

?>
