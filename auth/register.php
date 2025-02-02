<?php
include '../includes/connect.php';

$message = "";
$toastClass = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

      // Hash the password
      $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Check if email already exists
    $checkEmailStmt = $conn->prepare("SELECT email FROM users WHERE email = ?");
    $checkEmailStmt->bind_param("s", $email);
    $checkEmailStmt->execute();
    $checkEmailStmt->store_result();

    if ($checkEmailStmt->num_rows > 0) {
        $message = "Email ID already exists";
        $toastClass = "primary"; // Primary color
    } else {
        // Prepare and bind
        $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $email, $hashedPassword);

        if ($stmt->execute()) {
            $message = "Account created successfully";
            $toastClass = "success"; // Success color
        } else {
            $message = "Error: " . $stmt->error;
            $toastClass = "danger"; // Danger color
        }

        $stmt->close();
    }
  
    $checkEmailStmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assets/css/register.css">
    <title>Registration</title>
    <style>
        /* Custom Toast Styles */
        .custom-toast {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: #333;
            color: #fff;
            padding: 10px 15px;
            border-radius: 5px;
            max-width: 300px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            opacity: 1;
            transition: opacity 0.5s ease;
            z-index: 1000;
            font-size: 14px;
            font-weight: 500;
        }
        .custom-toast.primary { background-color: #007bff; }
        .custom-toast.success { background-color: #28a745; }
        .custom-toast.danger { background-color: #dc3545; }

        .close-btn {
            background: none;
            border: none;
            color: #fff;
            font-size: 16px;
            cursor: pointer;
            margin-left:5px;
        }
    </style>
</head>

<body>
<?php if ($message): ?>
    <div id="toast" class="custom-toast <?php echo $toastClass; ?>">
        <div class="custom-toast-content">
            <span><?php echo $message; ?></span>
            <button class="close-btn" onclick="closeToast()">x</button>
        </div>
    </div>
    
<?php endif; ?>

<section class="register-container">
    <div class="register-inputs">
        <h1>Gratafy</h1>
        <form action="" method="post">
            <input name="username" id="username" placeholder="Username" type="text" required />
            <input name="email" id="email" placeholder="Example@gmail.com" type="email" required />
            <div class="password"> <input name="password" id="password" placeholder="Password" type="password" id="password" required />
          <img src="../assets/images/view.png" alt="view-btn" class="view" id="view">
          </div>
            <button type="submit">Register</button>
        </form>
        <a class="forget" href="./login.php">Already have an account? Sign in</a>
    </div>
</section>

<script>
    // Auto-hide the toast after 3 seconds
    function closeToast() {
        const toast = document.getElementById('toast');
        if (toast) {
            toast.style.opacity = '0'; // Fade out
            setTimeout(() => {
                toast.style.display = 'none';
            }, 500); // Wait for the fade-out transition to complete
        }
    }

    // Show the toast and set it to auto-close
    window.onload = () => {
        setTimeout(closeToast, 3000); // Automatically close after 3 seconds
    };

    const view = document.getElementById('view'); 
    const password = document.getElementById('password');
    password.addEventListener('input', () => {
    // Check if the password input is not empty
    if (password.value.trim() !== "") {
        view.style.display = "block"; // Show the icon
    } else {
        view.style.display = "none"; // Hide the icon
    }
});

view.addEventListener('click',()=>{
    if(password.type === "password"){
        password.type ="text";
        console.log('pass');
        view.src = "../assets/images/hide.png";

    }else{
        password.type ="password";
        console.log('word');
        view.src = "../assets/images/view.png";
    }
});
</script>
</body>

</html>
