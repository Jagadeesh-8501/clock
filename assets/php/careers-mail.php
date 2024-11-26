<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Collect and sanitize form inputs
    $name = htmlspecialchars(strip_tags(trim($_POST['name'])));
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $phone = htmlspecialchars(strip_tags(trim($_POST['phone'])));
    $position = htmlspecialchars(strip_tags(trim($_POST['position'])));
    $experience = htmlspecialchars(strip_tags(trim($_POST['experience'])));
    $portfolio = htmlspecialchars(strip_tags(trim($_POST['portfolio'])));
    $message = htmlspecialchars(strip_tags(trim($_POST['message'])));
    
    // File upload setup
    $upload_dir = 'uploads/';
    $allowed_types = ['pdf', 'doc', 'docx'];
    $resume = $_FILES['resume'];

    // Ensure upload directory exists
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    $resume_name = basename($resume['name']);
    $resume_tmp_name = $resume['tmp_name'];
    $resume_size = $resume['size'];
    $resume_error = $resume['error'];
    $resume_ext = strtolower(pathinfo($resume_name, PATHINFO_EXTENSION));
    $resume_path = $upload_dir . uniqid() . '-' . $resume_name;

    // Validate resume file
    if ($resume_error === 0) {
        if (in_array($resume_ext, $allowed_types)) {
            if ($resume_size <= 5000000) { // 5MB max size
                move_uploaded_file($resume_tmp_name, $resume_path);
            } else {
                die('File size exceeds the limit of 5MB.');
            }
        } else {
            die('Invalid file type. Only PDF, DOC, or DOCX are allowed.');
        }
    } else {
        die('Error uploading resume.');
    }

    // Email setup
    $to = 'your-email@example.com'; // Replace with your email
    $subject = 'New Career Application - ' . $position;
    $headers = "From: $name <$email>\r\n";
    $headers .= "Reply-To: $email\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

    $email_body = "
        <h1>New Career Application</h1>
        <p><strong>Name:</strong> $name</p>
        <p><strong>Email:</strong> $email</p>
        <p><strong>Phone:</strong> $phone</p>
        <p><strong>Position:</strong> $position</p>
        <p><strong>Experience:</strong> $experience</p>
        <p><strong>Portfolio/LinkedIn:</strong> $portfolio</p>
        <p><strong>Cover Letter / Additional Information:</strong></p>
        <p>$message</p>
        <p><strong>Resume:</strong> <a href='$resume_path' download>Download Resume</a></p>
    ";

    // Send email
    if (mail($to, $subject, $email_body, $headers)) {
        echo 'Your application has been submitted successfully.';
    } else {
        echo 'Failed to send the application. Please try again later.';
    }
} else {
    echo 'Invalid request method.';
}
?>
