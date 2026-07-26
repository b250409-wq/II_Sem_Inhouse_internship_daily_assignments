<?php
require_once __DIR__ . '/../config/database.php';

class Note {
    private PDO $db;
    public function __construct() { $this->db = Database::getInstance(); }

    public function forUser(int $userId, array $filters = []): array {
        $sql = 'SELECT n.* FROM notes n WHERE n.user_id = ? AND n.is_deleted = 0';
        $p = [$userId];
        if (!empty($filters['archived'])) $sql .= ' AND n.is_archived = 1';
        else $sql .= ' AND n.is_archived = 0';
        if (!empty($filters['favorite'])) $sql .= ' AND n.is_favorite = 1';
        if (!empty($filters['pinned']))   $sql .= ' AND n.is_pinned = 1';
        if (!empty($filters['q'])) {
            $sql .= ' AND (n.title LIKE ? OR n.content LIKE ?)';
            $q = '%' . $filters['q'] . '%';
            $p[] = $q; $p[] = $q;
        }
        if (!empty($filters['tag_id'])) {
            $sql .= ' AND n.id IN (SELECT note_id FROM note_tags WHERE tag_id = ?)';
            $p[] = (int)$filters['tag_id'];
        }
        $order = $filters['sort'] ?? 'newest';
        switch ($order) {
            case 'oldest': $sql .= ' ORDER BY n.is_pinned DESC, n.created_at ASC'; break;
            case 'alpha':  $sql .= ' ORDER BY n.is_pinned DESC, n.title ASC'; break;
            default:       $sql .= ' ORDER BY n.is_pinned DESC, n.created_at DESC';
        }
        $s = $this->db->prepare($sql);
        $s->execute($p);
        $notes = $s->fetchAll();
        foreach ($notes as &$n) $n['tags'] = $this->tagsFor((int)$n['id']);
        return $notes;
    }

    public function find(int $id, int $userId): ?array {
        $s = $this->db->prepare('SELECT * FROM notes WHERE id = ? AND user_id = ? LIMIT 1');
        $s->execute([$id, $userId]);
        $n = $s->fetch();
        if (!$n) return null;
        $n['tags'] = $this->tagsFor($id);
        return $n;
    }

    public function create(int $userId, string $title, string $content, array $tagIds = []): int {
        $s = $this->db->prepare('INSERT INTO notes (user_id, title, content) VALUES (?,?,?)');
        $s->execute([$userId, $title, $content]);
        $id = (int)$this->db->lastInsertId();
        $this->syncTags($id, $tagIds);
        return $id;
    }
    public function update(int $id, int $userId, string $title, string $content, array $tagIds = []): bool {
        $s = $this->db->prepare('UPDATE notes SET title=?, content=? WHERE id=? AND user_id=?');
        $s->execute([$title, $content, $id, $userId]);
        $this->syncTags($id, $tagIds);
        return true;
    }
    public function toggle(int $id, int $userId, string $field): bool {
        if (!in_array($field, ['is_pinned','is_favorite','is_archived'])) return false;
        $s = $this->db->prepare("UPDATE notes SET $field = 1 - $field WHERE id = ? AND user_id = ?");
        return $s->execute([$id, $userId]);
    }
    public function softDelete(int $id, int $userId): bool {
        $s = $this->db->prepare('UPDATE notes SET is_deleted = 1 WHERE id = ? AND user_id = ?');
        return $s->execute([$id, $userId]);
    }
    public function restore(int $id, int $userId): bool {
        $s = $this->db->prepare('UPDATE notes SET is_deleted = 0, is_archived = 0 WHERE id = ? AND user_id = ?');
        return $s->execute([$id, $userId]);
    }
    public function tagsFor(int $noteId): array {
        $s = $this->db->prepare('SELECT t.* FROM tags t JOIN note_tags nt ON nt.tag_id = t.id WHERE nt.note_id = ?');
        $s->execute([$noteId]);
        return $s->fetchAll();
    }
    public function syncTags(int $noteId, array $tagIds): void {
        $this->db->prepare('DELETE FROM note_tags WHERE note_id = ?')->execute([$noteId]);
        if (!$tagIds) return;
        $ins = $this->db->prepare('INSERT IGNORE INTO note_tags (note_id, tag_id) VALUES (?,?)');
        foreach ($tagIds as $tid) $ins->execute([$noteId, (int)$tid]);
    }
    public function stats(int $userId): array {
        $q = fn($sql) => (int)$this->db->prepare($sql)->execute([$userId]) === true ? 0 : 0;
        $stmt = function(string $sql) use ($userId) {
            $s = $this->db->prepare($sql); $s->execute([$userId]); return (int)$s->fetchColumn();
        };
        return [
            'total'    => $stmt('SELECT COUNT(*) FROM notes WHERE user_id = ? AND is_deleted = 0'),
            'favorite' => $stmt('SELECT COUNT(*) FROM notes WHERE user_id = ? AND is_favorite = 1 AND is_deleted = 0'),
            'archived' => $stmt('SELECT COUNT(*) FROM notes WHERE user_id = ? AND is_archived = 1 AND is_deleted = 0'),
            'pinned'   => $stmt('SELECT COUNT(*) FROM notes WHERE user_id = ? AND is_pinned = 1 AND is_deleted = 0'),
            'tags'     => $stmt('SELECT COUNT(*) FROM tags WHERE user_id = ?'),
        ];
    }
}
