<?php
//index.php

//Include all necessary files
require_once 'includes/dbconnection.php';
require_once 'includes/functions.php';

//Check for user authentication and start session
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}

//Page title
$page_title = 'Lifestyle Coaching Service';

//include header
include 'includes/header.php';

//include navbar
include 'includes/navbar.php';

//include content
include 'includes/content.php';

//include footer
include 'includes/footer.php';

?>


//dbconnection.php

<?php

//Define constants for database connection
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'lifestyle');

// Connect to database
$db = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if (mysqli_connect_errno()) {
    echo 'Failed to connect to MySQL: ' . mysqli_connect_error();
    die();
}

?>


//functions.php

<?php

//Function to check if user is logged in
function is_logged_in() {
    if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
        return true;
    }
    return false;
}

//Function to check if user is admin
function is_admin() {
    if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true) {
        return true;
    }
    return false;
}

//Function to get user details
function get_user_details($user_id) {
    global $db;

    //Prepare query
    $query = "SELECT * FROM users WHERE id = $user_id";

    //Execute query
    $result = mysqli_query($db, $query);

    //Check if query is successful
    if ($result) {
        //Fetch user details
        return mysqli_fetch_assoc($result);
    }
    return array();
}

//Function to create user sessions
function create_user_session($user_id, $is_admin){
    //Assign user_id to session
    $_SESSION['user_id'] = $user_id;

    //Assign admin status to session
    $_SESSION['is_admin'] = $is_admin;
}

//Function to validate user login
function validate_login($username, $password){
    global $db;

    //Prepare query
    $query = "SELECT * FROM users WHERE user_name = '$username'";

    //Execute query
    $result = mysqli_query($db, $query);

    //Check if query is successful
    if ($result) {
        //Fetch user details
        $user_details = mysqli_fetch_assoc($result);

        //Check if password matches the hashed password
        if (password_verify($password, $user_details['password'])) {
            return $user_details;
        }
    }
    return false;
}

//Function to register a user
function register($username, $password){
    global $db;

    //Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    //Prepare query
    $query = "INSERT INTO users (user_name, password) VALUES ('$username', '$hashed_password')";

    //Execute query
    $result = mysqli_query($db, $query);

    //Check if query is successful
    if ($result) {
        //Return the insert id
        return mysqli_insert_id($db);
    }
    return false;
}

//Function to add a coaching session
function add_session($user_id, $coach_id, $start_date, $end_date, $topic, $description){
    global $db;

    //Prepare query
    $query = "INSERT INTO coaching_sessions (user_id, coach_id, start_date, end_date, topic, description) VALUES ($user_id, $coach_id, '$start_date', '$end_date', '$topic', '$description')";

    //Execute query
    $result = mysqli_query($db, $query);

    //Check if query is successful
    if ($result) {
        //Return the insert id
        return mysqli_insert_id($db);
    }
    return false;
}

//Function to get sessions for a coach
function get_coach_sessions($coach_id){
    global $db;

    //Prepare query
    $query = "SELECT * FROM coaching_sessions WHERE coach_id = $coach_id";

    //Execute query
    $result = mysqli_query($db, $query);

    //Check if query is successful
    if ($result) {
        //Fetch sessions
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }
    return array();
}

//Function to get all coaches
function get_all_coaches(){
    global $db;

    //Prepare query
    $query = "SELECT * FROM coaches";

    //Execute query
    $result = mysqli_query($db, $query);

    //Check if query is successful
    if ($result) {
        //Fetch coaches
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }
    return array();
}

//Function to get coach details
function get_coach_details($coach_id){
    global $db;

    //Prepare query
    $query = "SELECT * FROM coaches WHERE id = $coach_id";

    //Execute query
    $result = mysqli_query($db, $query);

    //Check if query is successful
    if ($result) {
        //Fetch coach details
        return mysqli_fetch_assoc($result);
    }
    return array();
}

//Function to delete user
function delete_user($user_id){
    global $db;

    //Prepare query
    $query = "DELETE FROM users WHERE id = $user_id";

    //Execute query
    $result = mysqli_query($db, $query);

    //Check if query is successful
    if ($result) {
        return true;
    }
    return false;
}

//Function to delete coach
function delete_coach($coach_id){
    global $db;

    //Prepare query
    $query = "DELETE FROM coaches WHERE id = $coach_id";

    //Execute query
    $result = mysqli_query($db, $query);

    //Check if query is successful
    if ($result) {
        return true;
    }
    return false;
}

//Function to delete session
function delete_session($session_id){
    global $db;

    //Prepare query
    $query = "DELETE FROM coaching_sessions WHERE id = $session_id";

    //Execute query
    $result = mysqli_query($db, $query);

    //Check if query is successful
    if ($result) {
        return true;
    }
    return false;
}

?>


//header.php

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?= $page_title ?></title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<h1><?= $page_title ?></h1>


//navbar.php

<?php if (is_logged_in()) : ?>
    <nav>
        <ul>
            <li><a href="index.php">Home</a></li>
            <?php if (is_admin()) : ?>
                <li><a href="coaches.php">Manage Coaches</a></li>
            <?php endif; ?>
            <li><a href="sessions.php">My Sessions</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </nav>
<?php else : ?>
    <nav>
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="login.php">Login</a></li>
            <li><a href="register.php">Register</a></li>
        </ul>
    </nav>
<?php endif; ?>


//content.php

<?php
//Check if user is logged in
if (is_logged_in()) {
    //If user is logged in, show welcome message
    $user_details = get_user_details($_SESSION['user_id']);
    echo '<h3>Welcome ' . $user_details['name'] . '</h3>';
}
?>

<p>Lifestyle Coaching Service provides personal coaching to help people improve their lifestyle habits.</p>
<p>Our coaches are experienced professional with years of experience in health, wellness and lifestyle coaching.</p>
<p>We offer individual and group coaching sessions to help people achieve their goals and improve their lives.</p>


//footer.php

<footer>
    <p>&copy; <?= date('Y') ?> Lifestyle Coaching Service</p>
</footer>

</body>
</html>