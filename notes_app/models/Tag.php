<?php
require_once __DIR__ . '/../config/database.php';

class Tag {
    private PDO $db;
    public function __construct() { $this->db = Database::getInstance(); }

    public function forUser(int $userId): array {
        $s = $this->db->prepare('SELECT * FROM tags WHERE user_id = ? ORDER BY name ASC');
        $s->execute([$userId]);
        return $s->fetchAll();
    }
    public function create(int $userId, string $name, string $color): int {
        $s = $this->db->prepare('INSERT INTO tags (user_id, name, color) VALUES (?,?,?)');
        $s->execute([$userId, $name, $color]);
        return (int)$this->db->lastInsertId();
    }
    public function update(int $id, int $userId, string $name, string $color): bool {
        $s = $this->db->prepare('UPDATE tags SET name=?, color=? WHERE id=? AND user_id=?');
        return $s->execute([$name, $color, $id, $userId]);
    }
    public function delete(int $id, int $userId): bool {
        $s = $this->db->prepare('DELETE FROM tags WHERE id=? AND user_id=?');
        return $s->execute([$id, $userId]);
    }
}
