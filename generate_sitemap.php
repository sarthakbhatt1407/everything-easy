<?php
include "backend/config.php"; 
header("Content-Type: application/xml; charset=utf-8");
$conn = getDBConnection();

echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">

<!-- Static Pages -->
<url><loc>https://everythingeasy.in/</loc></url>
<url><loc>https://everythingeasy.in/about.html</loc></url>
<url><loc>https://everythingeasy.in/contact.html</loc></url>
<url><loc>https://everythingeasy.in/it-services.php</loc></url>

<?php
// Dynamic location URLs (slug based)
$sql = "SELECT slug FROM locations";
$res = mysqli_query($conn, $sql);

while ($row = mysqli_fetch_assoc($res)) {
?>
<url>
  <loc>https://everythingeasy.in/it-services/<?php echo $row['slug']; ?></loc>
</url>
<?php } ?>

</urlset>
