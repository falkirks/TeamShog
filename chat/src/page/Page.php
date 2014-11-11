<?php
namespace shogchat\page;
abstract class Page{
    abstract public function render();
    abstract public function hasPermission();
}