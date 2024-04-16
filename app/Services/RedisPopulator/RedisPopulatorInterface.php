<?php

namespace App\Services\RedisPopulator;

interface RedisPopulatorInterface
{
    public function populate(): void;
}
