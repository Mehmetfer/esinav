<?php
declare(strict_types=1);

/**
 * Soru Yönetimi
 * Ehliyet + SRC için soru havuzu
 */

require_once dirname(__DIR__) . '/config.php';
require_once __DIR__ . '/db.php';

/**
 * Soru ekle
 */
function question_create(array $data): int {
    $pdo = db();
    $st = $pdo->prepare("
        INSERT INTO questions (group_id, category_id, topic_id, question_text, option_a, option_b, option_c, option_d, correct_answer, explanation, image, difficulty, source)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $st->execute([
        $data['group_id'],
        $data['category_id'] ?? null,
        $data['topic_id'] ?? null,
        $data['question_text'],
        $data['option_a'],
        $data['option_b'],
        $data['option_c'],
        $data['option_d'],
        $data['correct_answer'],
        $data['explanation'] ?? null,
        $data['image'] ?? null,
        $data['difficulty'] ?? 'orta',
        $data['source'] ?? null,
    ]);
    return (int)$pdo->lastInsertId();
}

/**
 * Soru güncelle
 */
function question_update(int $id, array $data): bool {
    $pdo = db();
    $st = $pdo->prepare("
        UPDATE questions SET 
            group_id = ?, category_id = ?, topic_id = ?, question_text = ?,
            option_a = ?, option_b = ?, option_c = ?, option_d = ?,
            correct_answer = ?, explanation = ?, image = ?, difficulty = ?, source = ?
        WHERE id = ?
    ");
    return $st->execute([
        $data['group_id'],
        $data['category_id'] ?? null,
        $data['topic_id'] ?? null,
        $data['question_text'],
        $data['option_a'],
        $data['option_b'],
        $data['option_c'],
        $data['option_d'],
        $data['correct_answer'],
        $data['explanation'] ?? null,
        $data['image'] ?? null,
        $data['difficulty'] ?? 'orta',
        $data['source'] ?? null,
        $id,
    ]);
}

/**
 * Soru sil
 */
function question_delete(int $id): bool {
    $pdo = db();
    $st = $pdo->prepare("DELETE FROM questions WHERE id = ?");
    return $st->execute([$id]);
}

/**
 * Soru durumunu değiştir (aktif/pasif)
 */
function question_toggle_active(int $id): bool {
    $pdo = db();
    $st = $pdo->prepare("UPDATE questions SET aktif = NOT aktif WHERE id = ?");
    return $st->execute([$id]);
}

/**
 * ID'ye göre soru getir
 */
function question_by_id(int $id): ?array {
    $pdo = db();
    $st = $pdo->prepare("SELECT * FROM questions WHERE id = ? LIMIT 1");
    $st->execute([$id]);
    $row = $st->fetch();
    return $row ?: null;
}

/**
 * Soruları listele (filtreleme ile)
 */
function questions_list(array $filters = [], int $page = 1, int $per_page = 20): array {
    $pdo = db();
    
    $where = ["1=1"];
    $params = [];
    
    if (!empty($filters['group_id'])) {
        $where[] = "q.group_id = ?";
        $params[] = $filters['group_id'];
    }
    if (!empty($filters['category_id'])) {
        $where[] = "q.category_id = ?";
        $params[] = $filters['category_id'];
    }
    if (!empty($filters['topic_id'])) {
        $where[] = "q.topic_id = ?";
        $params[] = $filters['topic_id'];
    }
    if (!empty($filters['difficulty'])) {
        $where[] = "q.difficulty = ?";
        $params[] = $filters['difficulty'];
    }
    if (isset($filters['aktif'])) {
        $where[] = "q.aktif = ?";
        $params[] = $filters['aktif'] ? 1 : 0;
    }
    if (!empty($filters['search'])) {
        $where[] = "(q.question_text LIKE ? OR q.explanation LIKE ?)";
        $search = '%' . $filters['search'] . '%';
        $params[] = $search;
        $params[] = $search;
    }
    
    $where_sql = implode(' AND ', $where);
    
    // Toplam sayı
    $count_sql = "SELECT COUNT(*) FROM questions q WHERE {$where_sql}";
    $st = $pdo->prepare($count_sql);
    $st->execute($params);
    $total = (int)$st->fetchColumn();
    
    // Sayfalama
    $offset = ($page - 1) * $per_page;
    $sql = "SELECT q.*, eg.name as group_name, c.name as category_name, t.name as topic_name
            FROM questions q
            LEFT JOIN education_groups eg ON eg.id = q.group_id
            LEFT JOIN categories c ON c.id = q.category_id
            LEFT JOIN topics t ON t.id = q.topic_id
            WHERE {$where_sql}
            ORDER BY q.id DESC
            LIMIT {$per_page} OFFSET {$offset}";
    
    $st = $pdo->prepare($sql);
    $st->execute($params);
    $items = $st->fetchAll();
    
    return [
        'items' => $items,
        'total' => $total,
        'page' => $page,
        'per_page' => $per_page,
        'total_pages' => ceil($total / $per_page),
    ];
}

/**
 * Sınav için rastgele soru seç
 */
function questions_random_for_exam(int $group_id, ?int $category_id, ?int $topic_id, int $count): array {
    $pdo = db();
    $where = ["group_id = ? AND aktif = 1"];
    $params = [$group_id];
    
    if ($category_id !== null) {
        $where[] = "category_id = ?";
        $params[] = $category_id;
    }
    if ($topic_id !== null) {
        $where[] = "topic_id = ?";
        $params[] = $topic_id;
    }
    
    $where_sql = implode(' AND ', $where);
    $sql = "SELECT * FROM questions WHERE {$where_sql} ORDER BY RAND() LIMIT {$count}";
    $st = $pdo->prepare($sql);
    $st->execute($params);
    return $st->fetchAll();
}

/**
 * CSV formatından soru içe aktar
 */
function questions_import_csv(string $csv_content, int $group_id, ?int $category_id = null, ?int $topic_id = null): array {
    $lines = explode("\n", $csv_content);
    $imported = 0;
    $errors = [];
    
    foreach ($lines as $i => $line) {
        $line = trim($line);
        if ($line === '') continue;
        
        $parts = str_getcsv($line, '|');
        if (count($parts) < 6) {
            $errors[] = "Satır " . ($i + 1) . ": Eksik alan";
            continue;
        }
        
        $correct = strtoupper(trim($parts[5] ?? 'A'));
        if (!in_array($correct, ['A', 'B', 'C', 'D'])) $correct = 'A';
        
        try {
            question_create([
                'group_id' => $group_id,
                'category_id' => $category_id,
                'topic_id' => $topic_id,
                'question_text' => trim($parts[0]),
                'option_a' => trim($parts[1]),
                'option_b' => trim($parts[2]),
                'option_c' => trim($parts[3]),
                'option_d' => trim($parts[4]),
                'correct_answer' => $correct,
                'difficulty' => 'orta',
                'source' => 'csv',
            ]);
            $imported++;
        } catch (Throwable $e) {
            $errors[] = "Satır " . ($i + 1) . ": " . $e->getMessage();
        }
    }
    
    return ['imported' => $imported, 'errors' => $errors];
}
