<?php

namespace Tests\PHPCtx\Ctx\Service\Example\Child;

use Tests\PHPCtx\Ctx\Basic\Ctx as BasicCtx;

class Demo extends BasicCtx
{
    protected $log = "";

    /**
     * @var string
     */
    private $prefix;

    /**
     * @var string
     */
    private $middle;

    public function __construct($prefix)
    {
        $this->prefix = $prefix;
    }

    public function init()
    {
        $this->middle = ' ';
    }

    public function setMessage($name)
    {
        $this->log = $this->prefix . $this->middle . $name;

        return true;
    }

    public function getMessage()
    {
        return $this->log;
    }
}
