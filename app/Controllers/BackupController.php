<?php

namespace App\Controllers;

class BackupController extends BaseController
{
    private $db;

    public function __construct()
    {
        $this->db = db_connect();
    }

    public function index()
    {
        if (! $this->isLoggedIn()) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        if ($redirect = $this->requireAdministrator('/admin/dashboard')) {
            return $redirect;
        }

        return view('admin/backup/index', [
            'databaseName' => $this->db->database,
            'tableCount'   => count($this->db->listTables()),
            'userName'     => session()->get('userName'),
            'userEmail'    => session()->get('userEmail'),
            'userRole'     => session()->get('userRole'),
        ]);
    }

    public function download()
    {
        if (! $this->isLoggedIn()) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        if ($redirect = $this->requireAdministrator('/admin/dashboard')) {
            return $redirect;
        }

        $fileName = sprintf(
            'backup-%s-%s.sql',
            preg_replace('/[^A-Za-z0-9_-]/', '_', (string) $this->db->database),
            date('Ymd-His')
        );

        return $this->response
            ->setHeader('Content-Type', 'application/sql; charset=UTF-8')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $fileName . '"')
            ->setBody($this->buildSqlDump());
    }

    private function buildSqlDump(): string
    {
        $database = (string) $this->db->database;
        $tables = $this->db->listTables();
        $lines = [
            '-- Backup database ' . $database,
            '-- Dibuat pada ' . date('Y-m-d H:i:s'),
            '-- Backup aplikasi',
            '',
            'SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";',
            'SET time_zone = "+00:00";',
            'SET FOREIGN_KEY_CHECKS = 0;',
            '',
            'CREATE DATABASE IF NOT EXISTS ' . $this->quoteIdentifier($database) . ' DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;',
            'USE ' . $this->quoteIdentifier($database) . ';',
            '',
        ];

        foreach ($tables as $table) {
            $lines[] = '-- --------------------------------------------------------';
            $lines[] = '-- Struktur tabel ' . $table;
            $lines[] = '';
            $lines[] = 'DROP TABLE IF EXISTS ' . $this->quoteIdentifier($table) . ';';
            $lines[] = $this->createTableSql($table) . ';';
            $lines[] = '';

            $rows = $this->db->table($table)->get()->getResultArray();

            if ($rows === []) {
                continue;
            }

            $columns = array_keys($rows[0]);
            $columnSql = implode(', ', array_map([$this, 'quoteIdentifier'], $columns));

            $lines[] = '-- Data tabel ' . $table;

            foreach ($rows as $row) {
                $values = [];

                foreach ($columns as $column) {
                    $values[] = $this->sqlValue($row[$column] ?? null);
                }

                $lines[] = 'INSERT INTO ' . $this->quoteIdentifier($table) . ' (' . $columnSql . ') VALUES (' . implode(', ', $values) . ');';
            }

            $lines[] = '';
        }

        $lines[] = 'SET FOREIGN_KEY_CHECKS = 1;';
        $lines[] = '';

        return implode("\n", $lines);
    }

    private function createTableSql(string $table): string
    {
        $row = $this->db->query('SHOW CREATE TABLE ' . $this->quoteIdentifier($table))->getRowArray();

        return (string) ($row['Create Table'] ?? array_values($row)[1] ?? '');
    }

    private function sqlValue($value): string
    {
        if ($value === null) {
            return 'NULL';
        }

        return (string) $this->db->escape($value);
    }

    private function quoteIdentifier(string $identifier): string
    {
        return '`' . str_replace('`', '``', $identifier) . '`';
    }

    private function isLoggedIn(): bool
    {
        return (bool) session()->get('isLoggedIn');
    }
}
