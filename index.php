<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WICHY COCONUT</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="global.css">
</head>

<body>



    <?php include 'components/navbar.php'; ?>

    <main class="container">
        <!-- hero section -->
        <section class="hero-section">
            <div class="hero-overlay"></div>
            <div class="hero-content">
                <img src="https://i.postimg.cc/Rh4vpGD4/LOGO.png" alt="WICHY COCONUT Logo" class="hero-logo">
                <div class="hero-text-and-button-group">
                    <h1 class="hero-title">Wichy—your source for handcrafted coconut products.</h1>
                    <a href="login.php" class="btn btn-solid btn-large">
                        <span>SHOP NOW</span>
                        <svg class="icon-arrow" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                            fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.707l-3-3a1 1 0 00-1.414 1.414L10.586 9H7a1 1 0 100 2h3.586l-1.293 1.293a1 1 0 101.414 1.414l3-3a1 1 0 000-1.414z"
                                clip-rule="evenodd" />
                        </svg>
                    </a>
                </div>
            </div>
        </section>
        <!-- stats section -->
        <section class="stats-section">
            <div class="stat-card">
                <h3 class="stat-number" id="total-users">0</h3>
                <p class="stat-label">Total Users</p>
            </div>
            <div class="stat-card">
                <h3 class="stat-number" id="total-items">0</h3>
                <p class="stat-label">Total Items</p>
            </div>
            <div class="stat-card">
                <h3 class="stat-number" id="total-recipes">0</h3>
                <p class="stat-label">Total Recipes</p>
            </div>
            <div class="stat-card">
                <h3 class="stat-number" id="total-recipes">0</h3>
                <p class="stat-label">Total Employees</p>
            </div>
        </section>
        <!-- process section -->
        <section class="process-section">
            <h2 class="section-title">Our Process</h2>
            <div class="process-content">
                <div class="process-text-card">
                    <p>
                        A promise to retain the delicious and wholesome properties of WICHY coconuts from the point of
                        sourcing to delivering, from our hands to yours. A commitment rooted in the rich tradition of
                        coconut production in Sri Lanka.
                    </p>
                </div>
                <div>
                    <img src="https://i.postimg.cc/RV4XRyXQ/Whats-App-Image-2025-09-09-at-18-55-03-68198245-removebg-preview.png"
                        alt="Our Process Logo" class="process-image animate-spin-slow">
                </div>
            </div>
        </section>
        <!-- prodduct section -->
        <section class="products-section">

            <div class="section-header">
                <h2 class="section-title">Our Products</h2>
                <p>
                    WICHY Coconut brings to you a trusted and exciting array of food products and beverages that have
                    exquisite taste, a variety of flavours, and most importantly, essential nutrients that help you stay
                    healthy.
                </p>
            </div>
            <div class="products-grid">
                <div class="product-card">
                    <img src="https://i.postimg.cc/59QxpYKX/pro1.png" alt="Product 1"
                        class="product-image">
                    <div class="product-info">
                        <h3 class="product-name">Coconut Oil</h3>
                        <p class="product-description">Pure, cold-pressed virgin coconut oil.</p>
                    </div>
                </div>
                <div class="product-card">
                    <img src="https://i.postimg.cc/LsGWsXtW/pro2.png" alt="Product 2"
                        class="product-image">
                    <div class="product-info">
                        <h3 class="product-name">Coconut Milk</h3>
                        <p class="product-description">Creamy and rich coconut milk for cooking.</p>
                    </div>
                </div>
                <div class="product-card">
                    <img src="https://i.postimg.cc/4dT1MhbX/pro3.png" alt="Product 3"
                        class="product-image">
                    <div class="product-info">
                        <h3 class="product-name">Coconut Water</h3>
                        <p class="product-description">Refreshing and hydrating natural coconut water.</p>
                    </div>
                </div>
                <div class="product-card">
                    <img src="https://i.postimg.cc/25nQ4SpM/pro4.png" alt="Product 4"
                        class="product-image">
                    <div class="product-info">
                        <h3 class="product-name">Coconut Flour</h3>
                        <p class="product-description">Gluten-free alternative for baking.</p>
                    </div>
                </div>
            </div>
        </section>
        <!-- Section for the large image gallery link -->
        <section class="commitment-section">
            <h1 class="section-title">Our Commitment</h1>
            <p class="commitment-text">
                The WICHY commitment is to our customers, planet, and community as a whole.<br>
                We pledge to follow sustainable business practices, with food quality and safety being our priority, while
                reducing our carbon footprint and protecting our planet. We support local farming communities; our success
                relies on their progress.
            </p>
            <div class="gallery-section">
                <div class="gallery-card">
                    <!-- IMPORTANT: Make sure the image path 'images/image1.png' is correct -->
                    <img src="https://i.postimg.cc/cLmwF5k4/image1.png" alt="Wichy Coconut Co. Building">
                    <div class="gallery-overlay">
                        <h1>Visit Our Company With Image Gallery</h1>
                    </div>
                </div>
        </section>
        <!--  section -->
    </main>

    <?php include 'components/footer.php'; ?>

    <!-- AI Agent Floating Icon & Chat Widget -->
    <div id="ai-chat-icon" style="position: fixed; bottom: 32px; right: 32px; z-index: 9999; width: 70px; height: 70px; background: rgba(255,255,255,0.25); border-radius: 50%; box-shadow: 0 8px 32px rgba(44,62,80,0.18); backdrop-filter: blur(8px); border: 1.5px solid rgba(255,255,255,0.35); display: flex; align-items: center; justify-content: center; cursor: pointer;">
        <img src="https://i.postimg.cc/Rh4vpGD4/LOGO.png" alt="AI Agent" style="width: 38px; height: 38px; filter: drop-shadow(0 2px 8px rgba(44,62,80,0.12));">
    </div>
    <div id="ai-chat-widget" style="position: fixed; bottom: 32px; right: 32px; z-index: 9999; width: 350px; max-width: 90vw; background: #fff; border-radius: 16px; box-shadow: 0 4px 24px rgba(0,0,0,0.15); border: 1px solid #e0e0e0; display: none; flex-direction: column; overflow: hidden;">
        <div style="background: #43a047; color: #fff; padding: 12px 16px; border-top-left-radius: 16px; border-top-right-radius: 16px; font-weight: bold; position: relative;">
            Wichy AI Agent
            <span id="ai-chat-close" style="position: absolute; right: 16px; top: 12px; cursor: pointer; font-size: 20px;">&times;</span>
        </div>
        <div id="ai-chat-bg" style="position: absolute; inset: 0; opacity: 0.04; background: url('https://i.postimg.cc/Rh4vpGD4/LOGO.png') center/140px no-repeat;"></div>
        <div id="ai-chat-messages" style="flex: 1; padding: 16px; overflow-y: auto; min-height: 120px; max-height: 300px; position: relative;"></div>
        <form id="ai-chat-form" style="display: flex; border-top: 1px solid #e0e0e0; position: relative; background: rgba(255,255,255,0.95);">
            <input type="text" id="ai-chat-input" placeholder="Ask about Wichy Plantation..." style="flex: 1; border: none; padding: 12px; border-bottom-left-radius: 16px; outline: none;">
            <button type="submit" style="background: #43a047; color: #fff; border: none; padding: 0 18px; border-bottom-right-radius: 16px; font-weight: bold; cursor: pointer;">Send</button>
        </form>
    </div>
    <script>
        
    
        // Mobile menu toggle
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');

        mobileMenuButton.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });
    </script>
</body>

</html>