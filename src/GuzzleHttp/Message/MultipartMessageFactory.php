<?php

namespace GuzzleHttp\Message;

use GuzzleHttp\Stream\Stream;

class MultipartMessageFactory extends MessageFactory {

    /**
     * {@inheritdoc}
     */
    public function createResponse($statusCode, array $headers = [], $body = null, array $options = [])
    {
        if (null !== $body) {
            $body = Stream::factory($body);
        }

        return new MultipartResponse($statusCode, $headers, $body, $options);
    }
}
