<?php
/**
 * Database Connection Test
 * Run this file to verify database connection
 * Access: http://localhost/everything-easy/backend/test-connection.php
 */

require_once 'config.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Connection Test</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .card {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .success {
            color: #28a745;
            padding: 15px;
            background: #d4edda;
            border: 1px solid #c3e6cb;
            border-radius: 5px;
            margin: 10px 0;
        }
        .error {
            color: #dc3545;
            padding: 15px;
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            border-radius: 5px;
            margin: 10px 0;
        }
        .info {
            color: #0066cc;
            padding: 15px;
            background: #d1ecf1;
            border: 1px solid #bee5eb;
            border-radius: 5px;
            margin: 10px 0;
        }
        h1 {
            color: #333;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background: #0066cc;
            color: white;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #0066cc;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }
        .btn:hover {
            background: #0052a3;
        }
    </style>
</head>
<body>
    <div class="card">
        <h1>🔌 Database Connection Test</h1>
        
        <?php
        try {
            // Test connection
            $conn = getDBConnection();
            
            echo '<div class="success">';
            echo '<strong>✓ Connection Successful!</strong><br>';
            echo 'Connected to database: <strong>' . DB_NAME . '</strong>';
            echo '</div>';
            
            // Test if quotes table exists
            $result = $conn->query("SHOW TABLES LIKE 'quotes'");
            
            if ($result->num_rows > 0) {
                echo '<div class="success">';
                echo '<strong>✓ Table "quotes" exists</strong>';
                echo '</div>';
                
                // Get table info
                $countResult = $conn->query("SELECT COUNT(*) as total FROM quotes");
                $count = $countResult->fetch_assoc();
                
                $statsResult = $conn->query("
                    SELECT 
                        SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                        SUM(CASE WHEN status = 'in-progress' THEN 1 ELSE 0 END) as in_progress,
                        SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed
                    FROM quotes
                ");
                $stats = $statsResult->fetch_assoc();
                
                echo '<div class="info">';
                echo '<strong>Database Statistics:</strong><br>';
                echo 'Total Quotes: <strong>' . $count['total'] . '</strong><br>';
                echo 'Pending: <strong>' . $stats['pending'] . '</strong><br>';
                echo 'In Progress: <strong>' . $stats['in_progress'] . '</strong><br>';
                echo 'Completed: <strong>' . $stats['completed'] . '</strong>';
                echo '</div>';
                
                // Show sample data
                $sampleResult = $conn->query("SELECT * FROM quotes ORDER BY created_at DESC LIMIT 5");
                
                if ($sampleResult->num_rows > 0) {
                    echo '<h3>Recent Quote Requests:</h3>';
                    echo '<table>';
                    echo '<tr>';
                    echo '<th>ID</th>';
                    echo '<th>Name</th>';
                    echo '<th>Email</th>';
                    echo '<th>Service</th>';
                    echo '<th>Status</th>';
                    echo '<th>Date</th>';
                    echo '</tr>';
                    
                    while ($row = $sampleResult->fetch_assoc()) {
                        echo '<tr>';
                        echo '<td>' . $row['id'] . '</td>';
                        echo '<td>' . $row['first_name'] . ' ' . $row['last_name'] . '</td>';
                        echo '<td>' . $row['email'] . '</td>';
                        echo '<td>' . $row['service'] . '</td>';
                        echo '<td>' . ucfirst($row['status']) . '</td>';
                        echo '<td>' . date('M d, Y', strtotime($row['created_at'])) . '</td>';
                        echo '</tr>';
                    }
                    
                    echo '</table>';
                }
                
            } else {
                echo '<div class="error">';
                echo '<strong>✗ Table "quotes" not found</strong><br>';
                echo 'Please import the database.sql file';
                echo '</div>';
            }
            
            closeDBConnection($conn);
            
        } catch (Exception $e) {
            echo '<div class="error">';
            echo '<strong>✗ Connection Failed!</strong><br>';
            echo 'Error: ' . $e->getMessage();
            echo '</div>';
            
            echo '<div class="info">';
            echo '<strong>Troubleshooting:</strong><br>';
            echo '1. Make sure MySQL is running<br>';
            echo '2. Check credentials in config.php<br>';
            echo '3. Import database.sql file<br>';
            echo '4. Verify database user has proper permissions';
            echo '</div>';
        }
        ?>
        
        <h3>Configuration:</h3>
        <table>
            <tr>
                <th>Setting</th>
                <th>Value</th>
            </tr>
            <tr>
                <td>Database Host</td>
                <td><?php echo DB_HOST; ?></td>
            </tr>
            <tr>
                <td>Database Name</td>
                <td><?php echo DB_NAME; ?></td>
            </tr>
            <tr>
                <td>Database User</td>
                <td><?php echo DB_USER; ?></td>
            </tr>
            <tr>
                <td>PHP Version</td>
                <td><?php echo phpversion(); ?></td>
            </tr>
        </table>
        
        <a href="../admin/" class="btn">Go to Admin Panel</a>
        <a href="../contact.html" class="btn">Go to Contact Form</a>
    </div>
</body>
</html>
