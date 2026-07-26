<?php
require_once __DIR__ . '/../config/database.php';
class Contact {
    private PDO $db;
    public function __construct() { $this->db = Database::getInstance(); }
    public function create(string $name, string $email, string $message): int {
        $s = $this->db->prepare('INSERT INTO contact_messages (name,email,message) VALUES (?,?,?)');
        $s->execute([$name, $email, $message]);
        return (int)$this->db->lastInsertId();
    }
    public function all(): array {
        return $this->db->query('SELECT * FROM contact_messages ORDER BY created_at DESC')->fetchAll();
    }
    public function delete(int $id): bool {
        $s = $this->db->prepare('DELETE FROM contact_messages WHERE id = ?');
        return $s->execute([$id]);
    }
}
