<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Splash Screen</title>
    <link rel="stylesheet" href="splash.css">
</head>
<body>
    <section id="splash-screen" class="splash-screen">
        <div class="logo-container">
            <img src="images/logo.jpeg" alt="App Logo" class="hex-logo">
        </div>
        <p class="welcome-text">Welcome to Our App</p>
        <h2 class="org-structure">Organizational Structure</h2>
        <button class="next-button" onclick="window.location.href='intro.php'">Next</button>
    </section>

    <section class="person-container">
        <div class="person-card">
            <img src="images/KB.jpg" alt="John Doe">
            <h1>Mr Matoko</h1>
            <h2>Database Administrator</h2>
            <p>Mr Matoko ensures the company's data systems are efficient, secure, and reliable. By managing databases, optimizing performance, and implementing backup solutions</p>
        </div>

        <div class="person-card">
            <img src="images/Kamza.jpg" alt="Jane Smith">
            <h1>Mr. Kgomommekoa</h1>
            <h2>UI Designer</h2>
            <p>Mr. Kgomommekoa designs clean and simple interfaces for the Stabus app  focusing on making the app easy to use, look good, and meet student needs.</p>
        </div>

        <div class="person-card">
            <img src="images/Martin.jpg" alt="Emily Johnson">
            <h1>Mr. Khoza</h1>
            <h2>Front End Developer</h2>
            <p>Mr. Khoza builds clean, responsive interfaces for the bus service platform, ensuring users can easily book trips, check schedules, and navigate the system on any device.</p>
        </div>

        <div class="person-card">
            <img src="images/asim.jpg" alt="Michael Lee">
            <h1>Mr. Mazibuko</h1>
            <h2>Back End Developer</h2>
            <p>Mr. Mazibuko handles server-side logic, database interactions, user authentication, and the implementation of real-time features such as notifications and bus tracking.</p>
        </div>

        <div class="person-card">
            <img src="images/Mahex.jpg" alt="Amy Brown">
            <h1>Mr. Mahelehele</h1>
            <h2>DevOps Engineer</h2>
            <p>Mr. Mahelehele handles automation and infrastructure for reliable deployments.</p>
        </div>
    </section>
</body>
</html>
