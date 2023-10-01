<?php 
    $config = include __DIR__ .'/config.php';
    session_start(); 
    $uid = uniqid(); 
    $_SESSION['token'] = $uid; 

    include __DIR__ . '/helpers.php';
    include __DIR__ . '/template.php';

    // Authorize User
    auth();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $config['app_name'] ?></title>
    <meta id="xtoken" content="<?= $uid ?>">
    <link rel="icon" type="image/jpg" href="https://pbs.twimg.com/profile_images/1616915938741465089/zfQFJP25_400x400.jpg">
    <link rel="stylesheet" href="fontawesome/css/all.css">
    <link rel="stylesheet" href="style.css?v=2">
</head>
<body>

    <div id="notification" class="notification"></div>
    
    <div class="bio">
        <div class="section">
            <div id="profile-photo">
                <img id="profile-photo-img" src="" alt="myself">
            </div>
            <h1 class="mb-1" id="bio-name"><span class="placeholder"></span></h1>
            <p class="mb-1" id="bio-dob"><span class="placeholder"></span></p>
            <p class="mb-1">
                <span id="bio-email"><span class="placeholder"></span></span> • 
                <span id="bio-phone"><span class="placeholder"></span></span>
            </p>
            <p class="mb-1" id="bio-website"><span class="placeholder"></span></p>
            <p class="mb-1" id="last-updated"><span class="placeholder"></span></p>
        </div>
    </div>

    <nav>
        <div class="section">
            <ul>
                <li><a href="#" data-target="languages" class="nav-link">Languages</a></li>
                <li><a href="#" data-target="skills" class="nav-link">Skills</a></li>
                <li><a href="#" data-target="academic" class="nav-link">Academic</a></li>
                <li><a href="#" data-target="work-experiences" class="nav-link">Employment</a></li>
                <li><a href="#" data-target="projects" class="nav-link">Projects</a></li>
            </ul>
        </div>
    </nav>

    <div class="contact sub-section">
        <div class="section">
            <h2><i class="fa-solid fa-magnifying-glass"></i> Find Me</h2>
            <div id="contact"><i class="fa-solid fa-spin fa-spinner"></i> Loading...</div>
        </div>
    </div>

    <div class="language sub-section">
        <div class="section">
            <h2><i class="fa-solid fa-hands-asl-interpreting"></i> Spoken Languages</h2>
            <div id="languages"><i class="fa-solid fa-spin fa-spinner"></i> Loading...</div>
        </div>
    </div>

    <div class="skills sub-section">
        <div class="section">
            <h2><i class="fa-solid fa-book-skull"></i> Skills Manual</h2>
            <div id="skills"><i class="fa-solid fa-spin fa-spinner"></i> Loading...</div>
        </div>
    </div>

    <div class="education sub-section">
        <div class="section">
            <h2><i class="fa-solid fa-graduation-cap"></i> Academic Background</h2>
            <div id="education"><i class="fa-solid fa-spin fa-spinner"></i> Loading...</div>
        </div>
    </div>

    <div class="work-exp sub-section">
        <div class="section">
            <h2><i class="fa-solid fa-briefcase"></i> Employment History</h2>
            <div id="work-experiences"><i class="fa-solid fa-spin fa-spinner"></i> Loading...</div>
        </div>
    </div>

    <div class="projects sub-section">
        <div class="section">
            <h2><i class="fa-solid fa-briefcase"></i> Projects</h2>
            <div id="projects"><i class="fa-solid fa-spin fa-spinner"></i> Loading...</div>
        </div>
    </div>

    <script src="app.js"></script>
</body>
</html>