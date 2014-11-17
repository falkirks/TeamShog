<?php
namespace shogchat\socket;

interface Client{
    public function isMemberOf($name);
    public function addChannel($name);
}