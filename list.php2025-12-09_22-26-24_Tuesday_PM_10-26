<?php

// Path to the directory to save the notes in, without trailing slash.
$save_path = getenv('MWN_SAVE_PATH') ?: '_tmp';
$base_url = getenv('MWN_BASE_URL') ?: '';

// Disable caching.
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

$notes = [];

// Get all note files
$files = glob($save_path . '/*');

foreach ($files as $file) {
    // Skip directories and .htaccess
    if (is_dir($file) || basename($file) === '.htaccess') {
        continue;
    }
    
    $note_name = basename($file);
    $note_content = file_get_contents($file);
    $modified_time = filemtime($file);
    
    $notes[] = [
        'name' => $note_name,
        'preview' => mb_substr($note_content, 0, 200),
        'size' => strlen($note_content),
        'modified' => $modified_time
    ];
}

// Sort by modified time (newest first)
usort($notes, function($a, $b) {
    return $b['modified'] - $a['modified'];
});

$total_notes = count($notes);
?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Notes</title>
    <link rel="shortcut icon" href="<?php print $base_url; ?>/favicon.ico">
    <link rel="stylesheet" href="<?php print $base_url; ?>/styles.css">
    <style>
        .list-container {
            max-width: 900px;
            margin: 20px auto;
            padding: 20px;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 10px;
        }
        .header h1 {
            margin: 0;
        }
        .header-links a {
            margin-left: 15px;
            color: #007bff;
            text-decoration: none;
        }
        .header-links a:hover {
            text-decoration: underline;
        }
        .stats {
            background: #f0f0f0;
            padding: 10px 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            color: #666;
        }
        .note-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .note-item {
            padding: 15px;
            margin: 10px 0;
            background: #f9f9f9;
            border-radius: 4px;
            border-left: 3px solid #28a745;
        }
        .note-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            flex-wrap: wrap;
            gap: 10px;
        }
        .note-title a {
            font-size: 18px;
            color: #007bff;
            text-decoration: none;
            font-weight: bold;
        }
        .note-title a:hover {
            text-decoration: underline;
        }
        .note-meta {
            font-size: 12px;
            color: #999;
        }
        .note-preview {
            margin-top: 10px;
            color: #666;
            font-size: 14px;
            word-break: break-word;
            line-height: 1.5;
            white-space: pre-wrap;
        }
        .no-notes {
            text-align: center;
            color: #666;
            font-style: italic;
            padding: 40px;
        }
        .empty-note {
            color: #999;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="list-container">
        <div class="header">
            <h1>📋 All Notes</h1>
            <div class="header-links">
                <a href="<?php print $base_url; ?>/search">🔍 Search</a>
                <a href="<?php print $base_url; ?>/">+ New Note</a>
            </div>
        </div>

        <div class="stats">
            📊 Total: <strong><?php print $total_notes; ?></strong> note(s)
        </div>

        <?php if (empty($notes)): ?>
            <div class="no-notes">
                <p>No notes yet.</p>
                <p><a href="<?php print $base_url; ?>/">Create your first note →</a></p>
            </div>
        <?php else: ?>
            <ul class="note-list">
                <?php foreach ($notes as $note): ?>
                    <li class="note-item">
                        <div class="note-header">
                            <div class="note-title">
                                <a href="<?php print $base_url . '/' . htmlspecialchars($note['name'], ENT_QUOTES, 'UTF-8'); ?>">
                                    <?php print htmlspecialchars($note['name'], ENT_QUOTES, 'UTF-8'); ?>
                                </a>
                            </div>
                            <div class="note-meta">
                                <?php print date('Y-m-d H:i:s', $note['modified']); ?> · 
                                <?php print number_format($note['size']); ?> bytes
                            </div>
                        </div>
                        <div class="note-preview">
                            <?php if ($note['size'] > 0): ?>
                                <?php print htmlspecialchars($note['preview'], ENT_QUOTES, 'UTF-8'); ?>
                                <?php if ($note['size'] > 200): ?>...<?php endif; ?>
                            <?php else: ?>
                                <span class="empty-note">(empty)</span>
                            <?php endif; ?>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</body>
</html>
