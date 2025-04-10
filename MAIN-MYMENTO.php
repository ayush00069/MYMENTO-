<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Mento - Home</title>
    <link rel="stylesheet" href="home.css"> <!-- Link to the CSS file -->
    <style>
        /* Hide all sections except the home section initially */
        #mymento-section, #contact-section {
            display: none;
        }

        /* Logo fade-in animation */
        .logo {
            opacity: 0;
            animation: fadeIn 2s forwards;
            animation-delay: 0.5s; /* Delay before the animation starts */
        }

        /* Fade-in animation for the title */
        .title-section h1 {
            opacity: 0;
            animation: fadeIn 2s forwards;
            animation-delay: 1s; /* Delay before the animation starts */
        }

        /* Fade-in animation for the subtitle */
        .subtitle {
            opacity: 0;
            animation: fadeIn 2s forwards;
            animation-delay: 1.5s; /* Delay before the animation starts */
        }

        /* Keyframe for fade-in animation */
        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }
    </style>
    <script>
        // Function to show the selected section and hide others
        function showSection(sectionId) {
            // Hide all sections
            document.getElementById('home-section').style.display = 'none';
            document.getElementById('mymento-section').style.display = 'none';
            document.getElementById('contact-section').style.display = 'none';

            // Show the selected section
            document.getElementById(sectionId).style.display = 'block';
        }
    </script>
</head>
<body>
    <header>
        <div class="navbar">
            <nav>
                <ul>
                    <li><a href="#" onclick="showSection('home-section');">Home</a></li>
                    <li><a href="#" onclick="showSection('mymento-section');">My Mento?</a></li>
                    <li><a href="#" onclick="showSection('contact-section');">Contact</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main>
        <!-- Home Section -->
        <div id="home-section" class="content-section">
            <div class="title-section">
                <img src="mohit.png" alt="My Mento Logo" class="logo"> <!-- Replace with your logo file -->
                <h1>MY MENTO</h1>
                <p class="subtitle">Designed for Mentoring of Students</p>
            </div>
            <div class="content">
                <h2>Register Now</h2>
                <div class="register-options">
                    <div class="register-card" onclick="location.href='registerteacher.php';">
                        <img src="DEV.png" alt="Teacher">
                        <p>TEACHER</p>
                    </div>
                    <div class="register-card" onclick="location.href='registerstudent.php';">
                        <img src="ayush.png" alt="Student">
                        <p>STUDENT</p>
                    </div>
                    <div class="register-card" onclick="location.href='loginparent.php';">
                        <img src="parent .png" alt="Parent">
                        <p>PARENT</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- My Mento Section (Hidden by default) -->
        <div id="mymento-section" class="content-section" style="display: none;">
            <h2>About My Mento</h2>
            <p>My Mento is a platform designed to provide mentorship to students, 
                <p>connecting them with teachers and parents to support their educational journey.</p>
                <p>Mymento is a web-based platform designed to manage and display student information,</p>
                 <p>including academic records, attendance, and mentor details. It allows secure access for students, faculty, and parents,</p>
                  ensuring data integrity and streamlined communication while preventing unauthorized changes to critical information.</p>
        </div>

        <!-- Contact Section (Hidden by default) -->
        <div id="contact-section" class="content-section" style="display: none;">
            <h2>Contact Us</h2>
            <p>If you have any questions or need assistance, please reach out to us at:</p>
            <ul>
                <li>Email: support@mymento.com</li>
                <li>Phone: +00 0000000000</li>
                <li>Address: Gandhinagar,Gujarat,India</li>
            </ul>
        </div>
    </main>
</body>
</html>
