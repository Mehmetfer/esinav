<?php
require_once __DIR__ . '/../includes/auth.php';

$tur = isset($_GET['tur']) ? strtolower($_GET['tur']) : 'src1';
$grup = isset($_GET['grup']) ? $_GET['grup'] : 'src';
$kID = isset($_GET['kID']) ? (int)$_GET['kID'] : 0;
$tur_baslik = strtoupper($tur);

$sinavlar = [
    'orta' => [
        'baslik' => '01. Orta Seviye Sınavlar',
        'ico'    => 'fa-chart-line',
        'not'    => 'Orta zorlukta, hem kavrama hem detay kontrolü gerektiren sorular.',
        'sayilar' => range(1, 31)
    ],
    'zor' => [
        'baslik' => '02. Zor Seviye Sınavlar',
        'ico'    => 'fa-tachometer-alt',
        'not'    => 'Zorluk seviyesi yüksek; çoklu kavram birleştiren ve yorucu süreyi simüle eden sorular.',
        'sayilar' => range(1, 8)
    ],
    'kolay' => [
        'baslik' => '03. Kolay Seviye Sınavlar',
        'ico'    => 'fa-smile',
        'not'    => 'Temel kavram ve tanıma odaklı, hızlı çözülebilen sorular.',
        'sayilar' => range(1, 13)
    ]
];

$toplam_soru = array_sum(array_map(function($g){ return count($g['sayilar']); }, $sinavlar));

$top_path = __DIR__ . '/_layout_top.php';
if (file_exists($top_path)) { include_once $top_path; }
?>

<div style="margin-bottom: 15px;">
    <a href="/kursiyer/src-cikmis-sorular.php?tur=<?php echo urlencode($tur); ?>&grup=<?php echo urlencode($grup); ?>" style="text-decoration: none; color: #2b6cb0; font-size: 14px; font-weight: bold; display: inline-flex; align-items: center; gap: 6px;">
        <i class="fas fa-arrow-left"></i> Çıkmış Sınavlar Listesine Dön
    </a>
</div>

