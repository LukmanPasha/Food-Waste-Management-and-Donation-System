<?php
// session_start();
// $connection=mysqli_connect("localhost:3307","root","");
// $db=mysqli_select_db($connection,'demo');
include '../connection.php';
$msg=0;
if(isset($_POST['sign']))
{
    $username=$_POST['username'];
    $email=$_POST['email'];
    $password=$_POST['password'];
    $location=$_POST['district'];

    $pass=password_hash($password,PASSWORD_DEFAULT);
    $sql="select * from delivery_persons where email='$email'" ;
    $result= mysqli_query($connection, $sql);
    $num=mysqli_num_rows($result);
    if($num==1){
        echo "<script>alert('Account already exists');</script>";
    }
    else{
        $query="insert into delivery_persons(name,email,password,city) values('$username','$email','$pass','$location')";
        $query_run= mysqli_query($connection, $query);
        if($query_run)
        {
            header("location:delivery.php");
        }
        else{
            echo '<script type="text/javascript">alert("data not saved")</script>';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delivery Register</title>
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
            /* Vibrant background image layout with a sleek dark overlay tint */
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.65)), 
                        url('https://images.unsplash.com/photo-1513104890138-7c749659a591?q=80&w=2070&auto=format&fit=crop') no-repeat center center/cover;
            padding: 40px 20px;
        }

        .center {
            background: rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 40px 35px;
            width: 100%;
            max-width: 420px;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.4);
            text-align: center;
        }

        .center h1 {
            font-size: 2.2rem;
            margin-bottom: 35px;
            font-weight: 700;
            /* Bright colorful orange-to-red gradient headline text */
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

        /* Colorful floating text labels */
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

        /* Handles label sliding and layout expansion transitions */
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

        /* Customized dark container for selection element */
        .select_container {
            margin-bottom: 20px;
            text-align: left;
        }

        .select_container select {
            width: 100%;
            padding: 12px 15px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 8px;
            color: #ffffff;
            font-size: 1rem;
            outline: none;
            cursor: pointer;
            transition: all 0.3s;
        }

        .select_container select:focus {
            border-color: #ff9f43;
            background: rgba(0, 0, 0, 0.85);
        }

        /* Forces dropdown choice options to be clearly visible against the glass style */
        .select_container select option {
            background-color: #1e1e1e;
            color: #ffffff;
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
        <h1>Register</h1>
        <form method="post" action="">
            
            <div class="txt_field">
                <input type="text" name="username" required autocomplete="off"/>
                <span></span>
                <label>Username</label>
            </div>
            
            <div class="txt_field">
                <input type="password" name="password" required/>
                <span></span>
                <label>Password</label>
            </div>
            
            <div class="txt_field">
                <input type="email" name="email" required autocomplete="off"/>
                <span></span>
                <label>Email</label>
            </div>
            
            <div class="select_container">
                <select id="district" name="district">
                    <option value="bengaluru" selected>Bengaluru</option>
                    <option value="mysuru">Mysuru</option>
                    <option value="hubli-dharwad">Hubli-Dharwad</option>
                    <option value="mangaluru">Mangaluru</option>
                    <option value="belagavi">Belagavi</option>
                    <option value="kalaburagi">Kalaburagi</option>
                    <option value="davanagere">Davanagere</option>
                    <option value="ballari">Ballari</option>
                    <option value="vijayapura">Vijayapura</option>
                    <option value="shivamogga">Shivamogga</option>
                    <option value="tumakuru">Tumakuru</option>
                    <option value="raichur">Raichur</option>
                    <option value="bidar">Bidar</option>
                    <option value="hassan">Hassan</option>
                    <option value="udupi">Udupi</option>
                </select> 
            </div>
            
            <input type="submit" name="sign" value="Register">
            
            <div class="signup_link">
                Already a member? <a href="deliverylogin.php">Signin</a>
            </div>
        </form>
    </div>

</body>
</html>