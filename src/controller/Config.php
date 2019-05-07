<?php

namespace tpadmin\controller;

use think\Request;
use tpadmin\model\Config as ConfigModel;

class Index extends Controller
{
    public function index()
    {
        return $this->fetch('index/index');
    }
}