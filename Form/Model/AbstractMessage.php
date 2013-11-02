<?php

namespace FOS\MessageBundle\Form\Model;

abstract class AbstractMessage
{
    /**
     * The message body
     *
     * @var string
     */
    protected $body;

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param string $body
     */
    public function setBody($body)
    {
        $this->body = $body;
    }
}
