<?php
// models/BaseModel.php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/helpers.php';

abstract class BaseModel {
    protected static function db(): PDO {
        return getDB();
    }
}
