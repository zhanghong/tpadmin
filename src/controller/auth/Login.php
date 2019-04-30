<?php

namespace tpadmin\controller\auth;

class Login extends Controller
{
    public function index()
    {
        return $this->fetch('index/index');
    }
}