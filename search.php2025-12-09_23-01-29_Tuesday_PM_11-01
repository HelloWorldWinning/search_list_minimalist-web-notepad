<?php

// Path to the directory to save the notes in, without trailing slash.
$save_path = getenv('MWN_SAVE_PATH') ?: '_tmp';
$base_url = getenv('MWN_BASE_URL') ?: '';

// Disable caching.
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

$query = isset($_GET['q']) ? trim($_GET['q']) : '';
$title_results = [];
$content_results = [];

if ($query !== '') {
    // Get all note files
    $files = glob($save_path . '/*');
    
    foreach ($files as $file) {
        // Skip directories and .htaccess
        if (is_dir($file) || basename($file) === '.htaccess') {
            continue;
        }
        
        $note_name = basename($file);
        $note_content = file_get_contents($file);
        
        // Step 1: Search titles (filenames)
        if (stripos($note_name, $query) !== false) {
            $title_results[] = [
                'name' => $note_name,
                'preview' => mb_substr($note_content, 0, 100)
            ];
        }
        // Step 2: Search content
        elseif (stripos($note_content, $query) !== false) {
            // Find the position and extract context
            $pos = stripos($note_content, $query);
            $start = max(0, $pos - 50);
            $preview = mb_substr($note_content, $start, 150);
            
            $content_results[] = [
                'name' => $note_name,
                'preview' => $preview
            ];
        }
    }
}
?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Notes</title>
    <link rel="shortcut icon" href="<?php print $base_url; ?>/favicon.ico">
    <link rel="stylesheet" href="<?php print $base_url; ?>/styles.css">
    <style>
        .search-container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
        }
        .search-form {
            margin-bottom: 30px;
        }
        .search-input {
            width: 70%;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .search-button {
            padding: 10px 20px;
            font-size: 16px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .search-button:hover {
            background: #0056b3;
        }
        .results-section {
            margin-bottom: 30px;
        }
        .results-section h2 {
            border-bottom: 2px solid #eee;
            padding-bottom: 10px;
            color: #333;
        }
        .result-item {
            padding: 15px;
            margin: 10px 0;
            background: #f9f9f9;
            border-radius: 4px;
            border-left: 3px solid #007bff;
        }
        .result-item a {
            font-size: 18px;
            color: #007bff;
            text-decoration: none;
            font-weight: bold;
        }
        .result-item a:hover {
            text-decoration: underline;
        }
        .result-preview {
            margin-top: 8px;
            color: #666;
            font-size: 14px;
            word-break: break-word;
        }
        .no-results {
            color: #666;
            font-style: italic;
        }
        .highlight {
            background: yellow;
            padding: 1px 2px;
        }
    </style>
</head>
<body>
    <div class="search-container">
        <h1>🔍 Search Notes</h1>
        
        <form class="search-form" method="get" action="<?php print $base_url; ?>/search">
            <input type="text" name="q" class="search-input" 
                   value="<?php print htmlspecialchars($query, ENT_QUOTES, 'UTF-8'); ?>" 
                   placeholder="Enter search term..." autofocus>
            <button type="submit" class="search-button">Search</button>
        </form>

        <?php if ($query !== ''): ?>
            
            <!-- Title Results -->
            <div class="results-section">
                <h2>📄 Title Matches (<?php print count($title_results); ?>)</h2>
                <?php if (empty($title_results)): ?>
                    <p class="no-results">No notes found with "<?php print htmlspecialchars($query, ENT_QUOTES, 'UTF-8'); ?>" in the title.</p>
                <?php else: ?>
                    <?php foreach ($title_results as $result): ?>
                        <div class="result-item">
                            <a href="<?php print $base_url . '/' . htmlspecialchars($result['name'], ENT_QUOTES, 'UTF-8'); ?>">
                                <?php 
                                    $highlighted = preg_replace(
                                        '/(' . preg_quote($query, '/') . ')/i', 
                                        '<span class="highlight">$1</span>', 
                                        htmlspecialchars($result['name'], ENT_QUOTES, 'UTF-8')
                                    );
                                    print $highlighted;
                                ?>
                            </a>
                            <div class="result-preview">
                                <?php print htmlspecialchars($result['preview'], ENT_QUOTES, 'UTF-8'); ?>
                                <?php if (strlen($result['preview']) >= 100): ?>...<?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Content Results -->
            <div class="results-section">
                <h2>📝 Content Matches (<?php print count($content_results); ?>)</h2>
                <?php if (empty($content_results)): ?>
                    <p class="no-results">No notes found with "<?php print htmlspecialchars($query, ENT_QUOTES, 'UTF-8'); ?>" in the content.</p>
                <?php else: ?>
                    <?php foreach ($content_results as $result): ?>
                        <div class="result-item">
                            <a href="<?php print $base_url . '/' . htmlspecialchars($result['name'], ENT_QUOTES, 'UTF-8'); ?>">
                                <?php print htmlspecialchars($result['name'], ENT_QUOTES, 'UTF-8'); ?>
                            </a>
                            <div class="result-preview">
                                ...<?php 
                                    $highlighted = preg_replace(
                                        '/(' . preg_quote($query, '/') . ')/i', 
                                        '<span class="highlight">$1</span>', 
                                        htmlspecialchars($result['preview'], ENT_QUOTES, 'UTF-8')
                                    );
                                    print $highlighted;
                                ?>...
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <p><strong>Total:</strong> <?php print count($title_results) + count($content_results); ?> result(s) found.</p>

        <?php endif; ?>

        <p><a href="<?php print $base_url; ?>/">← Create new note</a></p>
    </div>
</body>
</html>
