<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database configuration
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'safety_profile';
$table_name = 'user_profiles';

// Create database connection
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create table if not exists with improved schema
$create_table_sql = "CREATE TABLE IF NOT EXISTS $table_name (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    dob DATE,
    gender VARCHAR(10),
    email VARCHAR(50),
    phone VARCHAR(15) NOT NULL UNIQUE,
    sos_lifeguard VARCHAR(15),
    sos_friend VARCHAR(15),
    sos_family VARCHAR(15),
    latitude DECIMAL(10, 8),
    longitude DECIMAL(11, 8),
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX (phone)
)";

if (!$conn->query($create_table_sql)) {
    die("Error creating table: " . $conn->error);
}

// Initialize variables
$save_message = '';
$profile_data = [
    'name' => '',
    'dob' => '',
    'gender' => '',
    'email' => '',
    'phone' => '',
    'sos_lifeguard' => '1234567890', // Default lifeguard number
    'sos_friend' => '',
    'sos_family' => '',
    'latitude' => '',
    'longitude' => ''
];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate input
    $name = trim($_POST['name'] ?? '');
    $dob = trim($_POST['dob'] ?? '');
    $gender = trim($_POST['gender'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $sos_lifeguard = trim($_POST['sos1'] ?? '');
    $sos_friend = trim($_POST['sos2'] ?? '');
    $sos_family = trim($_POST['sos3'] ?? '');
    $latitude = trim($_POST['latitude'] ?? '');
    $longitude = trim($_POST['longitude'] ?? '');
    
    // Basic validation
    if (empty($name) || empty($phone)) {
        $save_message = "Error: Name and phone number are required fields.";
    } else {
        // Check if user with this phone already exists
        $check_sql = "SELECT id FROM $table_name WHERE phone = ?";
        $stmt = $conn->prepare($check_sql);
        $stmt->bind_param("s", $phone);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            // Update existing record
            $update_sql = "UPDATE $table_name SET 
                name=?, dob=?, gender=?, email=?, 
                sos_lifeguard=?, sos_friend=?, sos_family=?,
                latitude=?, longitude=?
                WHERE phone=?";
            
            $stmt = $conn->prepare($update_sql);
            $stmt->bind_param("ssssssssss", 
                $name, $dob, $gender, $email, 
                $sos_lifeguard, $sos_friend, $sos_family,
                $latitude, $longitude, $phone);
        } else {
            // Insert new record
            $insert_sql = "INSERT INTO $table_name 
                (name, dob, gender, email, phone, 
                 sos_lifeguard, sos_friend, sos_family,
                 latitude, longitude) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $conn->prepare($insert_sql);
            $stmt->bind_param("ssssssssss", 
                $name, $dob, $gender, $email, $phone, 
                $sos_lifeguard, $sos_friend, $sos_family,
                $latitude, $longitude);
        }
        
        if ($stmt->execute()) {
            $save_message = "Profile saved successfully!";
        } else {
            $save_message = "Error saving profile: " . $conn->error;
        }
        $stmt->close();
    }
}

// Handle SOS request for a specific user
if (isset($_GET['action']) && $_GET['action'] === 'sos' && isset($_GET['phone'])) {
    $phone = $_GET['phone'];
    
    // Get user data by phone number
    $sql = "SELECT * FROM $table_name WHERE phone = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $phone);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $phoneNumbers = array_filter([
            $user['sos_lifeguard'],
            $user['sos_friend'],
            $user['sos_family']
        ]);
        
        $name = $user['name'] ?: 'User';
        $location = "https://www.google.com/maps?q={$user['latitude']},{$user['longitude']}";
        $message = "$name is in Danger! They need help!\nCurrent Location: $location";
        
        // In a real implementation, you would send SMS here
        // This is just a simulation
        $response = "SOS alert sent to: " . implode(", ", $phoneNumbers);
        
        echo json_encode(['status' => 'success', 'message' => $response]);
        exit;
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No profile data found for this phone number']);
        exit;
    }
}

