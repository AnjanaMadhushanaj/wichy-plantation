<?php


// Use shared config for database connection
require_once 'config.php';


// ---- 🚀 FORM SUBMISSION HANDLING ----
// Check if the form was submitted using the POST method
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Get the data from the form and store it in variables
    // Use htmlspecialchars() to prevent basic XSS attacks

    $user_name = htmlspecialchars($_POST['user_name']);
    $comment = htmlspecialchars($_POST['user_comment']);

    // Insert into 'comments' table (no email field)
    $stmt = $conn->prepare("INSERT INTO comments (user_name, comment) VALUES (?, ?)");
    $stmt->bind_param("ss", $user_name, $comment);

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