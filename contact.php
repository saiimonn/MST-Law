<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>MST LAW</title>
  <link rel="stylesheet" href="css/contact.css">
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
</head>
<body>

  <header>
    <div class="menu-toggle">
      <span class="bar"></span>
      <span class="bar"></span>
      <span class="bar"></span>
    </div>
    <nav class="left">
      <a href="index.html">Home</a>
      <a href="faq.html">FAQ</a>
      <a href="about.html">About</a>
      <a href="attorneys.html">Attorneys</a>
    </nav>
    <nav class="right" style="display: flex; gap: 10px;">
      <a href="logins/login.php" style="font-size: 0.6rem; padding: 0.3125rem 0.625rem;">Login</a>
      <a href="logins/register.php" class="register" style="font-size: 0.6rem; padding: 0.3125rem 0.625rem; background-color: black; color: white;">Register</a>
    </nav>
  </header>

  <section id="contact">
    <h1 class="section-header">Contact</h1>
    <div class="contact-wrapper">
      <form id="contact-form" class="form-horizontal" role="form">
        <div class="form-group">
          <input type="text" class="form-control" id="name" placeholder="NAME" name="name" required>
        </div>
        <div class="form-group">
          <input type="email" class="form-control" id="email" placeholder="EMAIL" name="email" required>
        </div>
        <div class="form-group">
          <input type="text" class="form-control" id="subject" placeholder="SUBJECT" name="subject" required>
        </div>
        <textarea class="form-control" rows="10" placeholder="MESSAGE" name="message" required></textarea>
        <button class="btn btn-primary send-button" id="submit" type="submit" value="SEND">
          <div class="alt-send-button">
            <i class="fa fa-paper-plane" style="color: white;"></i><span class="send-text">SEND</span>
          </div>
        </button>
      </form>
      <div class="direct-contact-container">
        <ul class="contact-list">
          <li class="list-item"><i class="fa fa-map-marker fa-2x"></i><span class="contact-text place">Ground Floor, Eastern Shipping Lines Bldg, M.J. Cuenco Avenue, Corner Magallanes St., Cebu City, 6000</span></li>
          <li class="list-item"><i class="fa fa-phone fa-2x"></i><span class="contact-text phone">(032) 254 4035</span></li>
        </ul>
      </div>
    </div>
    <div class="business-hours">
      <h2>Business Hours</h2>
      <ul class="hours-list">
        <li>Sunday: Closed</li>
        <li>Monday - Friday: 08:00 AM - 05:00 PM</li>
        <li>Saturday: Closed</li>
      </ul>
    </div>
  </section>
  <script src="js/infoPageMenuToggle.js"></script>
  <script>
    document.querySelector('#contact-form').addEventListener('submit', function(e) {
      e.preventDefault();
      const formData = new FormData(this);
      fetch('https://mstlaw.dcism.org/includes/send_contact_email.php', {
  method: 'POST',
  body: formData
})
.then(async response => {
  console.log('Raw response:', response);
  const text = await response.text();
  console.log('Raw text response:', text);
  try {
    const data = JSON.parse(text);
    console.log('Parsed response:', data);
    if (data.status === 'success') {
      alert('Message sent successfully!');
      document.querySelector('#contact-form').reset();
    } else {
      alert(data.message || 'Failed to send message. Please try again.');
    }
  } catch (err) {
    console.error('JSON parse error:', err);
    alert('Server returned invalid JSON. Check console for details.');
  }
})
.catch(error => {
  console.error('Network or Fetch Error:', error);
  alert('A network error occurred. See console.');
});
    });
  </script>
</body>
</html>