<?php if ($kID > 0): ?>
    <style>
        .esinav-wrapper { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; font-family: system-ui, -apple-system, sans-serif; }
        .esinav-header { display: flex; justify-content: space-between; align-items: center; background: #1e293b; color: #fff; padding: 15px 20px; border-radius: 8px; margin-bottom: 20px; }
        .esinav-timer { background: #e11d48; color: #fff; padding: 6px 14px; border-radius: 20px; font-weight: bold; font-size: 14px; display: flex; align-items: center; gap: 8px; }
        .esinav-grid { display: grid; grid-template-columns: 1fr 300px; gap: 20px; }
        @media(max-width: 900px){ .esinav-grid { grid-template-columns: 1fr; } }
        .esinav-card { background: #fff; border: 1px solid #cbd5e1; border-radius: 8px; padding: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        .esinav-option { display: flex; align-items: center; padding: 12px 16px; border: 2px solid #e2e8f0; border-radius: 8px; margin-bottom: 10px; cursor: pointer; transition: all 0.2s; font-size: 15px; background: #fff; }
        .esinav-option:hover { border-color: #3b82f6; background: #eff6ff; }
        .esinav-option.selected { border-color: #2563eb; background: #dbeafe; font-weight: 600; }
        .esinav-option.correct { border-color: #16a34a; background: #dcfce7; color: #14532d; font-weight: 600; }
        .esinav-option.wrong { border-color: #dc2626; background: #fee2e2; color: #7f1d1d; }
        .opt-letter { width: 28px; height: 28px; border-radius: 50%; background: #e2e8f0; display: flex; align-items: center; justify-content: center; font-weight: bold; margin-right: 12px; flex-shrink: 0; }
        .esinav-option.selected .opt-letter { background: #2563eb; color: #fff; }
        .esinav-option.correct .opt-letter { background: #16a34a; color: #fff; }
        .esinav-option.wrong .opt-letter { background: #dc2626; color: #fff; }
        .num-box { width: 36px; height: 36px; border: 1px solid #cbd5e1; background: #fff; border-radius: 6px; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 13px; cursor: pointer; transition: all 0.15s; }
        .num-box:hover { border-color: #2563eb; color: #2563eb; }
        .num-box.active { background: #2563eb; color: #fff; border-color: #2563eb; }
        .num-box.answered { background: #e2e8f0; color: #334155; }
        .esinav-btn { padding: 10px 20px; border-radius: 6px; font-weight: 600; cursor: pointer; border: none; display: inline-flex; align-items: center; gap: 8px; font-size: 14px; transition: opacity 0.2s; }
        .esinav-btn:hover { opacity: 0.9; }
        .btn-primary { background: #2563eb; color: #fff; }
        .btn-success { background: #16a34a; color: #fff; }
        .btn-secondary { background: #64748b; color: #fff; }
    </style>

    <div class="esinav-wrapper">
        <div class="esinav-header">
            <div>
                <h3 style="margin:0; font-size: 18px; color:#fff;">SRC e-Sınav Simülatörü</h3>
                <span style="font-size: 13px; color: #94a3b8;"><?php echo htmlspecialchars($tur_baslik); ?> - Test <?php echo $kID; ?> (40 Soru / 50 Dakika)</span>
            </div>
            <div class="esinav-timer">
                <i class="fas fa-clock"></i> <span id="timer-display">50:00</span>
            </div>
        </div>

        <div class="esinav-grid">
            <div>
                <div class="esinav-card">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 15px; border-bottom: 1px solid #f1f5f9; padding-bottom: 10px;">
                        <span style="font-weight:bold; color:#0f172a;" id="question-number-title">Soru 1 / 40</span>
                        <span style="font-size:12px; background:#f1f5f9; padding:4px 8px; border-radius:4px; color:#475569;">Puan Değeri: 2.5</span>
                    </div>

                    <div id="question-text" style="font-size: 16px; color: #1e293b; line-height: 1.6; font-weight: 500; margin-bottom: 20px;">
                        <!-- Soru Metni -->
                    </div>

                    <div id="options-container">
                        <!-- Şıklar -->
                    </div>

                    <div id="explanation-box" style="display:none; margin-top:15px; padding:12px 15px; background:#f0fdf4; border-left:4px solid #16a34a; border-radius:4px; font-size:13px; color:#166534;">
                        <strong><i class="fas fa-info-circle"></i> Çözüm Açıklaması:</strong>
                        <div id="explanation-text" style="margin-top:4px;"></div>
                    </div>
                </div>

                <div style="display:flex; justify-content:space-between; margin-top: 15px;">
                    <button class="esinav-btn btn-secondary" onclick="prevQuestion()"><i class="fas fa-chevron-left"></i> Önceki Soru</button>
                    <button class="esinav-btn btn-success" onclick="checkAnswer()"><i class="fas fa-check"></i> Cevabı Kontrol Et</button>
                    <button class="esinav-btn btn-primary" onclick="nextQuestion()">Sonraki Soru <i class="fas fa-chevron-right"></i></button>
                </div>
            </div>

            <div>
                <div class="esinav-card">
                    <h4 style="margin-top:0; margin-bottom: 12px; color:#0f172a; font-size:15px; border-bottom:1px solid #f1f5f9; padding-bottom:8px;">Soru Navigasyonu</h4>
                    <div style="display:grid; grid-template-columns: repeat(5, 1fr); gap: 8px;" id="question-palette">
                        <!-- Soru Butonları -->
                    </div>

                    <div style="margin-top: 20px; border-top: 1px solid #f1f5f9; padding-top: 15px;">
                        <button class="esinav-btn btn-primary" style="width: 100%; justify-content: center;" onclick="finishExam()">
                            <i class="fas fa-flag-checkered"></i> Sınavı Bitir
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const questions = [
            {
                id: 1,
                question: '“DOT DB9Z 747R 4818”<br><br>Araç lastikleri üzerinde bulunan yukarıdaki tanıtıcı ifadelerden hangisi lastiğin üretim tarihi ile ilgili bilgi verir?',
                options: [
                    { key: 'A', text: 'DOT' },
                    { key: 'B', text: 'DB9Z' },
                    { key: 'C', text: '747R' },
                    { key: 'D', text: '4818' }
                ],
                correct: 'D',
                explanation: 'Lastik üzerindeki 4 haneli sayı üretim tarihini belirtir. İlk iki rakam üretim haftasını (48. Hafta), son iki rakam ise üretim yılını (2018) gösterir.'
            }
        ];

        // Toplam 40 Soruya Tamamlama
        for(let i = 2; i <= 40; i++) {
            questions.push({
                id: i,
                question: 'Bu soru henüz eklenmemiştir. (Soru ' + i + ')',
                options: [
                    { key: 'A', text: 'Seçenek A' },
                    { key: 'B', text: 'Seçenek B' },
                    { key: 'C', text: 'Seçenek C' },
                    { key: 'D', text: 'Seçenek D' }
                ],
                correct: 'A',
                explanation: 'Soru henüz sisteme aktarılmamıştır.'
            });
        }

        let currentIdx = 0;
        let userAnswers = {};
        let checkedStatus = {};

        function renderPalette() {
            const palette = document.getElementById('question-palette');
            palette.innerHTML = '';
            questions.forEach((q, idx) => {
                const btn = document.createElement('div');
                btn.className = 'num-box' + (idx === currentIdx ? ' active' : (userAnswers[idx] ? ' answered' : ''));
                btn.innerText = q.id;
                btn.onclick = () => { currentIdx = idx; loadQuestion(); };
                palette.appendChild(btn);
            });
        }

        function loadQuestion() {
            const q = questions[currentIdx];
            document.getElementById('question-number-title').innerText = 'Soru ' + q.id + ' / ' + questions.length;
            document.getElementById('question-text').innerHTML = q.question;
            
            const optContainer = document.getElementById('options-container');
            optContainer.innerHTML = '';

            const expBox = document.getElementById('explanation-box');
            if (checkedStatus[currentIdx]) {
                expBox.style.display = 'block';
                document.getElementById('explanation-text').innerText = q.explanation;
            } else {
                expBox.style.display = 'none';
            }

            q.options.forEach(opt => {
                const optDiv = document.createElement('div');
                let className = 'esinav-option';
                
                if (checkedStatus[currentIdx]) {
                    if (opt.key === q.correct) className += ' correct';
                    else if (userAnswers[currentIdx] === opt.key) className += ' wrong';
                } else if (userAnswers[currentIdx] === opt.key) {
                    className += ' selected';
                }

                optDiv.className = className;
                optDiv.innerHTML = `<div class="opt-letter">${opt.key}</div><div>${opt.text}</div>`;
                optDiv.onclick = () => {
                    if (!checkedStatus[currentIdx]) {
                        userAnswers[currentIdx] = opt.key;
                        loadQuestion();
                        renderPalette();
                    }
                };
                optContainer.appendChild(optDiv);
            });

            renderPalette();
        }

        function checkAnswer() {
            if (!userAnswers[currentIdx]) {
                alert('Lütfen önce bir şık seçiniz!');
                return;
            }
            checkedStatus[currentIdx] = true;
            loadQuestion();
        }

        function nextQuestion() {
            if (currentIdx < questions.length - 1) {
                currentIdx++;
                loadQuestion();
            }
        }

        function prevQuestion() {
            if (currentIdx > 0) {
                currentIdx--;
                loadQuestion();
            }
        }

        function finishExam() {
            alert('Sınavınız tamamlanmıştır. Doğru ve yanlış cevaplarınızı kontrol edebilirsiniz.');
        }

        // 50 Dakika Zamanlayıcı (3000 Saniye)
        let duration = 50 * 60;
        const timerDisplay = document.getElementById('timer-display');
        setInterval(() => {
            let minutes = parseInt(duration / 60, 10);
            let seconds = parseInt(duration % 60, 10);
            minutes = minutes < 10 ? "0" + minutes : minutes;
            seconds = seconds < 10 ? "0" + seconds : seconds;
            timerDisplay.textContent = minutes + ":" + seconds;
            if (--duration < 0) duration = 0;
        }, 1000);

        loadQuestion();
    </script>

<?php else: ?>
    <section class="k-card">
        <h1 class="k-page-title">SRC Çıkmış Sınav Soruları (<?php echo htmlspecialchars($tur_baslik); ?>)</h1>
        <div style="margin-bottom: 15px; font-weight: bold; color: #555;">
            <i class="fas fa-file-alt"></i> Sınav Formatında Çözebileceğiniz Testler (40 Soru / 50 Dk)
        </div>

        <?php foreach ($sinavlar as $sk => $sg): ?>
            <div style="margin-bottom: 20px;">
                <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 10px;">
                    <strong style="font-size: 16px; color: #2d3748;">
                        <i class="fas <?php echo htmlspecialchars($sg['ico']); ?>" style="margin-right: 8px; color: #3182ce;"></i>
                        <?php echo htmlspecialchars($sg['baslik']); ?>
                        <span style="color: #718096; font-weight: normal; font-size: 13px;">(<?php echo count($sg['sayilar']); ?> test)</span>
                    </strong>
                </div>
                <p style="margin: 0 0 10px; font-size: 13px; color: #718096; font-style: italic;">
                    <?php echo htmlspecialchars($sg['not']); ?>
                </p>

                <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                    <?php foreach ($sg['sayilar'] as $sn): ?>
                        <a href="/kursiyer/src-cikmis-sorular.php?tur=<?php echo urlencode($tur); ?>&grup=<?php echo urlencode($grup); ?>&kID=<?php echo $sn; ?>"
                           style="background: #2563eb; color: #fff; padding: 6px 14px; border-radius: 5px; text-decoration: none; font-size: 14px; font-weight: 600; display: inline-flex; align-items: center; gap: 6px;">
                            <i class="fas fa-laptop-code" style="font-size: 12px;"></i>
                            Test <?php echo $sn; ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </section>
<?php endif; ?>

<?php
$bottom_path = __DIR__ . '/_layout_bottom.php';
if (file_exists($bottom_path)) { include_once $bottom_path; }
?>