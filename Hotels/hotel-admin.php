<?php
require_once __DIR__ . '/../backend/config.php';

error_reporting(E_ALL);
ini_set('display_errors', 0);

$conn = getDBConnection();

if (!tableExists($conn, 'hotels')) {
    sendJSONResponse(false, 'Table "hotels" does not exist. Please import the hotels schema first.');
}

$message = '';
$messageType = 'success';
$editingHotel = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_id'])) {
        $deleteId = (int) $_POST['delete_id'];

        if ($deleteId > 0) {
            try {
                $stmt = $conn->prepare('DELETE FROM hotels WHERE id = ?');
                $stmt->bind_param('i', $deleteId);

                if ($stmt->execute()) {
                    header('Location: ' . $_SERVER['PHP_SELF'] . '?deleted=1');
                    exit;
                }

                $message = 'Failed to delete hotel: ' . $stmt->error;
                $messageType = 'error';
            } catch (Throwable $e) {
                $message = 'Database error: ' . $e->getMessage();
                $messageType = 'error';
            }
        }
    } else {
        $hotelData = [
        'name' => trim($_POST['name'] ?? ''),
        'city' => trim($_POST['city'] ?? ''),
        'state' => trim($_POST['state'] ?? ''),
        'rating' => trim($_POST['rating'] ?? ''),
        'price' => trim($_POST['price'] ?? ''),
        'contact' => trim($_POST['contact'] ?? ''),
        'email' => trim($_POST['email'] ?? ''),
        'address' => trim($_POST['address'] ?? ''),
        'facilities' => trim($_POST['facilities'] ?? ''),
        'food_type' => trim($_POST['food_type'] ?? ''),
        'cuisine' => trim($_POST['cuisine'] ?? ''),
        'reviews_count' => trim($_POST['reviews_count'] ?? ''),
        'opening_time' => trim($_POST['opening_time'] ?? ''),
        'closing_time' => trim($_POST['closing_time'] ?? ''),
        'latitude' => trim($_POST['latitude'] ?? ''),
        'longitude' => trim($_POST['longitude'] ?? ''),
        'open_days' => trim($_POST['open_days'] ?? ''),
        'customer_review' => trim($_POST['customer_review'] ?? ''),
        'customer_review2' => trim($_POST['customer_review2'] ?? ''),
        'customer_review3' => trim($_POST['customer_review3'] ?? ''),
        'customer_review4' => trim($_POST['customer_review4'] ?? ''),
        'customer_review5' => trim($_POST['customer_review5'] ?? ''),
        ];

        $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;

        if ($hotelData['name'] === '' || $hotelData['city'] === '' || $hotelData['state'] === '') {
            $message = 'Name, city, and state are required.';
            $messageType = 'error';
        } else {
            try {
                if ($id > 0) {
                    $sql = "UPDATE hotels SET name = ?, city = ?, state = ?, rating = ?, price = ?, contact = ?, email = ?, address = ?, facilities = ?, food_type = ?, cuisine = ?, reviews_count = ?, opening_time = ?, closing_time = ?, latitude = ?, longitude = ?, open_days = ?, customer_review = ?, customer_review2 = ?, customer_review3 = ?, customer_review4 = ?, customer_review5 = ? WHERE id = ?";
                    $stmt = $conn->prepare($sql);
                    $types = str_repeat('s', 11) . 'i' . str_repeat('s', 10) . 'i';
                    $stmt->bind_param(
                        $types,
                        $hotelData['name'],
                        $hotelData['city'],
                        $hotelData['state'],
                        $hotelData['rating'],
                        $hotelData['price'],
                        $hotelData['contact'],
                        $hotelData['email'],
                        $hotelData['address'],
                        $hotelData['facilities'],
                        $hotelData['food_type'],
                        $hotelData['cuisine'],
                        $hotelData['reviews_count'],
                        $hotelData['opening_time'],
                        $hotelData['closing_time'],
                        $hotelData['latitude'],
                        $hotelData['longitude'],
                        $hotelData['open_days'],
                        $hotelData['customer_review'],
                        $hotelData['customer_review2'],
                        $hotelData['customer_review3'],
                        $hotelData['customer_review4'],
                        $hotelData['customer_review5'],
                        $id
                    );

                    if ($stmt->execute()) {
                        header('Location: ' . $_SERVER['PHP_SELF'] . '?updated=1');
                        exit;
                    }

                    $message = 'Failed to update hotel: ' . $stmt->error;
                    $messageType = 'error';
                } else {
                    $sql = "INSERT INTO hotels (name, city, state, rating, price, contact, email, address, facilities, food_type, cuisine, reviews_count, opening_time, closing_time, latitude, longitude, open_days, customer_review, customer_review2, customer_review3, customer_review4, customer_review5) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                    $stmt = $conn->prepare($sql);
                    $types = str_repeat('s', 11) . 'i' . str_repeat('s', 10);
                    $stmt->bind_param(
                        $types,
                        $hotelData['name'],
                        $hotelData['city'],
                        $hotelData['state'],
                        $hotelData['rating'],
                        $hotelData['price'],
                        $hotelData['contact'],
                        $hotelData['email'],
                        $hotelData['address'],
                        $hotelData['facilities'],
                        $hotelData['food_type'],
                        $hotelData['cuisine'],
                        $hotelData['reviews_count'],
                        $hotelData['opening_time'],
                        $hotelData['closing_time'],
                        $hotelData['latitude'],
                        $hotelData['longitude'],
                        $hotelData['open_days'],
                        $hotelData['customer_review'],
                        $hotelData['customer_review2'],
                        $hotelData['customer_review3'],
                        $hotelData['customer_review4'],
                        $hotelData['customer_review5']
                    );

                    if ($stmt->execute()) {
                        header('Location: ' . $_SERVER['PHP_SELF'] . '?added=1');
                        exit;
                    }

                    $message = 'Failed to add hotel: ' . $stmt->error;
                    $messageType = 'error';
                }
            } catch (Throwable $e) {
                $message = 'Database error: ' . $e->getMessage();
                $messageType = 'error';
            }
        }
    }
}

