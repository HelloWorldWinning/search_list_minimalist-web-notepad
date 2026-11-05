<?php

date_default_timezone_set(getenv('TZ') ?: 'Australia/Perth');
// Base URL of the website, without trailing slash.
$base_url = getenv('MWN_BASE_URL') ?: '';

// Path to the directory to save the notes in, without trailing slash.
// Should be outside of the document root, if possible.
$save_path = getenv('MWN_SAVE_PATH') ?: '_tmp';

// Disable caching.
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

//// If no name is provided or it contains invalid characters or it is too long.
//if (!isset($_GET['note']) || !preg_match('/^[a-zA-Z0-9_-]+$/', $_GET['note']) || strlen($_GET['note']) > 64) {
//
//    // Generate a name with 5 random unambiguous characters. Redirect to it.
////    header("Location: $base_url/" . substr(str_shuffle('234579abcdefghjkmnpqrstwxyz'), -5));
//    header("Location: $base_url/" . substr(str_shuffle('abcdefghjkmnpqrstwxyz'), -6));
//    die;
//}
//


// If no name is provided or it contains invalid characters or it is too long.
if (!isset($_GET['note']) || !preg_match('/^[a-zA-Z0-9_-]+$/', $_GET['note']) || strlen($_GET['note']) > 64) {

	// Load the word list.
$words = @file(
    __DIR__ . '/.Longman_Communication_3000.txt',
    FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES
);

if ($words === false) {
    http_response_code(500);
    exit('Cannot read Longman_Communication_3000.txt');
}

// Keep unique, lowercase, alphabetic words.
$words = array_map('trim', $words);
$words = array_map('strtolower', $words);
$words = array_values(array_unique(
    preg_grep('/^[a-z]+$/D', $words)
));

if (!$words) {
    http_response_code(500);
    exit('The word list contains no usable words.');
}

// Try words in random order, avoiding existing note names.
shuffle($words);
$today = date('Y-m-d');

foreach ($words as $word) {
    $random_name = $today . '-' . $word;
    $path_check = $save_path . '/' . $random_name;

    if (!file_exists($path_check)) {
        header("Location: $base_url/" . $random_name);
        die;
    }
}

// Every usable word already has a note for today.
http_response_code(503);
exit('No unused note names are available for today.');


//    // Generate a name with 6 random unambiguous characters.
//    // Check for collision and regenerate if needed.
//    //    $random_name = substr(str_shuffle('abcdefghjkmnpqrstwxyz'), -6);
//    do {
//	$random_name = substr(str_shuffle('234579abcdefghjkmnpqrstwxyz'), -4);
//        $path_check = $save_path . '/' . $random_name;
//    } while (is_file($path_check));
//
//    // Redirect to the unique name.
//    header("Location: $base_url/" . $random_name);
//
//    die;
}




// Route "search" to the search page
if ($_GET['note'] === 'search') {
    include 'search.php';
    die;
}

// Route "list" to the list page
if ($_GET['note'] === 'list') {
    include 'list.php';
    die;
}

$path = $save_path . '/' . $_GET['note'];

if (isset($_POST['text'])) {

    // Update file.
    file_put_contents($path, $_POST['text']);

    // If provided input is empty, delete file.
    if (!strlen($_POST['text'])) {
        unlink($path);
    }
    die;
}

// Print raw file if the client is curl, wget, or when explicitly requested.
if (isset($_GET['raw']) || strpos($_SERVER['HTTP_USER_AGENT'], 'curl') === 0 || strpos($_SERVER['HTTP_USER_AGENT'], 'Wget') === 0) {
    if (is_file($path)) {
        header('Content-type: text/plain');
        print file_get_contents($path);
    } else {
        header('HTTP/1.0 404 Not Found');
    }
    die;
}
?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="generator" content="Minimalist Web Notepad (https://github.com/pereorga/minimalist-web-notepad)">
    <title><?php print $_GET['note']; ?></title>
    <link rel="shortcut icon" href="<?php print $base_url; ?>/favicon.ico">
    <link rel="stylesheet" href="<?php print $base_url; ?>/styles.css">
</head>
<body>
    <div class="container">
        <textarea id="content"><?php
            if (is_file($path)) {
                print htmlspecialchars(file_get_contents($path), ENT_QUOTES, 'UTF-8');
            }
        ?></textarea>
    </div>
    <pre id="printable"></pre>
    <script src="<?php print $base_url; ?>/script.js"></script>
</body>
</html>
