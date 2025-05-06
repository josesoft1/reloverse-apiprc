<?php

namespace App\Support\mailparser;

use eXorus\PhpMimeMailParser\Parser;

class EmlService
{
    protected $parser;

    public function __construct()
    {
        $this->parser = new Parser();
    }

    public function loadEmlFromString($content)
    {
        $this->parser->setText($content);
    }

    public function getHeader($name)
    {
        return $this->parser->getHeader($name);
    }

    public function getBody()
    {
        return $this->parser->getMessageBody('html');
    }

    public function getAttachments()
    {
        return $this->parser->getAttachments();
    }
}
