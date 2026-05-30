<?php
session_start();
include '../connection.php'; 
$msg=0;
if (isset($_POST['sign'])) {
  $email = $_POST['email'];
  $password = $_POST['password'];
  $sanitized_emailid =  mysqli_real_escape_string($connection, $email);
  $sanitized_password =  mysqli_real_escape_string($connection, $password);

  $sql = "select * from delivery_persons where email='$sanitized_emailid'";
  $result = mysqli_query($connection, $sql);
  $num = mysqli_num_rows($result);
 
  if ($num == 1) {
    while ($row = mysqli_fetch_assoc($result)) {
      if (password_verify($sanitized_password, $row['password'])) {
        $_SESSION['email'] = $email;
        $_SESSION['name'] = $row['name'];
        $_SESSION['Did']=$row['Did'];
        $_SESSION['city']=$row['city'];
        header("location:delivery.php");
      } else {
        $msg = 1;
      }
    }
  } else {
    echo "<script>alert('Account does not exist');</script>";
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delivery Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            /* Vibrant food/delivery themed background image with a dark overlay tint */
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.65)), 
                        url('https://images.unsplash.com/photo-1513104890138-7c749659a591?q=80&w=2070&auto=format&fit=crop') no-repeat center center/cover;
            padding: 20px;
        }

        .center {
            background: rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 40px 35px;
            width: 100%;
            max-width: 410px;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.4);
            text-align: center;
        }

        .center h1 {
            font-size: 2.2rem;
            margin-bottom: 35px;
            font-weight: 700;
            /* Colorful bright orange/red gradient text */
            background: linear-gradient(135deg, #ff9f43, #ff5252);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .txt_field {
            position: relative;
            margin-bottom: 30px;
            text-align: left;
        }

        .txt_field input {
            width: 100%;
            padding: 12px 5px;
            background: transparent;
            border: none;
            border-bottom: 2px solid rgba(255, 255, 255, 0.4);
            outline: none;
            color: #ffffff;
            font-size: 1.05rem;
            transition: all 0.3s ease;
        }

        /* Colorful floating labels */
        .txt_field label {
            position: absolute;
            top: 12px;
            left: 5px;
            color: #ffcccc; 
            font-size: 1rem;
            pointer-events: none;
            transition: all 0.3s ease;
        }

        .txt_field span::before {
            content: '';
            position: absolute;
            top: 40px;
            left: 0;
            width: 0%;
            height: 2px;
            background: #ff9f43;
            transition: .3s;
        }

        /* Triggers label scaling and custom color transitions */
        .txt_field input:focus ~ label,
        .txt_field input:valid ~ label {
            top: -14px;
            font-size: 0.85rem;
            color: #ff9f43;
            font-weight: 600;
        }

        .txt_field input:focus ~ span::before,
        .txt_field input:valid ~ span::before {
            width: 100%;
        }

        .txt_field input:focus {
            border-bottom-color: transparent;
        }

        /* Error state styling */
        .error {
            color: #ff5252;
            font-size: 0.9rem;
            font-weight: 500;
            margin-bottom: 15px;
            text-align: left;
            background: rgba(255, 82, 82, 0.15);
            padding: 8px 12px;
            border-radius: 6px;
            border-left: 3px solid #ff5252;
        }

        input[type="submit"] {
            width: 100%;
            padding: 13px;
            background: linear-gradient(135deg, #ff9f43, #ff5252);
            border: none;
            border-radius: 30px;
            color: white;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 5px 15px rgba(255, 82, 82, 0.35);
            transition: transform 0.2s, box-shadow 0.2s;
            margin-top: 10px;
        }

        input[type="submit"]:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(255, 82, 82, 0.55);
        }

        input[type="submit"]:active {
            transform: translateY(0);
        }

        .signup_link {
            margin-top: 25px;
            color: #e0e0e0;
            font-size: 0.95rem;
        }

        .signup_link a {
            color: #ff9f43;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.2s;
        }

        .signup_link a:hover {
            color: #ff5252;
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <div class="center">
        <h1>Delivery Login</h1>
        
        <form method="post">
            <div class="txt_field">
                <input type="email" name="email" required autocomplete="off"/>
                <span></span>
                <label>Email</label>
            </div>
            
            <div class="txt_field">
                <input type="password" name="password" required/>
                <span></span>
                <label>Password</label>
            </div>
            
            <?php
            if($msg == 1){
                echo '<p class="error">❌ Password does not match.</p>';
            }
            ?>
            
            <input type="submit" value="Login" name="sign">
            
            <div class="signup_link">
                Not a member? <a href="deliverysignup.php">Signup</a>
            </div>
        </form>
    </div>

</body>
</html>