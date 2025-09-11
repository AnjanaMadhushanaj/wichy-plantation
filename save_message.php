<?php

// ---- 🔌 DATABASE CONNECTION DETAILS ----
// Enter your database details here
$servername = "localhost";      // Usually "localhost"
$username = "your_db_username"; // Your database username
$password = "your_db_password"; // Your database password
$dbname = "your_db_name";       // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    // If the connection fails, show an error and stop the script
    die("Connection failed: " . $conn->connect_error);
}


// ---- 🚀 FORM SUBMISSION HANDLING ----
// Check if the form was submitted using the POST method
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Get the data from the form and store it in variables
    // Use htmlspecialchars() to prevent basic XSS attacks
    $name = htmlspecialchars($_POST['user_name']);
    $email = htmlspecialchars($_POST['user_email']);
    $comment = htmlspecialchars($_POST['user_comment']);

    // ---- 🛡️ PREPARED STATEMENT (To prevent SQL Injection) ----
    // 1. Prepare the SQL query. Use question marks (?) as placeholders for values.
    $stmt = $conn->prepare("INSERT INTO messages (name, email, comment) VALUES (?, ?, ?)");

    // 2. Bind the variables to the prepared statement.
    // "sss" means that all three variables are of the String type.
    $stmt->bind_param("sss", $name, $email, $comment);

    // 3. Execute the statement (this inserts the data into the database)
    if ($stmt->execute()) {
        echo "<h1>Thank You!</h1>";
        echo "<p>Your message has been received successfully.</p>";
    } else {
        echo "<h1>Error!</h1>";
        echo "<p>Something went wrong. Please try again later.</p>";
        // For debugging, you can uncomment the line below to see the specific error:
        // echo "Error: " . $stmt->error;
    }

    // Close the statement
    $stmt->close();
}

// Close the database connection
$conn->close();

?>