// DOM Content Loaded
document.addEventListener('DOMContentLoaded', function() {
    initializeWebsite();
});

// Initialize all website functionality
function initializeWebsite() {
    setupNavigation();
    setupScrollAnimations();
    // Check if setupStatsCounter is defined before calling
    if (typeof setupStatsCounter === 'function') {
        setupStatsCounter();
    }
    // Check if setupCourseEnrollment is defined before calling
    if (typeof setupCourseEnrollment === 'function') {
        setupCourseEnrollment();
    }
    // Check if setupMobileMenu is defined before calling
    if (typeof setupMobileMenu === 'function') {
        setupMobileMenu();
    }
}

// Navigation functionality
function setupNavigation() {
    // Smooth scrolling for navigation links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Navbar background change on scroll
    window.addEventListener('scroll', handleNavbarScroll);
}

// Handle navbar background opacity on scroll
function handleNavbarScroll() {
    const navbar = document.querySelector('.navbar');
    const scrollY = window.scrollY;
    
    if (scrollY > 50) {
        navbar.style.background = 'rgba(26, 35, 126, 0.98)';
    } else {
        navbar.style.background = 'rgba(26, 35, 126, 0.95)';
    }
}

// Setup scroll animations
function setupScrollAnimations() {
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
            }
        });
    }, observerOptions);

    // Observe all elements with fade-in class
    document.querySelectorAll('.fade-in').forEach(el => {
        observer.observe(el);
    });
}

// Setup stats counter animation
function setupStatsCounter() {
    const statsSection = document.querySelector('.stats');
    if (!statsSection) return;

    const statsObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const counters = entry.target.querySelectorAll('.stat-number');
                counters.forEach(counter => animateCounter(counter));
                statsObserver.unobserve(entry.target);
            }
        });
    }, { threshold: 0.5 });

    statsObserver.observe(statsSection);
}

// Animate counter numbers
function animateCounter(element) {
    const target = parseInt(element.dataset.count);
    const duration = 2000; // 2 seconds
    const step = target / (duration / 16); // 60 FPS
    let current = 0;

    const timer = setInterval(() => {
        current += step;
        if (current >= target) {
            current = target;
            clearInterval(timer);
        }
        element.textContent = Math.floor(current).toLocaleString();
    }, 16);
}

// Setup course enrollment functionality
function setupCourseEnrollment() {
    document.querySelectorAll('.enroll-btn').forEach(btn => {
        btn.addEventListener('click', handleEnrollment);
    });
}

// Handle course enrollment
function handleEnrollment() {
    const button = this;
    const originalText = button.textContent;
    
    // Change button state
    button.textContent = 'Enrolled!';
    button.style.background = 'linear-gradient(45deg, #4ECDC4, #44A08D)';
    button.disabled = true;
    
    // Add success animation
    button.style.transform = 'scale(1.05)';
    
    setTimeout(() => {
        button.style.transform = 'scale(1)';
    }, 200);
    
    // Reset button after 3 seconds
    setTimeout(() => {
        button.textContent = originalText;
        button.style.background = 'linear-gradient(45deg, #4a90e2, #74b9ff)';
        button.disabled = false;
    }, 3000);
    
    // Show enrollment confirmation
    showEnrollmentMessage();
}

// Show enrollment confirmation message
function showEnrollmentMessage() {
    // Create confirmation message
    const message = document.createElement('div');
    message.style.cssText = `
        position: fixed;
        top: 100px;
        right: 20px;
        background: linear-gradient(45deg, #4ECDC4, #44A08D);
        color: white;
        padding: 1rem 2rem;
        border-radius: 10px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        z-index: 10000;
        transform: translateX(400px);
        transition: transform 0.3s ease;
        font-weight: 500;
    `;
    message.textContent = '🎉 Successfully enrolled! Welcome to the course!';
    
    document.body.appendChild(message);
    
    // Animate in
    setTimeout(() => {
        message.style.transform = 'translateX(0)';
    }, 100);
    
    // Remove after 4 seconds
    setTimeout(() => {
        message.style.transform = 'translateX(400px)';
        setTimeout(() => {
            if (message.parentNode) {
                message.parentNode.removeChild(message);
            }
        }, 300);
    }, 4000);
}