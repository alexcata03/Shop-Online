<?php
declare(strict_types=1);

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;
use PDO;

class UserController
{
    private PDO $db;
    private Twig $view;
    private int $sessionDuration; 

    public function __construct(PDO $db, Twig $view)
    {
        $this->db = $db;
        $this->view = $view;
        session_start();
        $this->sessionDuration = 1209600; //14 zile
        $this->checkSessionExpiration();
    }
    
    //Verifica token
    private function checkSessionExpiration()
    {
        if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $this->sessionDuration) {
            // Session expired, destroy session
            session_unset();
            session_destroy();
        }
        $_SESSION['last_activity'] = time(); // Update last activity time
    }

    // Login
    public function login(Request $request, Response $response, $args)
{
    $loginData = $request->getParsedBody();

    // Verify user credentials (assuming username/password authentication)
    $username = $loginData['username'];
    $password = $loginData['password'];

    if (!$username || !$password) {
        return $this->view->render($response->withStatus(400), 'login_user_error.twig', [
            'error' => 'Username and password are required',
            'username' => $username,
            'password' => $password
        ]);
    }

    // Perform database query to fetch user data based on username
    $stmt = $this->db->prepare('SELECT * FROM users WHERE username = ? LIMIT 1');
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    // Check if user exists and if password matches
    if ($user && password_verify($password, $user['password'])) {
        // User authenticated, generate session token
        $sessionToken = uniqid();

        // Store session token and other user data in session
        $_SESSION['sessionToken'] = $sessionToken;
        $_SESSION['userId'] = $user['id']; // Store the user ID in session

        // Redirect to dashboard on successful login
        return $this->view->render($response, 'login_user_success.twig', [
            'username' => $username,
            'password' => $password
        ]);
    } else {
        // Authentication failed, render the login_query_error.twig template
        // to display the error message along with the result of the database query
        $errorMessage = 'Invalid username or password';
        // Check if $user is null (no user found with the provided username)
        // or if password verification failed
        if (!$user) {
            $errorMessage .= ' User not found';
        } else {
            $errorMessage .= ' Password mismatch';
        }
        return $this->view->render($response->withStatus(401), 'login_query_error.twig', [
            'error' => $errorMessage,
            'username' => $username,
            'password' => $password
        ]);
    }
}


    // Method for user registration
    public function register(Request $request, Response $response, $args)
    {
        $registerData = $request->getParsedBody();

        // Extract registration data
        $username = $registerData['username'];
        $email = $registerData['email'];
        $password = password_hash($registerData['password'], PASSWORD_DEFAULT); // Hash the password
        $phone = $registerData['phone'];
        $address = $registerData['address'];
        $userStatus = $registerData['userStatus'];
        $firstName = $registerData['firstName'];
        $lastName = $registerData['lastName'];

        // Prepare and execute the SQL query to insert the new user
        $stmt = $this->db->prepare('INSERT INTO users (username, email, password, phone, address, userStatus, firstName, lastName) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([$username, $email, $password, $phone, $address, $userStatus, $firstName, $lastName]);

        // Render the success template after successful registration
        return $this->view->render($response, 'register_user_success.twig', [
            'username' => $username,
            'email' => $email
        ]);
    }

    // Logout
    public function logout(Request $request, Response $response, $args)
    {
        // Invalidate session token and clear session data
        session_unset();
        session_destroy();

        // Redirect to login page after logout
        return $this->view->render($response, 'login_user.twig', ['message' => 'Logged out successfully']);
    }

    // Method to get all users
    public function getAll(Request $request, Response $response, $args)
    {
        $stmt = $this->db->query('SELECT * FROM users');
        $users = $stmt->fetchAll();

        return $this->view->render($response, 'users.twig', ['users' => $users]);
    }

    // Method to create a new user
    public function createUser(Request $request, Response $response, $args)
    {
        $userData = $request->getParsedBody();

        $stmt = $this->db->prepare('INSERT INTO users (username, email, password, phone, address, userStatus, firstName, lastName) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');

        $stmt->execute([
            $userData['username'],
            $userData['email'],
            $userData['password'],
            $userData['phone'],
            $userData['address'],
            $userData['userStatus'],
            $userData['firstName'],
            $userData['lastName']
        ]);

        return $this->view->render($response, 'create_user_success.twig');
    }

    // Method to update an existing user
    public function updateUser(Request $request, Response $response, $args)
    {
        $username = $args['username'];
        $userData = $request->getParsedBody();

        $stmt = $this->db->prepare('UPDATE users SET email = ?, password = ?, phone = ?, address = ?, userStatus = ?, firstName = ?, lastName = ? WHERE username = ?');

        $stmt->execute([
            $userData['email'],
            $userData['password'],
            $userData['phone'],
            $userData['address'],
            $userData['userStatus'],
            $userData['firstName'],
            $userData['lastName'],
            $username
        ]);

        return $this->view->render($response, 'update_user_success.twig', ['message' => 'User updated successfully']);
    }

    // Method to delete an existing user
    public function deleteUser(Request $request, Response $response, $args)
    {
        $username = $args['username'];

        $stmt = $this->db->prepare('DELETE FROM users WHERE username = ?');
        $stmt->execute([$username]);

        return $this->view->render($response, 'delete_user_success.twig', ['message' => 'User deleted successfully']);
    }
}
?>
