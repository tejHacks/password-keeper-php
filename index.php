<!-- Password Monkey - Landing Page -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Monkey</title>
    <link rel="stylesheet" href="assets/bootstrap-5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/font-awesome/css/font-awesome.css">
    <link rel="stylesheet" href="assets/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="assets/boxicons/css/boxicons.css">
    <link rel="stylesheet" href="assets/boxicons/css/boxicons.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            color: #333;
            transition: background 0.3s, color 0.3s;
        }
        .dark-mode {
            background-color: #121212;
            color: #ffffff;
        }
        .navbar {
            transition: background 0.3s;
        }
        .dark-mode .navbar {
            background-color: #222;
        }
        .feature-icon {
            font-size: 2rem;
            color: #007bff;
        }
        .dark-mode .feature-icon {
            color: #66b2ff;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark text-light">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">Password Monkey 🐒</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link text-light" href="#features">Features</a></li>
                    <li class="nav-item"><a class="nav-link text-light" href="register.php">SignUp</a></li>
                    <li class="nav-item"><a class="nav-link text-light" href="#contact">Contact</a></li>
                    <li class="nav-item"><button id="toggleDarkMode" class="btn btn-outline-primary text-light"><i class="fa fa-moon-o"></i> Dark Mode</button></li>
                </ul>
            </div>
        </div>
    </nav>

    <header class="text-center py-5">
        <div class="container">
            <h1>Secure Your Passwords with Ease</h1>
            <p class="lead">Password Monkey helps you store, manage, and export your passwords securely.</p>
            <a href="#features" class="btn btn-primary">Explore Features</a>
        </div>
    </header>

    <section id="features" class="py-5">
        <div class="container">
            <h2 class="text-center mb-4">Features <hr class="text-primary"></h2>
            <div class="row text-center">
                <div class="col-md-4">
                    <i class="fa fa-lock feature-icon"></i>
                    <h4>Secure Storage</h4>
                    <p>All passwords are stored securely using encryption.</p>
                </div>
                <div class="col-md-4">
                    <i class="fa fa-random feature-icon"></i>
                    <h4>Password Generator</h4>
                    <p>Generate strong, unique passwords with just one click.</p>
                </div>
                <div class="col-md-4">
                    <i class="bx bx-file feature-icon"></i>
                    <h4>Export to CSV</h4>
                    <p>Easily export your saved passwords to a CSV file.</p>
                </div>
            </div>
      
            <div  class="container col-md-12  my-6 text-center" id="contact" class="py-5 my-6 bg-light">
              <h2>Contact Us</h2>
            <p>Have questions? Reach out to us!</p>
            <ul class="list-unstyled">
                <li><i class="fa fa-envelope"></i> Email: olateju202@gmail.com</li>
                <li><i class="fa fa-phone"></i> Phone: +23408086976247</li>
                <li><i class="fa fa-twitter"></i> Twitter: <a href="#">@PasswordMonkey</a></li>
                <li><i class="fa fa-github"></i> GitHub: <a href="#">github.com/passwordmonkey</a></li>
            </ul>
            </div>
        </div>
    </section>

    <script>
        document.getElementById('toggleDarkMode').addEventListener('click', function() {
            document.body.classList.toggle('dark-mode');
        });
    </script>

    <script src="assets/bootstrap-5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
