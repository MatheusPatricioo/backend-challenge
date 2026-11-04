<?php

namespace Contatoseguro\TesteBackend\Config;

class DB
{
    private static ?\PDO $pdo = null;

    public static function connect()
    {
        $dbPath = getenv('DB_PATH') ?: dirname(__DIR__, 2) . '/db/db.sqlite3';

        if (self::$pdo === null) {
            self::$pdo = new \PDO('sqlite:' . $dbPath, null, null, [
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_OBJ
            ]);
        }

        return self::$pdo;
    }

    public static function disconnect(): void
    {
        self::$pdo = null;
    }
}
