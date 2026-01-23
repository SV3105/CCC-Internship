<?php
// easycart/php/signup_handler.php
include '../data/users_data.php';


header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    // Basic validation
    if (empty($name) || empty($email) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'All fields are required.']);
        exit;
    }

    // Check if email already exists
    foreach ($users as $user) {
        if (strtolower($user['email']) === strtolower($email)) {
            echo json_encode(['success' => false, 'message' => 'Email already exists.']);
            exit;
        }
    }

    // Create new user
    $newUser = [
        'id' => count($users) + 1,
        'name' => $name,
        'email' => $email,
        'password' => $password
    ];

    $users[] = $newUser;

    // Save back to users_data.php
    $content = "<?php\n\$users = " . var_export($users, true) . ";\n?>";
    
    if (file_put_contents('../data/users_data.php', $content)) {

        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to save data.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
