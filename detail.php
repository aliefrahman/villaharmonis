<?php
session_start();
require 'config/db.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = $_GET['id'];
$stmt = $conn->prepare("SELECT news.*, users.name as author_name, categories.name as category_name 
                        FROM news 
                        LEFT JOIN users ON news.author_id = users.id 
                        LEFT JOIN categories ON news.category_id = categories.id 
                        WHERE news.id = ? AND news.status = 'published'");
$stmt->execute([$id]);
$news = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$news) {
    header("Location: index.php");
    exit;
}

// Set dynamic title and additional head content for layout/header.php
$page_title = htmlspecialchars($news['title']) . ' - Berita.VHC';

// Prepare data for Open Graph (Social Media Share Preview)
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$base_url = $protocol . "://" . $_SERVER['HTTP_HOST'] . preg_replace('/\/detail\.php.*$/', '', $_SERVER['REQUEST_URI']);
$current_url = $protocol . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$image_url = $news['image'] ? $base_url . '/uploads/news/' . $news['image'] : '';
$short_desc = htmlspecialchars(mb_substr(strip_tags($news['content']), 0, 150) . '...');

$additional_head = '
    <!-- Primary Meta Tags -->
    <meta name="title" content="' . htmlspecialchars($news['title']) . '">
    <meta name="description" content="' . $short_desc . '">

    <!-- Open Graph / Facebook / WhatsApp -->
    <meta property="og:type" content="article">
    <meta property="og:url" content="' . $current_url . '">
    <meta property="og:title" content="' . htmlspecialchars($news['title']) . '">
    <meta property="og:description" content="' . $short_desc . '">
    ' . ($image_url ? '<meta property="og:image" itemprop="image" content="' . $image_url . '">' : '') . '

    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="' . $current_url . '">
    <meta name="twitter:title" content="' . htmlspecialchars($news['title']) . '">
    <meta name="twitter:description" content="' . $short_desc . '">
    ' . ($image_url ? '<meta name="twitter:image" content="' . $image_url . '">' : '') . '

    <link href="node_modules/quill/dist/quill.snow.css" rel="stylesheet">
    <style type="text/tailwindcss">
        @theme {
            --color-brand-300: #93c5fd;
            --color-brand-500: #2563eb;
            --color-brand-600: #1d4ed8;
            --color-brand-700: #1e40af;
            --color-brand-800: #1e3a8a;
        }
        
        .news-content .ql-editor {
            font-size: 1.1875rem;
            line-height: 1.85;
            color: #334155;
            letter-spacing: -0.01em;
            font-family: inherit;
            padding: 0;
        }
        
        .news-content .ql-editor p {
            margin-bottom: 1.75rem;
            text-align: left;
            color: #334155;
        }
        
        /* Drop cap for the first paragraph */
        .news-content .ql-editor > p:first-of-type::first-letter {
            font-size: 4.5rem;
            font-weight: 900;
            color: var(--color-brand-600);
            float: left;
            margin-right: 0.75rem;
            line-height: 0.4;
            margin-top: 0.25rem;
        }

        .news-content .ql-editor h2, 
        .news-content .ql-editor h3, 
        .news-content .ql-editor h4 {
            font-weight: 800;
            color: #0f172a;
            margin-top: 2rem;
            margin-bottom: 1.25rem;
            line-height: 1.3;
            letter-spacing: -0.02em;
        }
        .news-content .ql-editor h2 { font-size: 2rem; }
        .news-content .ql-editor h3 { font-size: 1.5rem; }
        
        .news-content .ql-editor ul, 
        .news-content .ql-editor ol { 
            padding-left: 1.5rem; 
            margin-bottom: 1rem; 
        }
        .news-content .ql-editor ul li, 
        .news-content .ql-editor ol li {
            margin-bottom: 0.75rem;
            padding-left: 0.25rem;
        }
        
        .news-content .ql-editor blockquote {
            border-left: 4px solid var(--color-brand-500);
            padding: 1.5rem 2rem;
            font-style: italic;
            color: #1e293b;
            background-color: #f8fafc;
            border-radius: 0 1rem 1rem 0;
            margin: 3rem 0;
            font-size: 1.25rem;
            line-height: 1.8;
            font-weight: 500;
            box-shadow: inset 0 2px 4px 0 rgba(0,0,0, 0.02);
        }
        
        .news-content .ql-editor img {
            border-radius: 1rem;
            max-width: 100%;
            height: auto;
            margin: 1rem auto;
            display: block;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
        
        .news-content .ql-editor a {
            color: var(--color-brand-600);
            text-decoration-line: underline;
            text-decoration-thickness: 2px;
            text-underline-offset: 4px;
            text-decoration-color: var(--color-brand-300);
            transition: all 0.2s ease;
        }
        .news-content .ql-editor a:hover {
            color: var(--color-brand-800);
            text-decoration-color: var(--color-brand-600);
        }
    </style>';

require 'layout/header.php';
?>

<!-- Main Content -->
<main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-2 lg:py-4">
    <article class="bg-white p-8 md:p-12 rounded-3xl border border-slate-100 shadow-sm">
        <header class="mb-10 text-center">
            <div class="inline-flex items-center justify-center mb-6">
                <span
                    class="bg-brand-50 text-brand-700 px-4 py-1.5 rounded-full text-sm font-bold uppercase tracking-wider">
                    <?= htmlspecialchars($news['category_name'] ?? 'Uncategorized') ?>
                </span>
            </div>
            <h1 class="text-4xl md:text-5xl font-extrabold text-slate-900 leading-tight mb-6">
                <?= htmlspecialchars($news['title']) ?>
            </h1>
            <div class="flex flex-wrap items-center justify-center text-slate-500 gap-6">
                <div class="flex items-center gap-2">
                    <div
                        class="w-10 h-10 rounded-full bg-gradient-to-tr from-brand-400 to-brand-600 flex items-center justify-center text-white font-bold">
                        <?= strtoupper(substr($news['author_name'] ?? 'U', 0, 1)) ?>
                    </div>
                    <span
                        class="font-medium text-slate-700 text-lg"><?= htmlspecialchars($news['author_name'] ?? 'Unknown') ?></span>
                </div>
                <div class="flex items-center gap-2 text-base">
                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                        </path>
                    </svg>
                    <?= date('d F Y', strtotime($news['created_at'])) ?>
                </div>
            </div>
        </header>

        <?php if ($news['image']): ?>
            <figure class="max-w-4xl mx-auto">
                <div class="rounded-2xl overflow-hidden shadow-sm border border-slate-100 bg-slate-50">
                    <img src="uploads/news/<?= htmlspecialchars($news['image']) ?>"
                        alt="<?= htmlspecialchars($news['image_caption'] ?? 'Thumbnail') ?>"
                        class="w-full h-auto object-cover max-h-[500px]">
                </div>
                <?php if (!empty($news['image_caption'])): ?>
                    <figcaption class="mt-3 text-center text-sm text-slate-500 italic px-4">
                        <?= htmlspecialchars($news['image_caption']) ?>
                    </figcaption>
                <?php endif; ?>
            </figure>
        <?php endif; ?>

        <?php
        $contentHtml = $news['content'];
        $data = json_decode($contentHtml, true);
        if ($data !== null && isset($data['blocks'])) {
            // If the content is an old EditorJS JSON, render it to HTML manually
            $html = '';
            foreach ($data['blocks'] as $block) {
                if ($block['type'] === 'paragraph')
                    $html .= '<p>' . $block['data']['text'] . '</p>';
                if ($block['type'] === 'header')
                    $html .= '<h' . $block['data']['level'] . '>' . $block['data']['text'] . '</h' . $block['data']['level'] . '>';
                if ($block['type'] === 'list') {
                    $tag = $block['data']['style'] === 'ordered' ? 'ol' : 'ul';
                    $html .= "<$tag>";
                    foreach ($block['data']['items'] as $item)
                        $html .= "<li>$item</li>";
                    $html .= "</$tag>";
                }
                if ($block['type'] === 'quote')
                    $html .= '<blockquote>' . $block['data']['text'] . '</blockquote>';
            }
            $contentHtml = $html;
        } elseif ($contentHtml === strip_tags($contentHtml)) {
            // If the content is old plain text
            $paragraphs = explode("\n\n", str_replace("\r", "", $contentHtml));
            $html = '';
            foreach ($paragraphs as $p) {
                if (trim($p) !== '')
                    $html .= '<p>' . nl2br(htmlspecialchars(trim($p))) . '</p>';
            }
            $contentHtml = $html;
        }
        ?>
        <div class="max-w-3xl mx-auto news-content ql-snow">
            <div class="ql-editor" style="padding: 0;">
                <?= $contentHtml ?>
            </div>
        </div>

        <!-- Share Section -->
        <div class="mt-16 pt-8 border-t border-slate-100" x-data="{ copied: false }">
            <div class="bg-slate-50/80 border border-slate-100 rounded-3xl p-6 md:p-8 flex flex-col md:flex-row items-center justify-between gap-6">
                <div class="text-center md:text-left">
                    <h4 class="text-lg font-bold text-slate-800 mb-1">Bagikan ke teman Anda</h4>
                    <p class="text-sm text-slate-500">Bantu sebarluaskan informasi bermanfaat ini.</p>
                </div>
                
                <div class="flex flex-wrap items-center justify-center gap-3">
                    <!-- WhatsApp -->
                    <a href="https://api.whatsapp.com/send?text=<?= urlencode($news['title'] . ' — Baca selengkapnya: ' . $current_url) ?>"
                       target="_blank" rel="noopener noreferrer" title="Bagikan ke WhatsApp"
                       class="flex items-center justify-center w-12 h-12 rounded-full bg-[#25D366]/10 text-[#25D366] hover:bg-[#25D366] hover:text-white transition-all duration-300 shadow-sm hover:shadow-md hover:-translate-y-1">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                    </a>

                    <!-- Facebook -->
                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode($current_url) ?>"
                       target="_blank" rel="noopener noreferrer" title="Bagikan ke Facebook"
                       class="flex items-center justify-center w-12 h-12 rounded-full bg-[#1877F2]/10 text-[#1877F2] hover:bg-[#1877F2] hover:text-white transition-all duration-300 shadow-sm hover:shadow-md hover:-translate-y-1">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                    </a>

                    <!-- X (Twitter) -->
                    <a href="https://twitter.com/intent/tweet?text=<?= urlencode($news['title']) ?>&url=<?= urlencode($current_url) ?>"
                       target="_blank" rel="noopener noreferrer" title="Bagikan ke X"
                       class="flex items-center justify-center w-12 h-12 rounded-full bg-[#0f1419]/10 text-[#0f1419] hover:bg-[#0f1419] hover:text-white transition-all duration-300 shadow-sm hover:shadow-md hover:-translate-y-1">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                    </a>

                    <!-- Telegram -->
                    <a href="https://t.me/share/url?url=<?= urlencode($current_url) ?>&text=<?= urlencode($news['title']) ?>"
                       target="_blank" rel="noopener noreferrer" title="Bagikan ke Telegram"
                       class="flex items-center justify-center w-12 h-12 rounded-full bg-[#0088cc]/10 text-[#0088cc] hover:bg-[#0088cc] hover:text-white transition-all duration-300 shadow-sm hover:shadow-md hover:-translate-y-1">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.479.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/></svg>
                    </a>

                    <!-- Copy Link -->
                    <button @click="navigator.clipboard.writeText(window.location.href); copied = true; setTimeout(() => copied = false, 2000)"
                            title="Salin Link"
                            class="flex items-center justify-center w-12 h-12 rounded-full transition-all duration-300 shadow-sm hover:shadow-md hover:-translate-y-1 cursor-pointer"
                            :class="copied ? 'bg-emerald-500 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-800 hover:text-white'">
                        <svg x-show="!copied" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                        </svg>
                        <svg x-show="copied" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <div class="mt-8 pt-6 border-t border-slate-100 text-center">
            <a href="index.php"
                class="inline-flex items-center justify-center gap-2 text-brand-600 hover:text-brand-800 font-medium transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali ke Beranda
            </a>
        </div>
    </article>
</main>

<?php require 'layout/footer.php'; ?>