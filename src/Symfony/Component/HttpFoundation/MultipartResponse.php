<?php

namespace Symfony\Component\HttpFoundation;

class MultipartResponse extends Response
{
    /**
     * @var string
     */
    public $subtype;

    /**
     * @var string
     */
    public $boundary;

    /**
     * @var Response[]
     */
    protected $parts;

    /**
     * Constructor.
     */
    public function __construct(array $parts = null, $status = 200, $headers = array(), $subtype = null)
    {
        parent::__construct(null, $status, $headers);

        $this->subtype = $subtype ?: 'mixed';
        $this->boundary = md5(microtime());

        if (null !== $parts) {
            $this->setParts($parts);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function prepare(Request $request)
    {
        $this->headers->set('Content-Type', "multipart/{$this->subtype}; boundary=\"{$this->boundary}\"");
        $this->headers->set('Transfer-Encoding', 'chunked');

        return parent::prepare($request);
    }

    /**
     * Sets a part of the multipart response.
     *
     * @param Response $part A response object to be part of the multipart response.
     *
     * @return MultipartResponse
     */
    public function setPart(Response $part)
    {
        $this->parts[] = $part;

        return $this;
    }

    /**
     * Sets multiple parts of the multipart response.
     *
     * @param Response[] $parts Response objects to be part of the multipart response.
     *
     * @return MultipartResponse
     */
    public function setParts(array $parts)
    {
        foreach ($parts as $part) {
            $this->setPart($part);
        }
        return $this;
    }

    /**
     * Returns the parts.
     *
     * @return Response[]
     */
    public function getParts() {
        return $this->parts;
    }

    /**
     * Sends content for the current web response.
     *
     * @return Response
     */
    public function sendContent()
    {
        $content = '';
        foreach ($this->parts as $part) {
            $content .= "--{$this->boundary}\r\n";
            $content .= "{$part->headers}\r\n";
            $content .= $part->getContent();
            $content .= "\r\n";
        }
        $content .= "--{$this->boundary}--";
        // Finally send all the content.
        echo strlen($content) . "\r\n" . $content;
        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \LogicException when the content is not null
     */
    public function setContent($content)
    {
        if (null !== $content) {
            throw new \LogicException('The content cannot be set on a MultipartResponse instance.');
        }
    }

    /**
     * {@inheritdoc}
     *
     * @return false
     */
    public function getContent()
    {
        return false;
    }
}
