<?php

namespace App\Services\Jp;

class Client
{
    /**
     * @var string
     */
    private $password;

    /**
     * JpClient constructor.
     * @param string $password
     */
    public function __construct(string $password)
    {
        $this->password = $password;
    }

    public function run(): string
    {
        return 'Its running using password: ' . $this->password;
    }

}
