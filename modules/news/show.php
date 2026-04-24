<?php
require '../../config/db.php';
require '../../includes/header.php';
require '../../includes/sidebar.php';

if(!isset($_GET['id'])) { header("Location: index.php"); exit; }
$id = $_GET['id'];

$stmt = $conn->prepare("SELECT news.*, users.name as author_name, categories.name as category_name 
                      FROM news 
                      LEFT JOIN users ON news.author_id = users.id 
                      LEFT JOIN categories ON news.category_id = categories.id 
                      WHERE news.id = ?");
$stmt->execute([$id]);
$news = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$news) { header("Location: index.php"); exit; }

// Check permissions
if($role === 'kontributor' && $news['author_id'] != $_SESSION['user_id']) {
    header("Location: index.php"); exit;
}
if($role === 'user') {
    header("Location: ../dashboard/index.php"); exit;
}

$back_url = isset($_GET['from']) && $_GET['from'] == 'review' ? '../review/index.php' : 'index.php';
?>
<main class="flex-1 overflow-y-auto bg-slate-50/50 p-4 sm:p-6 lg:p-8">
    <div class="mb-6 flex items-center justify-between">
        <a href="<?= $back_url ?>" class="inline-flex items-center gap-2 text-sm font-medium text-slate-500 hover:text-brand-600 transition bg-white px-4 py-2 rounded-lg border border-slate-200 shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali
        </a>
        <?php if($role === 'editor' || $role === 'admin'): ?>
            <?php if($news['status'] == 'pending'): ?>
            <div class="flex gap-2">
                <a href="../review/index.php?approve=<?= $news['id'] ?>&from=show" class="text-white bg-green-600 hover:bg-green-700 px-4 py-2 rounded-lg text-sm font-medium transition shadow-sm">Terbitkan Berita</a>
                <a href="../review/index.php?reject=<?= $news['id'] ?>&from=show" class="bg-red-50 border border-red-200 text-red-600 hover:bg-red-100 px-4 py-2 rounded-lg text-sm font-medium transition shadow-sm">Tolak ke Draft</a>
            </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <article class="bg-white p-8 md:p-12 rounded-2xl border border-slate-100 shadow-sm max-w-4xl mx-auto">
        <header class="mb-8 border-b border-slate-100 pb-8">
            <div class="flex flex-wrap items-center gap-3 mb-5">
                <span class="bg-brand-50 text-brand-700 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide">
                    <?= htmlspecialchars($news['category_name'] ?? 'Uncategorized') ?>
                </span>
                <?php if($news['status'] == 'published'): ?>
                    <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs font-semibold">Published</span>
                <?php elseif($news['status'] == 'pending'): ?>
                    <span class="bg-orange-100 text-orange-800 px-3 py-1 rounded-full text-xs font-semibold">Pending Review</span>
                <?php else: ?>
                    <span class="bg-slate-100 text-slate-800 px-3 py-1 rounded-full text-xs font-semibold">Draft</span>
                <?php endif; ?>
            </div>
            <h1 class="text-3xl md:text-4xl font-extrabold text-slate-900 leading-tight mb-4">
                <?= htmlspecialchars($news['title']) ?>
            </h1>
            <div class="flex flex-wrap items-center text-sm text-slate-500 gap-6">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-full bg-slate-200 flex items-center justify-center text-slate-600 font-bold">
                        <?= strtoupper(substr($news['author_name'] ?? 'U', 0, 1)) ?>
                    </div>
                    <span class="font-medium text-slate-700"><?= htmlspecialchars($news['author_name'] ?? 'Unknown') ?></span>
                </div>
                <div class="flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    <?= date('d M Y, H:i', strtotime($news['created_at'])) ?>
                </div>
            </div>
        </header>

        <?php if($news['image']): ?>
            <figure class="mb-10 max-w-4xl mx-auto">
                <div class="rounded-2xl overflow-hidden shadow-sm border border-slate-100 bg-slate-50">
                    <img src="../../uploads/news/<?= htmlspecialchars($news['image']) ?>" alt="<?= htmlspecialchars($news['image_caption'] ?? 'Thumbnail') ?>" class="w-full h-auto object-cover max-h-[500px]">
                </div>
                <?php if(!empty($news['image_caption'])): ?>
                    <figcaption class="mt-3 text-center text-sm text-slate-500 italic px-4">
                        <?= htmlspecialchars($news['image_caption']) ?>
                    </figcaption>
                <?php endif; ?>
            </figure>
        <?php endif; ?>

        <link href="../../node_modules/quill/dist/quill.snow.css" rel="stylesheet">
        <style>
            :root {
                --color-brand-300: #93c5fd;
                --color-brand-500: #2563eb;
                --color-brand-600: #1d4ed8;
                --color-brand-800: #1e3a8a;
            }
            .news-content .ql-editor {
                font-size: 1.1875rem; /* 19px for optimal reading */
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
                line-height: 0.8;
                margin-top: 0.25rem;
            }

            .news-content .ql-editor h2, 
            .news-content .ql-editor h3, 
            .news-content .ql-editor h4 {
                font-weight: 800;
                color: #0f172a;
                margin-top: 3rem;
                margin-bottom: 1.25rem;
                line-height: 1.3;
                letter-spacing: -0.02em;
            }
            .news-content .ql-editor h2 { font-size: 2rem; }
            .news-content .ql-editor h3 { font-size: 1.5rem; }
            
            .news-content .ql-editor ul, 
            .news-content .ql-editor ol { 
                padding-left: 1.5rem; 
                margin-bottom: 2rem; 
            }
            .news-content .ql-editor ul li, 
            .news-content .ql-editor ol li {
                margin-bottom: 0.75rem;
                padding-left: 0.25rem;
            }
            .news-content .ql-editor ul { list-style-type: disc; }
            .news-content .ql-editor ol { list-style-type: decimal; }
            
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
                margin: 3rem auto;
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
        </style>
        <?php
        $contentHtml = $news['content'];
        $data = json_decode($contentHtml, true);
        if ($data !== null && isset($data['blocks'])) {
            $html = '';
            foreach ($data['blocks'] as $block) {
                if ($block['type'] === 'paragraph') $html .= '<p>' . $block['data']['text'] . '</p>';
                if ($block['type'] === 'header') $html .= '<h' . $block['data']['level'] . '>' . $block['data']['text'] . '</h' . $block['data']['level'] . '>';
                if ($block['type'] === 'list') {
                    $tag = $block['data']['style'] === 'ordered' ? 'ol' : 'ul';
                    $html .= "<$tag>";
                    foreach ($block['data']['items'] as $item) $html .= "<li>$item</li>";
                    $html .= "</$tag>";
                }
                if ($block['type'] === 'quote') $html .= '<blockquote>' . $block['data']['text'] . '</blockquote>';
            }
            $contentHtml = $html;
        } elseif ($contentHtml === strip_tags($contentHtml)) {
            $paragraphs = explode("\n\n", str_replace("\r", "", $contentHtml));
            $html = '';
            foreach ($paragraphs as $p) {
                if (trim($p) !== '') $html .= '<p>' . nl2br(htmlspecialchars(trim($p))) . '</p>';
            }
            $contentHtml = $html;
        }
        ?>
        <div class="max-w-3xl mx-auto mt-8 news-content ql-snow">
            <div class="ql-editor" style="padding: 0;">
                <?= $contentHtml ?>
            </div>
        </div>
    </article>
</main>
<?php require '../../includes/footer.php'; ?>
