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
    <changefreq>daily</changefreq>
    <priority>1.0</priority>
</url>

<url>
    <loc>https://everythingeasy.in/about.html</loc>
    <changefreq>monthly</changefreq>
    <priority>0.7</priority>
</url>

<url>
    <loc>https://everythingeasy.in/contact.html</loc>
    <changefreq>monthly</changefreq>
    <priority>0.7</priority>
</url>

<url>
    <loc>https://everythingeasy.in/it-services.php</loc>
    <changefreq>weekly</changefreq>
    <priority>0.8</priority>
</url>

<url>
    <loc>https://everythingeasy.in/website-development-dehradun</loc>
    <changefreq>weekly</changefreq>
    <priority>0.8</priority>
</url>


<?php
// Dynamic location URLs
$sql = "SELECT slug, updated_at FROM locations WHERE status='active'";
$res = mysqli_query($conn, $sql);

while ($row = mysqli_fetch_assoc($res)) {

    $url = "https://everythingeasy.in/it-services/" . $row['slug'];
    $lastmod = date('Y-m-d', strtotime($row['updated_at']));
?>
<url>
    <loc><?= htmlspecialchars($url) ?></loc>
    <lastmod><?= $lastmod ?></lastmod>
    <changefreq>weekly</changefreq>
    <priority>0.6</priority>
</url>

<?php } ?>

</urlset>
