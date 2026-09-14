<?php
declare(strict_types=1);

function education_groups(bool $aktif_only = true): array {
    $pdo = db();
    $sql = "SELECT * FROM education_groups";
    if ($aktif_only) $sql .= " WHERE aktif = 1";
    $sql .= " ORDER BY sort_order, id";
    return $pdo->query($sql)->fetchAll();
}

function education_group_by_slug(string $slug): ?array {
    $pdo = db();
    $st = $pdo->prepare("SELECT * FROM education_groups WHERE slug = ? AND aktif = 1 LIMIT 1");
    $st->execute([$slug]);
    $row = $st->fetch();
    return $row ?: null;
}

function education_group_by_id(int $id): ?array {
    $pdo = db();
    $st = $pdo->prepare("SELECT * FROM education_groups WHERE id = ? AND aktif = 1 LIMIT 1");
    $st->execute([$id]);
    $row = $st->fetch();
    return $row ?: null;
}

function education_categories(int $group_id, ?int $parent_id = null, bool $aktif_only = true): array {
    $pdo = db();
    $sql = "SELECT * FROM categories WHERE group_id = ?";
    $params = [$group_id];
    if ($parent_id !== null) {
        $sql .= " AND parent_id = ?";
        $params[] = $parent_id;
    } else {
        $sql .= " AND parent_id IS NULL";
    }
    if ($aktif_only) $sql .= " AND aktif = 1";
    $sql .= " ORDER BY sort_order, id";
    $st = $pdo->prepare($sql);
    $st->execute($params);
    return $st->fetchAll();
}

function user_education_groups(int $user_id): array {
    $pdo = db();
    $st = $pdo->prepare(
        "SELECT eg.* FROM education_groups eg
         INNER JOIN user_groups ug ON ug.group_id = eg.id
         WHERE ug.user_id = ? AND eg.aktif = 1
         ORDER BY eg.sort_order"
    );
    $st->execute([$user_id]);
    return $st->fetchAll();
}

function user_assign_group(int $user_id, int $group_id): bool {
    $pdo = db();
    $st = $pdo->prepare("INSERT IGNORE INTO user_groups (user_id, group_id) VALUES (?, ?)");
    return $st->execute([$user_id, $group_id]);
}

function user_has_group(int $user_id, int $group_id): bool {
    $pdo = db();
    $st = $pdo->prepare("SELECT 1 FROM user_groups WHERE user_id = ? AND group_id = ? LIMIT 1");
    $st->execute([$user_id, $group_id]);
    return (bool)$st->fetch();
}

function user_has_group_slug(int $user_id, string $slug): bool {
    $pdo = db();
    $st = $pdo->prepare(
        "SELECT 1 FROM user_groups ug
         INNER JOIN education_groups eg ON eg.id = ug.group_id
         WHERE ug.user_id = ? AND eg.slug = ? AND eg.aktif = 1 LIMIT 1"
    );
    $st->execute([$user_id, $slug]);
    return (bool)$st->fetch();
}
