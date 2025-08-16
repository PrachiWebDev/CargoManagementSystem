<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cargo Management System - Efficient Logistics Solutions</title>
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #000000;
            color: #ffffff;
        }
        .hero-section {
            background: linear-gradient(rgba(0, 0, 0, 0.8), rgba(0, 0, 0, 0.8)), url('https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80');
            background-size: cover;
            background-position: center;
            color: #ffd700;
            padding: 150px 0;
        }
        .feature-card {
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(255, 215, 0, 0.2);
            transition: transform 0.3s;
            height: 100%;
            background-color: #1a1a1a;
            border: 1px solid #ffd700;
        }
        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 0 30px rgba(255, 215, 0, 0.3);
        }
        .feature-icon {
            font-size: 40px;
            color: #ffd700;
            margin-bottom: 20px;
        }
        .cta-section {
            background: linear-gradient(45deg, #000000, #1a1a1a);
            color: #ffd700;
            padding: 100px 0;
            border-top: 2px solid #ffd700;
            border-bottom: 2px solid #ffd700;
        }
        .testimonial-card {
            background: #1a1a1a;
            padding: 30px;
            border-radius: 10px;
            margin-bottom: 20px;
            border: 1px solid #ffd700;
        }
        .testimonial-img {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            margin-right: 15px;
            border: 2px solid #ffd700;
        }
        .navbar {
            background-color: rgba(0, 0, 0, 0.95);
            box-shadow: 0 2px 10px rgba(255, 215, 0, 0.2);
        }
        .navbar-brand {
            font-weight: 600;
            color: #ffd700;
        }
        .nav-link {
            font-weight: 500;
            color: #ffffff;
        }
        .nav-link:hover {
            color: #ffd700;
        }
        .btn-custom {
            padding: 12px 30px;
            border-radius: 30px;
            font-weight: 500;
        }
        .btn-primary {
            background-color: #ffd700;
            border-color: #ffd700;
            color: #000000;
        }
        .btn-primary:hover {
            background-color: #ffed4a;
            border-color: #ffed4a;
            color: #000000;
        }
        .btn-outline-light {
            border-color: #ffd700;
            color: #ffd700;
        }
        .btn-outline-light:hover {
            background-color: #ffd700;
            color: #000000;
        }
        h1, h2, h3, h4, h5, h6 {
            color: #ffd700;
        }
        .text-muted {
            color: #ffd700 !important;
            opacity: 0.7;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light fixed-top">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-shipping-fast text-warning me-2"></i><span class="text-warning">Cargo Management</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#features">Features</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#testimonials">Testimonials</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contact">Contact</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link btn btn-primary text-white ms-2" href="login.php">Login</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section text-center">
        <div class="container">
            <h1 class="display-4 fw-bold mb-4">Efficient Cargo Management Solutions</h1>
            <p class="lead mb-5">Streamline your logistics operations with our comprehensive cargo management system</p>
            <div class="d-flex justify-content-center gap-3">
                <a href="register.php" class="btn btn-primary btn-custom">Get Started</a>
                <a href="#features" class="btn btn-outline-light btn-custom">Learn More</a>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-5">
        <div class="container">
            <h2 class="text-center mb-5">Key Features</h2>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-truck"></i>
                        </div>
                        <h4>Vehicle Management</h4>
                        <p>Efficiently manage your fleet with real-time tracking and maintenance scheduling.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <h4>Location Tracking</h4>
                        <p>Track shipments in real-time with our advanced location tracking system.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h4>Analytics & Reports</h4>
                        <p>Get detailed insights with comprehensive analytics and reporting tools.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    

    <!-- How It Works Section -->
    <section id="how-it-works" class="py-5 m-2">
        <div class="container border border-warning">
            <h2 class="text-center mb-5">How It Works</h2>
            <div class="row g-4">
                <div class="col-md-3">
                    <div class="text-center">
                        <div class="feature-icon">
                            <i class="fas fa-user-plus"></i>
                        </div>
                        <h4>1. Sign Up</h4>
                        <p>Create your account and set up your profile</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center">
                        <div class="feature-icon">
                            <i class="fas fa-truck-loading"></i>
                        </div>
                        <h4>2. Add Shipments</h4>
                        <p>Create and manage your cargo shipments</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center">
                        <div class="feature-icon">
                            <i class="fas fa-map-marked-alt"></i>
                        </div>
                        <h4>3. Track & Monitor</h4>
                        <p>Real-time tracking of your shipments</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center">
                        <div class="feature-icon">
                            <i class="fas fa-chart-bar"></i>
                        </div>
                        <h4>4. Analyze & Optimize</h4>
                        <p>Get insights and improve operations</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing Plans Section -->
    <!--section id="pricing" class="py-5 m-3">
        <div class="container border border-warning py-3">
            <h2 class="text-center mb-5">Choose Your Plan</h2>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card h-100">
                        <div class="card-header bg-primary text-white text-center">
                            <h4 class="my-0">Basic</h4>
                        </div>
                        <div class="card-body bg-dark text-white">
                            <h1 class="card-title text-center">₹999<small class="text-muted fw-light">/mo</small></h1>
                            <ul class="list-unstyled mt-3 mb-4">
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Up to 50 shipments/month</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Basic tracking</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Email support</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Basic reports</li>
                            </ul>
                            <div class="d-grid">
                                <a href="register.php" class="btn btn-outline-primary">Get Started</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 border-primary">
                        <div class="card-header bg-primary text-white text-center">
                            <h4 class="my-0">Professional</h4>
                        </div>
                        <div class="card-body bg-dark text-white">
                            <h1 class="card-title text-center">₹2499<small class="text-muted fw-light">/mo</small></h1>
                            <ul class="list-unstyled mt-3 mb-4">
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Up to 200 shipments/month</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Advanced tracking</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Priority support</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Advanced analytics</li>
                            </ul>
                            <div class="d-grid">
                                <a href="register.php" class="btn btn-primary">Get Started</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100">
                        <div class="card-header bg-primary text-white text-center">
                            <h4 class="my-0">Enterprise</h4>
                        </div>
                        <div class="card-body bg-dark text-white">
                            <h1 class="card-title text-center">₹4999<small class="text-muted fw-light">/mo</small></h1>
                            <ul class="list-unstyled mt-3 mb-4">
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Unlimited shipments</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Real-time tracking</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>24/7 support</li>
                                <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Custom reports</li>
                            </ul>
                            <div class="d-grid">
                                <a href="register.php" class="btn btn-outline-primary">Get Started</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section-->

    <!-- Testimonials Section -->
    <section id="testimonials" class="py-5 m-3">
        <div class="container border border-warning py-3">
            <h2 class="text-center mb-5">What Our Clients Say</h2>
            <div class="row">
                <div class="col-md-4">
                    <div class="testimonial-card">
                        <div class="d-flex align-items-center mb-3">
                            <img src="https://randomuser.me/api/portraits/men/1.jpg" alt="Client" class="testimonial-img">
                            <div>
                                <h5 class="mb-0">John Doe</h5>
                                <small class="text-muted">Logistics Manager</small>
                            </div>
                        </div>
                        <p class="mb-0">"This system has revolutionized our cargo management process. Highly recommended!"</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="testimonial-card">
                        <div class="d-flex align-items-center mb-3">
                            <img src="https://randomuser.me/api/portraits/women/1.jpg" alt="Client" class="testimonial-img">
                            <div>
                                <h5 class="mb-0">Jane Smith</h5>
                                <small class="text-muted">Transport Coordinator</small>
                            </div>
                        </div>
                        <p class="mb-0">"The real-time tracking feature has significantly improved our delivery efficiency."</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="testimonial-card">
                        <div class="d-flex align-items-center mb-3">
                            <img src="https://randomuser.me/api/portraits/men/2.jpg" alt="Client" class="testimonial-img">
                            <div>
                                <h5 class="mb-0">Mike Johnson</h5>
                                <small class="text-muted">Fleet Manager</small>
                            </div>
                        </div>
                        <p class="mb-0">"Excellent customer support and user-friendly interface. A game-changer for our business."</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section id="faq" class="py-5 m-3">
        <div class="container border border-warning py-3">
            <h2 class="text-center mb-5">Frequently Asked Questions</h2>
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="accordion" id="faqAccordion">
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                    How does the tracking system work?
                                </button>
                            </h2>
                            <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Our tracking system uses GPS technology to provide real-time location updates of your shipments. You can monitor your cargo's movement through our user-friendly dashboard.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                    Can I integrate with my existing systems?
                                </button>
                            </h2>
                            <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Yes, our system offers API integration capabilities that allow you to connect with your existing ERP, CRM, or other business systems.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                    What kind of support do you offer?
                                </button>
                            </h2>
                            <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    We provide comprehensive support including email, phone, and live chat support. Our support team is available 24/7 to assist you with any queries or issues.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                                    Is there a mobile app available?
                                </button>
                            </h2>
                            <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Yes, we offer mobile apps for both iOS and Android platforms, allowing you to manage your shipments and track cargo on the go.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Form Section -->
    <section id="contact" class="py-5 m-3">
        <div class="container border border-warning py-3">
            <h2 class="text-center mb-5">Contact Us</h2>
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card shadow-sm">
                        <div class="card-body p-4">
                            <form id="contactForm" action="process_contact.php" method="POST" class="needs-validation " novalidate>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="form-floating mb-3">
                                            <input type="text" class="form-control" id="name" name="name" placeholder="Your Name" required>
                                            <label for="name">Your Name</label>
                                            <div class="invalid-feedback">
                                                Please enter your name.
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating mb-3">
                                            <input type="email" class="form-control" id="email" name="email" placeholder="Your Email" required>
                                            <label for="email">Your Email</label>
                                            <div class="invalid-feedback">
                                                Please enter a valid email address.
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating mb-3">
                                            <input type="tel" class="form-control" id="phone" name="phone" placeholder="Your Phone" required>
                                            <label for="phone">Your Phone</label>
                                            <div class="invalid-feedback">
                                                Please enter your phone number.
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating mb-3">
                                            <select class="form-select" id="subject" name="subject" required>
                                                <option value="">Select a subject</option>
                                                <option value="general">General Inquiry</option>
                                                <option value="support">Technical Support</option>
                                                <option value="billing">Billing Question</option>
                                                <option value="partnership">Partnership Opportunity</option>
                                            </select>
                                            <label for="subject">Subject</label>
                                            <div class="invalid-feedback">
                                                Please select a subject.
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-floating mb-3">
                                            <textarea class="form-control" id="message" name="message" placeholder="Your Message" style="height: 150px" required></textarea>
                                            <label for="message">Your Message</label>
                                            <div class="invalid-feedback">
                                                Please enter your message.
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 text-center">
                                        <button type="submit" class="btn btn-primary btn-lg px-5">
                                            <i class="fas fa-paper-plane me-2"></i>Send Message
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section id="contact" class="cta-section text-center m-3">
        <div class="container border border-warning py-3">
            <h2 class="mb-4">Ready to Optimize Your Cargo Management?</h2>
            <p class="lead mb-5">Join thousands of satisfied customers who trust our system</p>
            <a href="register.php" class="btn btn-light btn-custom">Get Started Now</a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-light py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5>About Us</h5>
                    <p>We provide comprehensive cargo management solutions to streamline your logistics operations.</p>
                </div>
                <div class="col-md-4">
                    <h5>Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="#features" class="text-light">Features</a></li>
                        <li><a href="#testimonials" class="text-light">Testimonials</a></li>
                        <li><a href="#contact" class="text-light">Contact</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5>Contact Us</h5>
                    <ul class="list-unstyled">
                        <li><i class="fas fa-phone me-2"></i> +1 234 567 890</li>
                        <li><i class="fas fa-envelope me-2"></i> info@cargomanagement.com</li>
                        <li><i class="fas fa-map-marker-alt me-2"></i> 123 Logistics Street, City</li>
                    </ul>
                </div>
            </div>
            <hr class="my-4">
            <div class="text-center">
                <p class="mb-0">&copy; 2025 Cargo Management System. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5.3 JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Form validation
        (function () {
            'use strict'
            var forms = document.querySelectorAll('.needs-validation')
            Array.prototype.slice.call(forms).forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    }
                    form.classList.add('was-validated')
                }, false)
            })
        })()
    </script>
</body>
</html> 