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

    public function __construct(PDO $db, Twig $view)
    {
        $this->db = $db;
        $this->view = $view;
        session_start();
    }

    // Login
    public function login(Request $request, Response $response, $args)
    {
        $loginData = $request->getParsedBody();

        // Verify user credentials (assuming username/password authentication)
        $username = $loginData['username'];
        $password = $loginData['password'];

        // Perform database query to fetch user data based on username
        $stmt = $this->db->prepare('SELECT * FROM users WHERE username = ?');
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
            return $this->view->render($response, 'dashboard.twig');
        } else {
            // Authentication failed, redirect to login page with error message
            return $this->view->render($response, 'login_user.twig', ['error' => 'Invalid credentials']);
        }
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
