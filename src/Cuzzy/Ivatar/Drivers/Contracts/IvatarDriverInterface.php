<?php

namespace Cuzzy\Ivatar\Drivers\Contracts;

interface IvatarDriverInterface
{
    public function stage();
    public function encode();
    public function save();
}
