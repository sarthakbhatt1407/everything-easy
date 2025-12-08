<?php
// Change this to your domain
$baseUrl = "https://everythingeasy.in";

// Allowed file types
$allowed_ext = ["php","html","htm"];

// Files/folders to ignore
$exclude = [
    "generate_sitemap.php",
    "sitemap.xml",
    "robots.txt",
    "header.html",
    "footer.html"
];

// FOLDERS to ignore (admin, assets, js, css etc)
$ignore_folders = [
    "/admin",
    "/assets",
    "/css",
    "/js",
    "/images",
    "/img",
    "/vendor"
];

$rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(__DIR__));

$urls = [];

foreach ($rii as $file) {
    if ($file->isDir()) continue;

    $path = str_replace(__DIR__, "", $file->getPathname());
    $path = str_replace("\\", "/", $path);

    // Skip ignored folders
    foreach ($ignore_folders as $f) {
        if (strpos($path, $f) === 0) continue 2;
    }

    // Skip excluded files
    if (in_array(basename($path), $exclude)) continue;

    $ext = pathinfo($path, PATHINFO_EXTENSION);

    // Only add allowed extensions
    if (!in_array($ext, $allowed_ext)) continue;

    // Convert index.php / index.html to folder URL
    if (preg_match('/index\.(php|html|htm)$/i', $path)) {
        $url = rtrim(dirname($path), "/");
        if ($url == "") $url = "";
    } else {
        $url = $path;
    }

    // FINAL URL
    $final_url = $baseUrl . $url;
    $urls[] = $final_url;
}

// XML CREATE
$xml = '<?xml version="1.0" encoding="UTF-8"?>';
$xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

foreach ($urls as $u) {
    $xml .= "<url><loc>$u</loc></url>";
}

$xml .= '</urlset>';

// Save file
file_put_contents("sitemap.xml", $xml);

echo "Sitemap created successfully: sitemap.xml";
?>
