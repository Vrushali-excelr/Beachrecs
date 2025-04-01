<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "school";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get sorting parameters
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'name';
$order = isset($_GET['order']) && $_GET['order'] === 'desc' ? 'DESC' : 'ASC';

// Fetch student records
$sql = "SELECT * FROM students ORDER BY $sort $order";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sort Students</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 20px;
        }
        table {
            width: 50%;
            margin: 20px auto;
            border-collapse: collapse;
            background-color: #fff;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
        }
        form {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }
        select, button {
            padding: 10px;
            margin: 0 5px;
        }
    </style>
</head>
<body>
    <h1 style="text-align:center;">Student Records</h1>
    <form method="GET" action="index2.php">
        <label for="sort">Sort by:</label>
        <select name="sort" id="sort">
            <option value="name" <?php if ($sort == 'name') echo 'selected'; ?>>Name</option>
            <option value="grade" <?php if ($sort == 'grade') echo 'selected'; ?>>Grade</option>
        </select>
        <label for="order">Order:</label>
        <select name="order" id="order">
            <option value="asc" <?php if ($order == 'ASC') echo 'selected'; ?>>Ascending</option>
            <option value="desc" <?php if ($order == 'DESC') echo 'selected'; ?>>Descending</option>
        </select>
        <button type="submit">Sort</button>
    </form>
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Grade</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>{$row['name']}</td>
                            <td>{$row['grade']}</td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='2'>No records found</td></tr>";
            }
            ?>
        </tbody>
    </table>
</body>
</html>

<?php
$conn->close();
?>
