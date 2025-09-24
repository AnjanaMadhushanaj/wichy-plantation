<link rel="stylesheet" href="global.css">


<div class="background-bars-container">
        <div class="bar"></div>
        <div class="bar"></div>
        <div class="bar"></div>
        <div class="bar"></div>
        <div class="bar"></div>
        <div class="bar"></div>
        <div class="bar"></div>
        <div class="bar"></div>
        <div class="bar"></div>
        <div class="bar"></div>
    </div>

<header class="header">
        <div class="container header-container">
            <div class="logo-container">
                <h1 style="color: var(--color-dark-green-text);">Wichy</h1>
            </div>
            <nav class="main-nav">
                <?php $current = basename($_SERVER['PHP_SELF']); ?>
                <a href="index.php"<?php if($current=='index.php') echo ' class="active"'; ?>>Home</a>
                <a href="aboutus.php"<?php if($current=='aboutus.php') echo ' class="active"'; ?>>About us</a>
                <a href="news.php"<?php if($current=='news.php') echo ' class="active"'; ?>>News</a>
                <a href="contactus.php"<?php if($current=='contactus.php') echo ' class="active"'; ?>>Help us</a>
                <a href="image-gallery.php"<?php if($current=='image-gallery.php') echo ' class="active"'; ?>>Gallery</a>
            </nav>
            <div class="header-actions">
                <a href="login.php" class="btn btn-outline">Sign In</a>
                <a href="signup.php" class="btn btn-solid">Sign Up</a>
            </div>
            <div class="mobile-menu-trigger">
                <button id="mobile-menu-button">
                    <svg class="icon" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16m-7 6h7"></path>
                    </svg>
                </button>
            </div>
        </div>
        <div id="mobile-menu" class="mobile-menu hidden">
            <a href="#">Home</a>
            <a href="#">Contact us</a>
        </div>
    </header>