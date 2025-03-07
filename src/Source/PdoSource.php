<?php

namespace Goteo\Benzina\Source;

class PdoSource implements SourceInterface
{
    private \PDOStatement $countStmt;

    private \PDOStatement $selectStmt;

    public function __construct(
        string $database,
        private string $tablename,
        private int $offset = 0,
    ) {
        $parsedUrl = parse_url($database);
        $dbdata = [
            'name' => ltrim($parsedUrl['path'], '/'),
            ...$parsedUrl,
        ];

        $pdo = new \PDO(
            dsn: sprintf('%s:host=%s;dbname=%s', $dbdata['scheme'], $dbdata['host'], $dbdata['name']),
            username: $dbdata['user'],
            password: $dbdata['pass'],
            options: [
                \PDO::ATTR_EMULATE_PREPARES => false,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            ]
        );

        $this->countStmt = $pdo->prepare(
            "SELECT COUNT(*) FROM (SELECT * FROM `$tablename` LIMIT 18446744073709551615 OFFSET $offset) AS count"
        );

        $this->selectStmt = $pdo->prepare(
            "SELECT * FROM `$tablename` OFFSET $offset ROWS",
            [\PDO::ATTR_CURSOR => \PDO::CURSOR_SCROLL],
        );
    }

    public function records(): \Traversable
    {
        $this->selectStmt->execute();

        return $this->selectStmt;
    }

    public function sample(): mixed
    {
        $this->selectStmt->execute();

        return $this->selectStmt->fetch();
    }

    public function size(): int
    {
        $this->countStmt->execute();

        return $this->countStmt->fetchColumn();
    }
}
