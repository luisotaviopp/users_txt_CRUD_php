<?php

// Function to write user data to a text file
function writeUserData(array $userData): void {
    $data = '';
    foreach ($userData as $user) {
        $data .= $user['id'] . ',' . $user['timestamp'] . ',' . $user['latitude'] . ',' . $user['longitude'] . PHP_EOL;
    }
    file_put_contents('user_locations.txt', $data);
}

// Function to read user data from the text file
function readUserData(): array {
    $userData = [];
    if (file_exists('user_locations.txt')) {
        $file = fopen('user_locations.txt', 'r');
        while (($line = fgets($file)) !== false) {
            $data = explode(',', $line);
            $id = $data[0];
            $timestamp = $data[1];
            $latitude = $data[2];
            $longitude = $data[3];
            $userData[$id] = [
                'id' => $id,
                'timestamp' => $timestamp,
                'latitude' => $latitude,
                'longitude' => $longitude
            ];
        }
        fclose($file);
    }
    return $userData;
}

// Function to export user data to JSON
function exportToJson(array $userData): void {
    $json = json_encode(array_values($userData), JSON_PRETTY_PRINT);
    file_put_contents('user_locations.json', $json);
}

// Function to list all users
function listAllUsers(array $userData): void {
    foreach ($userData as $user) {
        echo "User ID: " . $user['id'] . PHP_EOL;
        echo "Timestamp: " . $user['timestamp'] . PHP_EOL;
        echo "Latitude: " . $user['latitude'] . PHP_EOL;
        echo "Longitude: " . $user['longitude'] . PHP_EOL . PHP_EOL;
    }
}

// Function to update user data
function updateUserData(int $id, float $latitude, float $longitude): void {
    $userData = readUserData();
    if (isset($userData[$id])) {
        unset($userData[$id]);
    }
    $timestamp = date('d-m-Y H:i:s');
    $userData[$id] = [
        'id' => $id,
        'timestamp' => $timestamp,
        'latitude' => $latitude,
        'longitude' => $longitude
    ];
    writeUserData($userData);
}

// Function to append or create user data
function appendOrCreateUserData(int $id, float $latitude, float $longitude): void {
    $userData = readUserData();
    if (isset($userData[$id])) {
        unset($userData[$id]);
    }
    $timestamp = date('d-m-Y H:i:s');
    $userData[$id] = [
        'id' => $id,
        'timestamp' => $timestamp,
        'latitude' => $latitude,
        'longitude' => $longitude
    ];
    writeUserData($userData);
}

// Function to delete user data
function deleteUserData(int $id): void {
    $userData = readUserData();
    if (isset($userData[$id])) {
        unset($userData[$id]);
        writeUserData($userData);
        echo "User data deleted successfully." . PHP_EOL;
    } else {
        echo "User not found." . PHP_EOL;
    }
}

// Function to filter user data by ID
function filterUserData(int $id): array {
    $userData = readUserData();
    $filteredData = array_filter($userData, fn($user) => $user['id'] == $id);
    return $filteredData;
}

// Haversine formula to calculate distance between two points
function calculateDistance(float $lat1, float $lon1, float $lat2, float $lon2): float {
    $earthRadius = 6371; // Earth's radius in kilometers

    $lat1Rad = deg2rad($lat1);
    $lon1Rad = deg2rad($lon1);
    $lat2Rad = deg2rad($lat2);
    $lon2Rad = deg2rad($lon2);

    $deltaLat = $lat2Rad - $lat1Rad;
    $deltaLon = $lon2Rad - $lon1Rad;

    $a = sin($deltaLat / 2) * sin($deltaLat / 2) +
        cos($lat1Rad) * cos($lat2Rad) *
        sin($deltaLon / 2) * sin($deltaLon / 2);

    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

    $distance = $earthRadius * $c;
    return $distance;
}

// Function to list users sorted by distance to the reference latitude and longitude
function listUsersByDistance(array $userData, float $referenceLatitude, float $referenceLongitude): void {
    $distances = [];
    foreach ($userData as $user) {
        $distance = calculateDistance($referenceLatitude, $referenceLongitude, $user['latitude'], $user['longitude']);
        $distances[$user['id']] = $distance;
    }

    asort($distances);

    foreach ($distances as $id => $distance) {
        $user = $userData[$id];
        echo "User ID: " . $user['id'] . PHP_EOL;
        echo "Distance: " . $distance . " km" . PHP_EOL;
        echo "Timestamp: " . $user['timestamp'] . PHP_EOL;
        echo "Latitude: " . $user['latitude'] . PHP_EOL;
        echo "Longitude: " . $user['longitude'] . PHP_EOL . PHP_EOL;
    }
}

// Create an empty user_locations.txt file if it doesn't exist
if (!file_exists('user_locations.txt')) {
    file_put_contents('user_locations.txt', '');
}

// Test the functions
$referenceLatitude = 40.7128;
$referenceLongitude = -74.0060;

// Write user data
writeUserData([
    [
        'id' => 1,
        'timestamp' => date('d-m-Y H:i:s'),
        'latitude' => 40.7128,
        'longitude' => -74.0060
    ],
    [
        'id' => 2,
        'timestamp' => date('d-m-Y H:i:s'),
        'latitude' => 37.7749,
        'longitude' => -122.4194
    ],
    [
        'id' => 3,
        'timestamp' => date('d-m-Y H:i:s'),
        'latitude' => 51.5074,
        'longitude' => -0.1278
    ],
]);

// Read user data
$userData = readUserData();

// List all users
listAllUsers($userData);

// Export user data to JSON
exportToJson($userData);

// Update user data
updateUserData(2, 35.6895, 139.6917);

// Append or create user data
appendOrCreateUserData(4, 41.8781, -87.6298);

// Delete user data
deleteUserData(1);

// Filter user data
$filteredData = filterUserData(3);
var_dump($filteredData);

// List users by distance
listUsersByDistance($userData, $referenceLatitude, $referenceLongitude);
