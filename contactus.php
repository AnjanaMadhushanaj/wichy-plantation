<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Help Center</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<link rel="stylesheet" href="global.css">
<style>

    header h1 {
        font-size: 1.8rem;
        color: var(--color-dark-green-text);
        margin-left: 1rem;
    }

    main {
        padding-top: 120px;
    }

    /* ---- HERO BANNER ---- */
    .hero {
        background: linear-gradient(135deg, var(--color-main-green), var(--color-bg-light-card));
        border-radius: 12px;
        padding: 2rem;
        text-align: center;
        margin-bottom: 2rem;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    .hero h2 {
        font-size: 2rem;
        color: var(--color-dark-green-text);
        margin-bottom: 0.5rem;
    }

    .hero p {
        font-size: 1.1rem;
        color: var(--color-gray-700);
    }

    /* ---- SUPPORT + FORM LAYOUT ---- */
    .support-section {
        display: flex;
        gap: 2rem;
        flex-wrap: wrap;
    }

    /* ---- SUPPORT CARDS ---- */
    .support-cards {
        flex: 1 1 350px;
        display: grid;
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }

    .card {
        background-color: var(--color-bg-light-card);
        border-radius: 12px;
        padding: 1.5rem;
        display: flex;
        align-items: flex-start;
        gap: 1rem;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 6px 18px rgba(0,0,0,0.15);
    }

    .card i {
        font-size: 1.8rem;
        color: var(--color-medium-green-text);
        min-width: 30px;
    }

    .card div {
        flex: 1;
    }

    .card div h4 {
        margin: 0;
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--color-dark-green-text);
        margin-bottom: 0.3rem;
    }

    .card div p {
        margin: 0;
        color: var(--color-gray-700);
    }

    /* ---- CONTACT FORM ---- */
    .contact-form {
        flex: 1 1 400px;
        background-color: var(--color-bg-light-card);
        border-radius: 12px;
        padding: 2rem;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    .contact-form label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 600;
        color: var(--color-dark-green-text);
    }

    .contact-form input,
    .contact-form textarea {
        width: 100%;
        padding: 0.75rem;
        margin-bottom: 1.5rem;
        border-radius: 8px;
        border: 1px solid var(--color-gray-700);
        font-size: 1rem;
        font-family: inherit;
    }

    .contact-form textarea {
        resize: vertical;
        min-height: 120px;
    }

    .btn-submit {
        display: inline-block;
        background-color: var(--color-dark-btn);
        color: white;
        padding: 0.75rem 2rem;
        border-radius: 9999px;
        text-decoration: none;
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .btn-submit:hover {
        background-color: var(--color-gray-700);
    }

    @media (max-width: 992px) {
        .support-section {
            flex-direction: column;
        }
    }
</style>
<?php include 'components/navbar.php'; ?>
<main class="container">
    <div class="hero">
        <h2>Welcome to Our Help Center</h2>
        <p>We're here to assist you. Contact us via the form or check our support options below.</p>
    </div>

    <div class="support-section">
        <!-- SUPPORT CARDS -->
        <div class="support-cards">
            <div class="card">
                <i class="fas fa-map-marker-alt"></i>
                <div>
                    <h4>Address</h4>
                    <p>123 Green Street, Colombo, Sri Lanka</p>
                </div>
            </div>

            <div class="card">
                <i class="fas fa-phone"></i>
                <div>
                    <h4>Phone</h4>
                    <p>+94 77 123 4567</p>
                </div>
            </div>

            <div class="card">
                <i class="fas fa-envelope"></i>
                <div>
                    <h4>Email</h4>
                    <p>support@wichycocunut.com</p>
                </div>
            </div>

            <div class="card">
                <i class="fas fa-comments"></i>
                <div>
                    <h4>Live Chat</h4>
                    <p>Available 9AM - 6PM</p>
                </div>
            </div>
        </div>

        <!-- CONTACT FORM -->
        <div class="contact-form">
            <form action="#" method="POST">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" placeholder="Your Name" required>

                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" placeholder="you@example.com" required>

                <label for="subject">Subject</label>
                <input type="text" id="subject" name="subject" placeholder="Subject" required>

                <label for="message">Message</label>
                <textarea id="message" name="message" placeholder="Write your message..." required></textarea>

                <button type="submit" class="btn-submit">Send Message</button>
            </form>
        </div>
    </div>
</main>

    <?php include 'components/footer.php'; ?>
</body>
</html>
