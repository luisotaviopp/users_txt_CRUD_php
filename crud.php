<?php
// Function to write user data to a text file
function writeUserData($userData) {
    $mode = file_exists('user_data.txt') ? FILE_APPEND : 0;
    $data = '';
    foreach ($userData as $user) {
        $data .= $user['userId'] . ',' . $user['latitude'] . ',' . $user['longitude'] . "\n";
    }
    file_put_contents('user_data.txt', $data, $mode);
}

// Function to read user data from the text file
function readUserData() {
    $userData = array();
    if (file_exists('user_data.txt')) {
        $file = fopen('user_data.txt', 'r');
        while (($line = fgets($file)) !== false) {
            $data = explode(',', $line);
            $userId = $data[0];
            $latitude = $data[1];
            $longitude = $data[2];
            $userData[] = array(
                'userId' => $userId,
                'latitude' => $latitude,
                'longitude' => $longitude
            );
        }
        fclose($file);
    }
    return $userData;
}

// Function to export user data to JSON
function exportToJson($userData) {
    $json = json_encode($userData, JSON_PRETTY_PRINT);
    file_put_contents('user_data.json', $json);
}

// Function to list all users
function listAllUsers($userData) {
    foreach ($userData as $user) {
        echo "User ID: " . $user['userId'] . "\n";
        echo "Latitude: " . $user['latitude'] . "\n";
        echo "Longitude: " . $user['longitude'] . "\n\n";
    }
}

// Function to update user data
function updateUserData($userId, $latitude, $longitude) {
    $userData = readUserData();
    foreach ($userData as &$user) {
        if ($user['userId'] == $userId) {
            $user['latitude'] = $latitude;
            $user['longitude'] = $longitude;
        }
    }
    writeUserData($userData);
}

// Function to delete user data
function deleteUserData($userId) {
    $userData = readUserData();
    $filteredData = array_filter($userData, function ($user) use ($userId) {
        return $user['userId'] != $userId;
    });
    writeUserData($filteredData);
}

// Function to filter user data
function filterUserData($latitude) {
    $userData = readUserData();
    $filteredData = array_filter($userData, function ($user) use ($latitude) {
        return $user['latitude'] == $latitude;
    });
    return $filteredData;
}

// Example usage:
$users = array(
    array('userId' => 1, 'latitude' => 40.7128, 'longitude' => -74.0060),
    array('userId' => 2, 'latitude' => 34.0522, 'longitude' => -118.2437),
    array('userId' => 3, 'latitude' => 51.5074, 'longitude' => -0.1278)
);

writeUserData($users); // Write user data to the file

$additionalUsers = array(
    array('userId' => 4, 'latitude' => 35.6895, 'longitude' => 139.6917),
    array('userId' => 5, 'latitude' => 48.8566, 'longitude' => 2.3522)
);

writeUserData($additionalUsers); // Append additional user data to the file

$allUsers = readUserData(); // Read user data from the file

listAllUsers($allUsers); // List all users

exportToJson($allUsers); // Export user data to JSON

// Update user data
updateUserData(2, 37.7749, -122.4194);

// Delete user data
deleteUserData(3);

// Filter user data
$filteredUsers = filterUserData(40.7128);
listAllUsers($filteredUsers);
