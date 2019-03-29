<?php

namespace tpadmin\controller;

class Index extends Controller
{
    public function index()
    {
        return $this->fetch('index/index');
    }
}