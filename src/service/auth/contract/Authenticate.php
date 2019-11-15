<?php

namespace tpadmin\service\auth\contract;

interface Authenticate
{
    public function retrieveByCredentials(array $credentials);

    public function getAuthIdentifier();

    public function retrieveByIdentifier($identifier);
}