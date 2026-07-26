<?php
require_once __DIR__ . '/../config/database.php';

class User {
    private PDO $db;
    public function __construct() { $this->db = Database::getInstance(); }

    public function findByEmail(string $email): ?array {
        $s = $this->db->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
        $s->execute([$email]);
        $r = $s->fetch();
        return $r ?: null;
    }
    public function findById(int $id): ?array {
        $s = $this->db->prepare('SELECT * FROM users WHERE id = ? LIMIT 1');
        $s->execute([$id]);
        $r = $s->fetch();
        return $r ?: null;
    }
    public function create(string $username, string $email, string $password): int {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $s = $this->db->prepare('INSERT INTO users (username, email, password) VALUES (?,?,?)');
        $s->execute([$username, $email, $hash]);
        return (int)$this->db->lastInsertId();
    }
    public function update(int $id, array $data): bool {
        $fields = []; $vals = [];
        foreach ($data as $k => $v) { $fields[] = "$k = ?"; $vals[] = $v; }
        $vals[] = $id;
        $s = $this->db->prepare('UPDATE users SET ' . implode(',', $fields) . ' WHERE id = ?');
        return $s->execute($vals);
    }
    public function updatePassword(int $id, string $newPassword): bool {
        $hash = password_hash($newPassword, PASSWORD_BCRYPT);
        return $this->update($id, ['password' => $hash]);
    }
    public function all(): array {
        return $this->db->query('SELECT id, username, email, role, created_at FROM users ORDER BY id DESC')->fetchAll();
    }
    public function delete(int $id): bool {
        $s = $this->db->prepare('DELETE FROM users WHERE id = ?');
        return $s->execute([$id]);
    }
}
