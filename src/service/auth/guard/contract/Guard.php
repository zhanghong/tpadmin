<?php

namespace tpadmin\service\auth\guard\contract;

use tpadmin\service\auth\contract\Authenticate;

interface Guard
{
    public function login(Authenticate $authenticate);

    public function logout();

    public function getName();
}