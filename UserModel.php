<?php

namespace AzizbekIsmoilov\phpmvc;

use AzizbekIsmoilov\phpmvc\db\DBModel;

abstract class UserModel extends DBModel
{
    abstract public function getDisplayName(): string;
}