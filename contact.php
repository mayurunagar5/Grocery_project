<?php
require_once 'config/database.php';

// Check if user is logged in
if (!isUserLoggedIn()) {
    redirect('login.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Contact Us - FreshMart</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <style>
    :root {
      --primary: #2fb36d;
      --primary-dark: #249458;
      --text: #1f2937;
      --muted: #6b7280;
      --bg: #f7f7f7;
      --card: #ffffff;
      --border: #e5e7eb;
      --shadow-color: 24 39 75; /* rgb */
    }
    * { box-sizing: border-box; }
    html, body { margin: 0; padding: 0; font-family: 'Poppins', system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif; color: var(--text); background: var(--bg); }
    a { color: inherit; text-decoration: none; }

    header { position: sticky; top: 0; z-index: 40; background: #fff; border-bottom: 1px solid var(--border); }
    .container { width: min(1100px, 100% - 2rem); margin: 0 auto; }
    .nav { display: flex; align-items: center; justify-content: space-between; padding: 0.75rem 0; gap: 1rem; }
    .brand { display: inline-flex; align-items: center; gap: .5rem; font-weight: 700; color: var(--primary); }
    .brand svg { width: 26px; height: 26px; }
    .back { color: var(--text); border: 1px solid var(--border); border-radius: 10px; padding: .45rem .7rem; font-weight: 600; background: #fff; }
    .back:hover { box-shadow: 0 8px 20px rgba(var(--shadow-color) / 0.12); }

    main { padding: 1.5rem 0 2.25rem; }

    .hero {
      background: linear-gradient(90deg, rgba(47,179,109,0.08), rgba(255,209,102,0.15));
      border: 1px solid var(--border);
      border-radius: 14px;
      padding: 1.25rem 1.25rem;
      display: flex; align-items: center; justify-content: space-between; gap: 1rem;
    }

    .grid { display: grid; grid-template-columns: 1.2fr 1fr; gap: 1rem; margin-top: 1rem; }

    .card { background: #fff; border: 1px solid var(--border); border-radius: 14px; box-shadow: 0 8px 20px rgba(var(--shadow-color) / 0.06); }
    .card h2 { margin: 0; font-size: 1.25rem; }
    .card-header { padding: .9rem 1rem; border-bottom: 1px solid var(--border); display: flex; align-items: center; justify-content: space-between; }
    .card-body { padding: 1rem; }

    form { display: grid; gap: .75rem; }
    label { font-weight: 600; font-size: .95rem; }
    input, textarea, select { width: 100%; border: 1px solid var(--border); border-radius: 12px; padding: .7rem .9rem; font-family: inherit; }
    textarea { min-height: 120px; resize: vertical; }
    .row { display: grid; grid-template-columns: 1fr 1fr; gap: .75rem; }
    .hint { color: var(--muted); font-size: .9rem; }

    .btn { border: none; background: var(--primary); color: #fff; padding: .75rem 1rem; border-radius: 12px; font-weight: 700; cursor: pointer; }
    .btn:hover { background: var(--primary-dark); }

    .info { display: grid; gap: .5rem; }
    .info a { color: var(--primary); font-weight: 600; }

    .map { height: 260px; border: 1px solid var(--border); border-radius: 12px; overflow: hidden; background: #e5f7ec; display: grid; place-items: center; color: var(--muted); }

    .success { display: none; padding: .75rem 1rem; border: 1px solid #bbf7d0; background: #ecfdf5; color: #065f46; border-radius: 12px; font-weight: 600; margin-top: .5rem; }

    @media (max-width: 900px) {
      .grid { grid-template-columns: 1fr; }
      .row { grid-template-columns: 1fr; }
    }
  </style>
</head>
<body>
  <header>
    <div class="container nav">
      <a class="brand" href="Homepage.php">
        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
          <path d="M5 12c0-4.418 3.582-8 8-8a6 6 0 110 12H5z" fill="currentColor"/>
          <path d="M5 12c0 3.866 3.134 7 7 7 2.761 0 5-2.239 5-5H5z" fill="#1f8a58"/>
        </svg>
        FreshMart
      </a>
      <div style="display: flex; align-items: center; gap: 1rem;">
        <span style="color: var(--muted); font-weight: 600;">Welcome, <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?></span>
        <a class="back" href="homepage.php">← Back to Home</a>
        <a href="api/logout.php" style="color: var(--danger); font-weight: 600; padding: 0.45rem 0.7rem; border: 1px solid var(--danger); border-radius: 10px; background: #fff;">Logout</a>
      </div>
    </div>
  </header>

  <main class="container">
    <section class="hero">
      <div>
        <h1 style="margin:0 0 .25rem;">Contact Us</h1>
        <p class="hint" style="margin:0;">We’d love to hear from you. Send us a message and our team will reply within 24 hours.</p>
      </div>
      <div class="hint">Support hours: Mon–Sat 9:00–18:00</div>
    </section>

    <div class="grid">
      <section class="card" aria-labelledby="form-heading">
        <div class="card-header">
          <h2 id="form-heading">Send a message</h2>
        </div>
        <div class="card-body">
          <form id="contactForm" novalidate>
            <div class="row">
              <div>
                <label for="name">Full name</label>
                <input id="name" name="name" type="text" placeholder="Your Name" required />
              </div>
              <div>
                <label for="email">Email address</label>
                <input id="email" name="email" type="email" placeholder="test123@gmail.com" required />
              </div>
            </div>
            <div class="row">
              <div>
                <label for="phone">Phone (optional)</label>
                <input id="phone" name="phone" type="tel" placeholder="+91 1234567890" />
              </div>
              <div>
                <label for="topic">Topic</label>
                <select id="topic" name="topic" required>
                  <option value="" disabled selected>Select a topic</option>
                  <option>Orders</option>
                  <option>Delivery</option>
                  <option>Returns</option>
                  <option>Account</option>
                  <option>Other</option>
                </select>
              </div>
            </div>
            <div>
              <label for="message">Message</label>
              <textarea id="message" name="message" placeholder="How can we help?" required></textarea>
              <p class="hint">By submitting, you agree to be contacted about your request.</p>
            </div>
            <button class="btn" type="submit">Send message</button>
            <div id="success" class="success" role="status" aria-live="polite">Thanks! Your message has been sent.</div>
          </form>
        </div>
      </section>

      <aside class="card" aria-labelledby="info-heading">
        <div class="card-header">
          <h2 id="info-heading">Support</h2>
        </div>
        <div class="card-body info">
          <div>
            <div><strong>Email</strong></div>
            <a href="mailto:support@freshmart.example">support@freshmart.example</a>
          </div>
          <div>
            <div><strong>Phone</strong></div>
            <a href="tel:+15551234567">+1 (555) 123-4567</a>
          </div>
          <div>
            <div><strong>Address</strong></div>
            <div>123 Market Street, Springfield</div>
          </div>
          <div>
            <div><strong>Follow</strong></div>
            <div class="hint">Instagram · Facebook · Twitter</div>
          </div>
          <div class="map" aria-label="Map placeholder">Map preview</div>
        </div>
      </aside>
    </div>
  </main>

  <script>
    const form = document.getElementById('contactForm');
    const success = document.getElementById('success');
    const submitBtn = form.querySelector('button[type="submit"]');

    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      
      // Disable submit button to prevent double submission
      submitBtn.disabled = true;
      submitBtn.textContent = 'Sending...';
      
      const data = new FormData(form);
      
      // Simple client validation
      for (const key of ['name','email','topic','message']) {
        if (!String(data.get(key) || '').trim()) {
          alert('Please fill out the ' + key + ' field.');
          submitBtn.disabled = false;
          submitBtn.textContent = 'Send message';
          return;
        }
      }

      try {
        // Send form data to PHP script
        const response = await fetch('api/contact_submit.php', {
          method: 'POST',
          body: data
        });

        const result = await response.json();

        if (result.success) {
          // Show success message
          success.style.display = 'block';
          success.textContent = result.message;
          success.style.background = '#ecfdf5';
          success.style.borderColor = '#bbf7d0';
          success.style.color = '#065f46';
          
          // Reset form
          form.reset();
          const topic = document.getElementById('topic');
          topic.selectedIndex = 0;
        } else {
          // Show error message
          success.style.display = 'block';
          success.textContent = result.error || 'An error occurred. Please try again.';
          success.style.background = '#fef2f2';
          success.style.borderColor = '#fecaca';
          success.style.color = '#991b1b';
        }
      } catch (error) {
        // Show error message
        success.style.display = 'block';
        success.textContent = 'Network error. Please check your connection and try again.';
        success.style.background = '#fef2f2';
        success.style.borderColor = '#fecaca';
        success.style.color = '#991b1b';
      } finally {
        // Re-enable submit button
        submitBtn.disabled = false;
        submitBtn.textContent = 'Send message';
      }
    });
  </script>
</body>
</html> 