if (isset($_GET['edit'])) {
    $editId = (int) $_GET['edit'];
    $stmt = $conn->prepare('SELECT * FROM hotels WHERE id = ? LIMIT 1');
    $stmt->bind_param('i', $editId);
    $stmt->execute();
    $editingHotel = $stmt->get_result()->fetch_assoc() ?: null;

    if (!$editingHotel) {
        $message = 'Hotel not found.';
        $messageType = 'error';
    }
}

$hotels = [];
$result = $conn->query('SELECT * FROM hotels ORDER BY created_at DESC, id DESC');
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $hotels[] = $row;
    }
}

function tableExists($conn, $tableName)
{
    $tableName = mysqli_real_escape_string($conn, $tableName);
    $result = $conn->query("SHOW TABLES LIKE '$tableName'");

    return $result && $result->num_rows > 0;
}

function fieldValue($row, $key)
{
    return htmlspecialchars($row[$key] ?? '', ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Manager</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #f4f8fd;
            --surface: rgba(255, 255, 255, 0.92);
            --text: #1f3049;
            --muted: #64748b;
            --line: #dbe6f3;
            --primary: #0b69c7;
            --primary-dark: #0b4f97;
            --success: #15803d;
            --danger: #b91c1c;
            --shadow: 0 18px 45px rgba(6, 20, 42, 0.12);
            --radius: 20px;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            color: var(--text);
            background:
                radial-gradient(circle at 15% 15%, rgba(14, 165, 233, 0.12), transparent 28%),
                radial-gradient(circle at 85% 20%, rgba(59, 130, 246, 0.12), transparent 30%),
                linear-gradient(145deg, #f7fbff 0%, var(--bg) 48%, #eef4fb 100%);
            min-height: 100vh;
        }

        .wrap {
            max-width: 1400px;
            margin: 0 auto;
            padding: 32px 18px 60px;
        }

        .hero {
            background: linear-gradient(135deg, #0f2f54 0%, #0b5cab 55%, #0ea5e9 100%);
            color: #fff;
            border-radius: 28px;
            padding: 28px;
            box-shadow: var(--shadow);
            margin-bottom: 24px;
        }

        .hero h1 {
            margin: 0 0 8px;
            font-size: 30px;
        }

        .hero p {
            margin: 0;
            color: rgba(255, 255, 255, 0.85);
            max-width: 760px;
        }

        .grid {
            display: grid;
            grid-template-columns: 1.05fr 1fr;
            gap: 22px;
            align-items: start;
        }

        .card {
            background: var(--surface);
            border: 1px solid rgba(255, 255, 255, 0.72);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            backdrop-filter: blur(12px);
        }

        .card-head {
            padding: 20px 22px 0;
        }

        .card-head h2 {
            margin: 0 0 6px;
            font-size: 22px;
        }

        .card-head p {
            margin: 0;
            color: var(--muted);
            font-size: 14px;
        }

        .form {
            padding: 18px 22px 22px;
        }

        .notice {
            margin: 0 22px 18px;
            padding: 12px 14px;
            border-radius: 14px;
            font-size: 14px;
        }

        .notice.success { background: #dcfce7; color: var(--success); }
        .notice.error { background: #fee2e2; color: var(--danger); }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
        }

        .field { display: flex; flex-direction: column; gap: 7px; }

        .field.full { grid-column: 1 / -1; }

        label {
            font-size: 13px;
            font-weight: 600;
            color: #344767;
        }

        input, textarea, select {
            width: 100%;
            border: 1px solid var(--line);
            border-radius: 14px;
            padding: 12px 14px;
            font: inherit;
            color: var(--text);
            background: #fff;
            outline: none;
            transition: 0.2s ease;
        }

        textarea { min-height: 92px; resize: vertical; }

        input:focus, textarea:focus, select:focus {
            border-color: rgba(11, 105, 199, 0.55);
            box-shadow: 0 0 0 4px rgba(11, 105, 199, 0.08);
        }

        .actions {
            display: flex;
            gap: 12px;
            margin-top: 18px;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: none;
            border-radius: 14px;
            padding: 12px 18px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: 0.2s ease;
        }

        .btn-primary { background: linear-gradient(135deg, var(--primary), var(--primary-dark)); color: #fff; }
        .btn-light { background: #eef4fb; color: var(--text); }

        .btn:hover { transform: translateY(-1px); }

        .list-card {
            padding: 0;
            overflow: hidden;
        }

        .table-wrap { overflow-x: auto; }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 1100px;
        }

        th:last-child,
        td:last-child {
            width: 180px;
            white-space: nowrap;
        }

        th, td {
            padding: 14px 16px;
            border-bottom: 1px solid var(--line);
            text-align: left;
            vertical-align: top;
            font-size: 14px;
        }

        th {
            background: #f8fbff;
            color: #35506f;
            position: sticky;
            top: 0;
            z-index: 1;
        }

        .meta {
            color: var(--muted);
            font-size: 12px;
        }

        .pill {
            display: inline-flex;
            align-items: center;
            padding: 5px 10px;
            border-radius: 999px;
            background: #edf6ff;
            color: #0b4f97;
            font-size: 12px;
            font-weight: 600;
            margin-right: 6px;
            margin-bottom: 6px;
        }

        .row-actions a {
            display: inline-block;
            margin-right: 8px;
            color: var(--primary-dark);
            text-decoration: none;
            font-weight: 600;
        }

        .row-actions {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
            min-width: 160px;
        }

        .row-edit-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 8px 12px;
            border-radius: 10px;
            background: #e8f1fd;
            color: var(--primary-dark);
            text-decoration: none;
            font-weight: 700;
            border: 1px solid #cfe0f6;
            box-shadow: 0 2px 8px rgba(11, 105, 199, 0.08);
        }

        .row-edit-btn:hover {
            background: #dbeaff;
        }

        .row-delete-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 8px 12px;
            border-radius: 10px;
            border: none;
            background: #ffecec;
            color: var(--danger);
            font: inherit;
            font-weight: 600;
            cursor: pointer;
            border: 1px solid #f3b9b9;
            box-shadow: 0 2px 8px rgba(185, 28, 28, 0.08);
        }

        .row-delete-btn:hover {
            background: #ffdcdc;
        }

        .empty {
            padding: 28px;
            color: var(--muted);
        }

        @media (max-width: 1100px) {
            .grid { grid-template-columns: 1fr; }
        }

        @media (max-width: 700px) {
            .form-grid { grid-template-columns: 1fr; }
            .wrap { padding: 18px 12px 40px; }
            .hero { padding: 22px; }
            .hero h1 { font-size: 24px; }
        }
    </style>
</head>
<body>
    <div class="wrap">
        <div class="hero">
            <h1>Hotel Manager</h1>
            <p>Add or edit hotel listings from a single page. Use the form on the left to create or update records, and the table below to jump into edit mode.</p>
        </div>

        <div class="grid">
            <section class="card">
                <div class="card-head">
                    <h2><?php echo $editingHotel ? 'Edit Hotel' : 'Add Hotel'; ?></h2>
                    <p><?php echo $editingHotel ? 'Update the selected listing and save changes.' : 'Fill in hotel details and submit to create a new record.'; ?></p>
                </div>

                <?php if (!empty($_GET['added'])): ?>
                    <div class="notice success">Hotel added successfully.</div>
                <?php endif; ?>

                <?php if (!empty($_GET['updated'])): ?>
                    <div class="notice success">Hotel updated successfully.</div>
                <?php endif; ?>

                <?php if (!empty($_GET['deleted'])): ?>
                    <div class="notice success">Hotel deleted successfully.</div>
                <?php endif; ?>

                <?php if ($message !== ''): ?>
                    <div class="notice <?php echo $messageType; ?>"><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></div>
                <?php endif; ?>

                <?php $form = $editingHotel ?: []; ?>
                <form method="post" class="form">
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($form['id'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">

                    <div class="form-grid">
                        <div class="field full">
                            <label>Hotel Name</label>
                            <input type="text" name="name" value="<?php echo fieldValue($form, 'name'); ?>" required>
                        </div>
                        <div class="field">
                            <label>City</label>
                            <input type="text" name="city" value="<?php echo fieldValue($form, 'city'); ?>" required>
                        </div>
                        <div class="field">
                            <label>State</label>
                            <input type="text" name="state" value="<?php echo fieldValue($form, 'state'); ?>" required>
                        </div>
                        <div class="field">
                            <label>Rating</label>
                            <input type="text" name="rating" value="<?php echo fieldValue($form, 'rating'); ?>" placeholder="4.5">
                        </div>
                        <div class="field">
                            <label>Price</label>
                            <input type="text" name="price" value="<?php echo fieldValue($form, 'price'); ?>" placeholder="2500.00">
                        </div>
                        <div class="field">
                            <label>Contact</label>
                            <input type="text" name="contact" value="<?php echo fieldValue($form, 'contact'); ?>">
                        </div>
                        <div class="field">
                            <label>Email</label>
                            <input type="email" name="email" value="<?php echo fieldValue($form, 'email'); ?>">
                        </div>
                        <div class="field full">
                            <label>Address</label>
                            <textarea name="address"><?php echo fieldValue($form, 'address'); ?></textarea>
                        </div>
                        <div class="field full">
                            <label>Facilities</label>
                            <textarea name="facilities"><?php echo fieldValue($form, 'facilities'); ?></textarea>
                        </div>
                        <div class="field">
                            <label>Food Type</label>
                            <select name="food_type">
                                <?php $foodType = $form['food_type'] ?? ''; ?>
                                <option value="" <?php echo $foodType === '' ? 'selected' : ''; ?>>Select</option>
                                <option value="veg" <?php echo $foodType === 'veg' ? 'selected' : ''; ?>>Veg</option>
                                <option value="non-veg" <?php echo $foodType === 'non-veg' ? 'selected' : ''; ?>>Non-Veg</option>
                                <option value="both" <?php echo $foodType === 'both' ? 'selected' : ''; ?>>Both</option>
                            </select>
                        </div>
                        <div class="field">
                            <label>Cuisine</label>
                            <input type="text" name="cuisine" value="<?php echo fieldValue($form, 'cuisine'); ?>" placeholder="Indian, Chinese, Italian">
                        </div>
                        <div class="field">
                            <label>Reviews Count</label>
                            <input type="number" name="reviews_count" value="<?php echo fieldValue($form, 'reviews_count'); ?>" min="0">
                        </div>
                        <div class="field">
                            <label>Open Time</label>
                            <input type="time" name="opening_time" value="<?php echo fieldValue($form, 'opening_time'); ?>">
                        </div>
                        <div class="field">
                            <label>Close Time</label>
                            <input type="time" name="closing_time" value="<?php echo fieldValue($form, 'closing_time'); ?>">
                        </div>
                        <div class="field">
                            <label>Latitude</label>
                            <input type="text" name="latitude" value="<?php echo fieldValue($form, 'latitude'); ?>" placeholder="30.3165">
                        </div>
                        <div class="field">
                            <label>Longitude</label>
                            <input type="text" name="longitude" value="<?php echo fieldValue($form, 'longitude'); ?>" placeholder="78.0322">
                        </div>
                        <div class="field full">
                            <label>Open Days</label>
                            <input type="text" name="open_days" value="<?php echo fieldValue($form, 'open_days'); ?>" placeholder="Mon,Tue,Wed,Thu,Fri">
                        </div>
                        <div class="field full">
                            <label>Customer Review 1</label>
                            <textarea name="customer_review"><?php echo fieldValue($form, 'customer_review'); ?></textarea>
                        </div>
                        <div class="field full">
                            <label>Customer Review 2</label>
                            <textarea name="customer_review2"><?php echo fieldValue($form, 'customer_review2'); ?></textarea>
                        </div>
                        <div class="field full">
                            <label>Customer Review 3</label>
                            <textarea name="customer_review3"><?php echo fieldValue($form, 'customer_review3'); ?></textarea>
                        </div>
                        <div class="field full">
                            <label>Customer Review 4</label>
                            <textarea name="customer_review4"><?php echo fieldValue($form, 'customer_review4'); ?></textarea>
                        </div>
                        <div class="field full">
                            <label>Customer Review 5</label>
                            <textarea name="customer_review5"><?php echo fieldValue($form, 'customer_review5'); ?></textarea>
                        </div>
                    </div>

                    <div class="actions">
                        <button type="submit" class="btn btn-primary"><?php echo $editingHotel ? 'Update Hotel' : 'Save Hotel'; ?></button>
                        <a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="btn btn-light">Reset</a>
                    </div>
                </form>
            </section>

            <section class="card list-card">
                <div class="card-head" style="padding-bottom: 18px;">
                    <h2>Saved Hotels</h2>
                    <p><?php echo count($hotels); ?> records found</p>
                </div>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Location</th>
                                <th>Rating</th>
                                <th>Price</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($hotels)): ?>
                                <tr>
                                    <td colspan="6"><div class="empty">No hotels added yet.</div></td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($hotels as $hotel): ?>
                                    <tr>
                                        <td><?php echo (int) $hotel['id']; ?></td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($hotel['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?></strong><br>
                                            <span class="meta"><?php echo htmlspecialchars($hotel['food_type'] ?? '', ENT_QUOTES, 'UTF-8'); ?> | <?php echo htmlspecialchars($hotel['cuisine'] ?? '', ENT_QUOTES, 'UTF-8'); ?></span>
                                        </td>
                                        <td>
                                            <?php echo htmlspecialchars($hotel['city'] ?? '', ENT_QUOTES, 'UTF-8'); ?>, <?php echo htmlspecialchars($hotel['state'] ?? '', ENT_QUOTES, 'UTF-8'); ?><br>
                                            <span class="meta"><?php echo htmlspecialchars($hotel['open_days'] ?? '', ENT_QUOTES, 'UTF-8'); ?></span>
                                        </td>
                                        <td><?php echo htmlspecialchars($hotel['rating'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><?php echo htmlspecialchars($hotel['price'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td class="row-actions">
                                            <a class="row-edit-btn" href="?edit=<?php echo (int) $hotel['id']; ?>">Edit</a>
                                            <form method="post" style="display:inline-block; margin:0;" onsubmit="return confirm('Delete this hotel?');">
                                                <input type="hidden" name="delete_id" value="<?php echo (int) $hotel['id']; ?>">
                                                <button type="submit" class="row-delete-btn">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>
</body>
</html>