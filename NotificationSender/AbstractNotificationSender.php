<?php

namespace GrossumUA\NotificationBundle\NotificationSender;

use GrossumUA\NotificationBundle\Notification\NotificationInterface;
use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;
use Monolog\Logger;

/**
 * Class AbstractNotificationSender
 * @package GrossumUA\NotificationBundle\NotificationSender
 */
abstract class AbstractNotificationSender implements NotificationSenderInterface
{
    /** @var ProducerInterface $producer */
    private $producer;
    /** @var Logger $logger */
    private $logger;

    /**
     * AbstractNotificationSender constructor.
     * @param ProducerInterface $producer
     * @param Logger $logger
     */
    public function __construct(ProducerInterface $producer, Logger $logger)
    {
        $this->producer = $producer;
        $this->logger = $logger;
    }

    /**
     * @param NotificationInterface $message
     */
    public function sendNotification(NotificationInterface $message)
    {
        try {
            if ($message->isValid()) {
                $this->producer->publish(json_encode($message->exportData()));
            }
        } catch (\Exception $exception) {
            $this->logger->addError($exception->getMessage(), [
                    'name' => $message->getName(),
                    'data' => $message->exportData()
                ]
            );
        }
    }
}
