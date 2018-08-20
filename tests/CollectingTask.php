<?php
declare(strict_types=1);

namespace Fig\EventDispatcher;


use Psr\EventDispatcher\TaskInterface;

class CollectingTask implements TaskInterface
{
    protected $out = [];

    public function add(string $val) : void
    {
        $this->out[] = $val;
    }

    public function result() : array
    {
        return $this->out;
    }

}