// Get profile data if phone number is provided in GET request
if (isset($_GET['phone'])) {
    $phone = $_GET['phone'];
    $sql = "SELECT * FROM $table_name WHERE phone = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $phone);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $profile_data = $result->fetch_assoc();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Section</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #2196f3;
            --secondary-color: #1976d2;
            --danger-color: #f44336;
            --success-color: #4caf50;
            --text-color: #333;
            --light-bg: #f5f5f5;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('bg (8).webp') no-repeat center center/cover;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .profile-container {
            background: rgba(255, 255, 255, 0.95);
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 500px;
            position: relative;
            overflow: hidden;
            backdrop-filter: blur(10px);
        }

        .profile-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
        }

        .profile-header {
            text-align: center;
            margin-bottom: 30px;
            position: relative;
        }

        .profile-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: var(--light-bg);
            margin: 0 auto 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
            color: var(--primary-color);
            border: 3px solid var(--primary-color);
        }

        h1 {
            color: var(--primary-color);
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .form-group {
            margin-bottom: 20px;
            position: relative;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--text-color);
            font-size: 14px;
        }

        input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        input:focus {
            border-color: var(--primary-color);
            outline: none;
            box-shadow: 0 0 0 3px rgba(33, 150, 243, 0.1);
        }

        .sos-section {
            margin-top: 30px;
            padding-top: 30px;
            border-top: 2px solid #e0e0e0;
        }

        .sos-section h2 {
            color: var(--danger-color);
            font-size: 18px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .sos-section h2 i {
            font-size: 20px;
        }

        .sos-button {
            width: 100%;
            padding: 15px;
            background: var(--danger-color);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            margin-top: 20px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .sos-button:hover {
            background: #d32f2f;
            transform: translateY(-2px);
        }

        .sos-button:disabled {
            background: #ccc;
            cursor: not-allowed;
            transform: none;
        }

        .location-link {
            text-align: center;
            margin-top: 20px;
            padding: 15px;
            background: var(--light-bg);
            border-radius: 8px;
        }

        .location-link a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .location-link a:hover {
            text-decoration: underline;
        }

        .save-indicator {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: var(--success-color);
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            font-size: 14px;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .save-indicator.show {
            opacity: 1;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        .sos-button:not(:disabled):hover {
            animation: pulse 1s infinite;
        }

        .gender-group {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }

        .gender-option {
            flex: 1;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            cursor: pointer;
            text-align: center;
            transition: all 0.3s ease;
        }

        .gender-option:hover {
            border-color: var(--primary-color);
            background: rgba(33, 150, 243, 0.1);
        }

        .gender-option.selected {
            border-color: var(--primary-color);
            background: rgba(33, 150, 243, 0.1);
        }
        
        .save-button {
            width: 100%;
            padding: 15px;
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            margin-top: 20px;
            transition: all 0.3s ease;
        }
        
        .save-button:hover {
            background: var(--secondary-color);
        }
        
        .alert-message {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
            text-align: center;
        }
        
        .success {
            background-color: #dff0d8;
            color: #3c763d;
            border: 1px solid #d6e9c6;
        }
        
        .error {
            background-color: #f2dede;
            color: #a94442;
            border: 1px solid #ebccd1;
        }
        
        .user-search {
            margin-bottom: 20px;
        }
        
        .search-container {
            display: flex;
            gap: 10px;
        }
        
        .search-container input {
            flex: 1;
        }
        
        .search-container button {
            padding: 0 15px;
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="profile-container">
        <div class="profile-header">
            <div class="profile-avatar">
                <i class="fas fa-user"></i>
            </div>
            <h1>Profile Section</h1>
        </div>

        <?php if (!empty($save_message)): ?>
            <div class="alert-message <?php echo strpos($save_message, 'Error') !== false ? 'error' : 'success'; ?>">
                <?php echo htmlspecialchars($save_message); ?>
            </div>
        <?php endif; ?>

        <div class="user-search">
            <form method="get" id="searchForm">
                <div class="search-container">
                    <input type="tel" name="phone" placeholder="Enter phone number to search" 
                           value="<?php echo htmlspecialchars($_GET['phone'] ?? ''); ?>">
                    <button type="submit"><i class="fas fa-search"></i></button>
                </div>
            </form>
        </div>

        <form method="post" id="profileForm">
            <input type="hidden" id="latitude" name="latitude" value="<?php echo htmlspecialchars($profile_data['latitude'] ?? ''); ?>">
            <input type="hidden" id="longitude" name="longitude" value="<?php echo htmlspecialchars($profile_data['longitude'] ?? ''); ?>">
            
            <div class="form-group">
                <label for="name"><i class="fas fa-user-circle"></i> Name</label>
                <input type="text" id="name" name="name" placeholder="Enter your name" 
                       value="<?php echo htmlspecialchars($profile_data['name'] ?? ''); ?>" required>
            </div>

            <div class="form-group">
                <label for="dob"><i class="fas fa-calendar"></i> Date of Birth</label>
                <input type="date" id="dob" name="dob" 
                       value="<?php echo htmlspecialchars($profile_data['dob'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label><i class="fas fa-venus-mars"></i> Gender</label>
                <div class="gender-group">
                    <div class="gender-option <?php echo ($profile_data['gender'] ?? '') === 'male' ? 'selected' : ''; ?>" 
                         onclick="selectGender('male')">Male</div>
                    <div class="gender-option <?php echo ($profile_data['gender'] ?? '') === 'female' ? 'selected' : ''; ?>" 
                         onclick="selectGender('female')">Female</div>
                    <div class="gender-option <?php echo ($profile_data['gender'] ?? '') === 'other' ? 'selected' : ''; ?>" 
                         onclick="selectGender('other')">Other</div>
                </div>
                <input type="hidden" id="gender" name="gender" value="<?php echo htmlspecialchars($profile_data['gender'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="email"><i class="fas fa-envelope"></i> Email</label>
                <input type="email" id="email" name="email" placeholder="Enter your email" 
                       value="<?php echo htmlspecialchars($profile_data['email'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="phone"><i class="fas fa-phone"></i> Phone Number</label>
                <input type="tel" id="phone" name="phone" placeholder="Enter your phone number" 
                       value="<?php echo htmlspecialchars($profile_data['phone'] ?? ''); ?>" required>
            </div>

            <div class="sos-section">
                <h2><i class="fas fa-exclamation-triangle"></i> SOS Information</h2>
                <div class="form-group">
                    <label for="sos1">Lifeguard</label>
                    <input type="tel" id="sos1" name="sos1" value="<?php echo htmlspecialchars($profile_data['sos_lifeguard'] ?? '1234567890'); ?>" required>
                </div>

                <div class="form-group">
                    <label for="sos2">Friend</label>
                    <input type="tel" id="sos2" name="sos2" placeholder="Enter phone number" 
                           value="<?php echo htmlspecialchars($profile_data['sos_friend'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="sos3">Family</label>
                    <input type="tel" id="sos3" name="sos3" placeholder="Enter phone number" 
                           value="<?php echo htmlspecialchars($profile_data['sos_family'] ?? ''); ?>">
                </div>
            </div>

            <button type="submit" class="save-button">
                <i class="fas fa-save"></i> Save Profile
            </button>
            
            <?php if (!empty($profile_data['phone'])): ?>
            <button type="button" class="sos-button" onclick="sendSOS('<?php echo htmlspecialchars($profile_data['phone']); ?>')">
                <i class="fas fa-exclamation-circle"></i>
                Send SOS Alert
            </button>
            <?php else: ?>
            <button type="button" class="sos-button" disabled>
                <i class="fas fa-exclamation-circle"></i>
                Save profile to enable SOS
            </button>
            <?php endif; ?>
            
            <div class="location-link" id="locationLink">
                <?php if (!empty($profile_data['latitude']) && !empty($profile_data['longitude'])): ?>
                    Your Current Location: 
                    <a href="https://www.google.com/maps?q=<?php echo $profile_data['latitude']; ?>,<?php echo $profile_data['longitude']; ?>" target="_blank">
                        <i class="fas fa-map-marker-alt"></i> Click Here
                    </a>
                <?php else: ?>
                    Location not available
                <?php endif; ?>
            </div>
        </form>
    </div>

    <div class="save-indicator" id="saveIndicator">Changes saved!</div>

    <script>
        // Get current location when page loads
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    document.getElementById('latitude').value = position.coords.latitude;
                    document.getElementById('longitude').value = position.coords.longitude;
                    
                    // Update location link
                    const locationURL = `https://www.google.com/maps?q=${position.coords.latitude},${position.coords.longitude}`;
                    document.getElementById('locationLink').innerHTML = 
                        `Your Current Location: <a href="${locationURL}" target="_blank"><i class="fas fa-map-marker-alt"></i> Click Here</a>`;
                },
                function(error) {
                    console.error("Geolocation error:", error);
                }
            );
        } else {
            console.log("Geolocation is not supported by this browser.");
        }

        function validatePhone(phone) {
            return /^[6-9]\d{9}$/.test(phone);
        }

        async function sendSOS(phone) {
            // Validate SOS numbers
            const phoneNumbers = ['sos1', 'sos2', 'sos3']
                .map(id => document.getElementById(id).value)
                .filter(validatePhone);

            if (phoneNumbers.length === 0) {
                alert("No valid SOS numbers entered. Please add valid Indian phone numbers.");
                return;
            }

            const sosButton = document.querySelector('.sos-button');
            sosButton.disabled = true;
            sosButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending SOS...';

            try {
                const response = await fetch(`?action=sos&phone=${encodeURIComponent(phone)}`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json'
                    }
                });
                
                const result = await response.json();
                
                if (result.status === 'success') {
                    alert(result.message);
                } else {
                    alert('Error: ' + result.message);
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Failed to send SOS. Please check your connection.');
            } finally {
                sosButton.disabled = false;
                sosButton.innerHTML = '<i class="fas fa-exclamation-circle"></i> Send SOS Alert';
            }
        }

        function showSaveIndicator() {
            const indicator = document.getElementById('saveIndicator');
            indicator.classList.add('show');
            setTimeout(() => indicator.classList.remove('show'), 2000);
        }

        document.querySelectorAll('input').forEach(input => {
            input.addEventListener('input', () => {
                showSaveIndicator();
            });
        });

        function selectGender(gender) {
            document.getElementById('gender').value = gender;
            document.querySelectorAll('.gender-option').forEach(option => {
                option.classList.remove('selected');
            });
            event.target.classList.add('selected');
            showSaveIndicator();
        }
    </script>
</body>
</html>
<?php
$conn->close();
?>