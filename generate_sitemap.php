<?php
include "backend/config.php"; 

header("Content-Type: application/xml; charset=utf-8");

$conn = getDBConnection();

echo '<?xml version="1.0" encoding="UTF-8"?>';
?>

<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">

<!-- Static Pages -->
 
<url>
    <loc>https://everythingeasy.in/</loc>
    <lastmod>2025-12-22T19:02:28+00:00</lastmod>
    <changefreq>daily</changefreq>
    <priority>1.0</priority>
</url>

<url>
    <loc>https://everythingeasy.in/about</loc>
    <lastmod>2025-12-22T19:02:28+00:00</lastmod>
    <changefreq>monthly</changefreq>
    <priority>0.8</priority>
</url>

<url>
    <loc>https://everythingeasy.in/services</loc>
    <lastmod>2025-12-22T19:02:28+00:00</lastmod>
    <changefreq>monthly</changefreq>
    <priority>0.8</priority>
</url>

<url>
    <loc>https://everythingeasy.in/portfolio</loc>
    <lastmod>2025-12-22T19:02:28+00:00</lastmod>
    <changefreq>monthly</changefreq>
    <priority>0.8</priority>
</url>

<url>
    <loc>https://everythingeasy.in/blog</loc>
    <lastmod>2025-12-22T19:02:28+00:00</lastmod>
    <changefreq>weekly</changefreq>
    <priority>0.8</priority>
</url>

<url>
    <loc>https://everythingeasy.in/contact</loc>
    <lastmod>2025-12-22T19:02:28+00:00</lastmod>
    <changefreq>monthly</changefreq>
    <priority>0.8</priority>
</url>

<url>
    <loc>https://everythingeasy.in/career</loc>
    <lastmod>2025-12-22T19:02:28+00:00</lastmod>
    <changefreq>monthly</changefreq>
    <priority>0.8</priority>
</url>

<url>
    <loc>https://everythingeasy.in/services-locations</loc>
    <lastmod>2025-12-22T19:02:28+00:00</lastmod>
    <changefreq>monthly</changefreq>
    <priority>0.8</priority>
</url>

<url>
    <loc>https://everythingeasy.in/it-services.php</loc>
    <lastmod>2025-12-22T19:02:28+00:00</lastmod>
    <changefreq>weekly</changefreq>
    <priority>0.8</priority>
</url>

<?php
// Dynamic URLs
$sql = "SELECT slug, updated_at FROM locations";
$res = mysqli_query($conn, $sql);

while ($row = mysqli_fetch_assoc($res)) {

    // Fix double --
    $cleanSlug = preg_replace('/-+/', '-', $row['slug']);

    $url = "https://everythingeasy.in/it-services/" . $cleanSlug;

    $lastmod = date('c', strtotime($row['updated_at']));
?>
<url>
    <loc><?= htmlspecialchars($url) ?></loc>
    <lastmod><?= $lastmod ?></lastmod>
    <changefreq>weekly</changefreq>
    <priority>0.6</priority>
</url>

<?php } ?>

</urlset>