<?php

namespace Seba\API;

/**
 * SQLite Database class.
 * This class provides a simple interface for interacting with SQLite databases.
 *
 * @author Sebastiano Racca <sebastiano@racca.me>
 * @package Seba\API
 */
class Database
{
    private static array $_databases = [];

    private \PDO $pdo;

    /**
     * Constructor for Database class.
     *
     * @param string $path Path to the SQLite database file.
     * @param string|null $strucure Optional path to the file containing SQL structure.
     */
    public function __construct(string $path, ?string $strucure = null)
    {
        self::$_databases[$path] = $this;

        if(!file_exists($path)) {
            touch($path);
        }

        $this->pdo = new \PDO("sqlite:$path");

        if($strucure !== null) {
            $this->init($strucure);
        }

    }

    /**
     * Get an instance of the Database class.
     *
     * @param string $path Path to the SQLite database file.
     * @param string|null $strucure Optional path to the file containing SQL structure.
     * @return static The instance.
     */
    public static function getInstance(string $path, ?string $strucure = null): static
    {
        if (!isset(self::$_databases[$path])) {
            self::$_databases[$path] = new static($path, $strucure);
        }

        return self::$_databases[$path];
    }

    /**
     * Create tables and structures in the database.
     *
     * @param string $pathToStructure Path to the file containing SQL structure.
     * @return array Array of objects representing execution results for each query.
     */
    public function init(string $pathToStructure): array
    {
        $queries = explode(';', strtr(file_get_contents($pathToStructure), "\n", ""));
        $results = [];

        foreach ($queries as $query) {
            if (trim($query) === '') {
                continue;
            }

            $res = $this->query(function (\PDO $pdo) use ($query) {
                $stmt = $pdo->prepare($query);
                $stmt->execute();
                return (object)['ok' => true];
            });

            $results[] = (object)['ok' => $res->ok, 'query' => $query, 'error' => $res->error ?? null];
        }

        return $results;
    }

    /**
     * Perform a database query using a callback function.
     *
     * @param callable|object $fn Callback function for executing the query.
     * @return mixed Result of the query execution.
     */
    public function query(callable|object $fn): mixed
    {
        try {
            return $fn($this->pdo);
        } catch (\PDOException $e) {
            return (object)['ok' => false, 'error' => $e->getMessage()];
        }
    }
}
