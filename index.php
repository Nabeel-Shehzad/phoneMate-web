<?php // requiring the header of page
require_once("includes/header.php");
?>
<?php // requiring the slider of page
require_once("includes/slider.php");
?>

<!-- Why Choose Us Section -->
<section class="why-choose-us py-5">
    <div class="container">
        <div class="row mb-4">
            <div class="col-12">
                <h2 class="anton-regular text-center">Why Choose Us</h2>
            </div>
        </div>
        <div class="row g-4">
            <div class="col-md-3">
                <div class="feature-card text-center">
                    <div class="icon-wrapper">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                        </svg>
                    </div>
                    <h4>Secure Payments</h4>
                    <p>100% secure payment processing with advanced encryption</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="feature-card text-center">
                    <div class="icon-wrapper">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"/>
                            <polyline points="12 6 12 12 16 14"/>
                        </svg>
                    </div>
                    <h4>Fast Delivery</h4>
                    <p>Quick and reliable shipping with real-time tracking</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="feature-card text-center">
                    <div class="icon-wrapper">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                        </svg>
                    </div>
                    <h4>24/7 Support</h4>
                    <p>Round the clock customer service with expert assistance</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="feature-card text-center">
                    <div class="icon-wrapper">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M3 7v10a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-6l-2-2H5a2 2 0 0 0-2 2z"/>
                        </svg>
                    </div>
                    <h4>Easy Returns</h4>
                    <p>Hassle-free return policy with quick refunds</p>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.why-choose-us {
    background-color: #f8f9fa;
    padding: 80px 0;
}

.feature-card {
    background: #fff;
    padding: 40px 30px;
    border-radius: 15px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);
    transition: all 0.3s ease;
    height: 100%;
}

.feature-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
}

.icon-wrapper {
    width: 80px;
    height: 80px;
    margin: 0 auto 25px;
    background: #fff5f5;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
}

.icon-wrapper svg {
    width: 40px;
    height: 40px;
    color: #eb1616;
}

.feature-card:hover .icon-wrapper {
    background: #eb1616;
}

.feature-card:hover .icon-wrapper svg {
    color: #fff;
}

.feature-card h4 {
    color: #333;
    font-size: 20px;
    margin-bottom: 15px;
    font-weight: 600;
}

.feature-card p {
    color: #666;
    margin: 0;
    line-height: 1.6;
}

@media (max-width: 768px) {
    .feature-card {
        margin-bottom: 30px;
    }
}
</style>

<?php // requiring the footer of page
require_once("includes/footer.php");
?>