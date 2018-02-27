<?php

use ChatFuel\Chatfuel;
use ChatFuel\ChatfuelException;
use PHPUnit\Framework\TestCase;

class ChatfuelTest extends TestCase
{
    public function testObjectIsInstanceOfChatfuel()
    {
        $chatfuel = new Chatfuel();
        $this->assertTrue($chatfuel instanceof Chatfuel);
        unset($chatfuel);
    }

    public function testExceptionIsInstanceOfOpenGraphException()
    {
        $this->expectException(ChatfuelException::class);
        new Chatfuel();
    }
}