<?php
// File to store the count
$counterFile = 'counter.txt';

// Check if the file exists
if (!file_exists($counterFile)) {
    // Create the file and initialize it with 0 if it doesn't exist
    file_put_contents($counterFile, '0');
}

// Read the current count
$visitorCount = file_get_contents($counterFile);

// Increment the count by 1
$visitorCount++;

// Write the new count back to the file
file_put_contents($counterFile, $visitorCount);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visitor Counter</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background: linear-gradient(135deg, #f5a623, #f093fb);
            color: white;
        }
        .container {
            background: rgba(0, 0, 0, 0.5);
            padding: 30px;
            border-radius: 10px;
        }
        h1 {
            font-size: 3em;
            margin: 0 0 20px;
        }
        p {
            font-size: 1.5em;
            margin: 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Visitors visisted to my Website</h1>
        <p>Total Visitors: <?php echo $visitorCount; ?></p>
    </div>
</body>
</html>
