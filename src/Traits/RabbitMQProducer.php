<?php

namespace KeyHoang\OrgModule\Traits;

use Exception;
use Illuminate\Support\Facades\Log;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Exchange\AMQPExchangeType;
use PhpAmqpLib\Message\AMQPMessage;
use function App\Traits\config;

trait RabbitMQProducer
{
    public AMQPStreamConnection $connection;
    private string              $vhost;

    /**
     * @param array  $body
     * @param string $exchange
     * @param string $type
     * @param string $routingKey
     *
     * @throws Exception
     */
    public function pushToExchange(array  $body, string $exchange, string $type = AMQPExchangeType::DIRECT,
                                   string $routingKey = ''): void
    {
        $this->init();
        $channel = $this->connection->channel();

        $channel->exchange_declare($exchange, $type, false, true, false);

        $messageBody = json_encode($body);

        $message = new AMQPMessage($messageBody, ['content_type' => 'text/plain']);

        if (empty($routingKey))
            $channel->basic_publish($message, $exchange);
        else
            $channel->basic_publish($message, $exchange, $routingKey);

        Log::info("Push to RabbitMQ exchange: $exchange, type: $type, routing key: $routingKey, body: ", $body);

        $channel->close();
        $this->connection->close();
    }

    /**
     * @throws Exception
     */
    public function init(): void
    {
        $this->connection = new AMQPStreamConnection(
            config('rabbitmq.host'),
            config('rabbitmq.port'),
            config('rabbitmq.username'),
            config('rabbitmq.password'),
            $this->getVhost()
        );
    }

    public function getVhost()
    {
        return $this->vhost ?? config('rabbitmq.vhost');
    }

    public function setVhost(string $vhost): static
    {
        $this->vhost = $vhost;

        return $this;
    }

    public function setQueue()
    {
        // Do something
    }
}
