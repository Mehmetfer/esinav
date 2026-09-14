<?php
declare(strict_types=1);
$file = __DIR__ . '/../data/soru-havuzu.php';
$bank = file_exists($file) ? (array)(require $file) : [];

// Test sorusunu ekleyelim
$bank[] = [
    'ders' => 'trafik',
    'soru' => 'Trafik kazası gördüğünde gerekli müdahale ve yardımları yapmayan sürücüye aşağıdakilerden hangisi uygulanır?',
    'a' => 'Sürücü belgesinin süresiz geri alınması',
    'b' => 'Ağır hapis cezası',
    'c' => 'Para ve ceza puanı',
    'd' => 'Trafikten men',
    'dogru' => 'A',
    'aktif' => 1,
    'kaynak' => 'doc'
];

file_put_contents($file, "<?php\ndeclare(strict_types=1);\n\nreturn " . var_export($bank, true) . ";\n");
echo "Manuel test sorusu başarıyla eklendi! Toplam: " . count($bank) . "\n";
