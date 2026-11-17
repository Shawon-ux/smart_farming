<style>
.sf-footer{background:linear-gradient(160deg,#1f3a1d 0%,#2e5a27 100%);color:#e6efd8;font-family:'Poppins','Segoe UI',sans-serif;margin-top:60px;position:relative}
.sf-footer::before{content:"";display:block;height:5px;background:linear-gradient(90deg,#b7cf2a,#6fa33b,#b7cf2a)}
.sf-footer .sf-wrap{max-width:1180px;margin:0 auto;padding:56px 20px 28px}
.sf-footer .sf-grid{display:grid;grid-template-columns:1.5fr 1fr 1.2fr 1.3fr;gap:36px}
.sf-footer h3{color:#fff;font-size:21px;font-weight:700;margin:0 0 20px;position:relative;padding-bottom:10px}
.sf-footer h3::after{content:"";position:absolute;left:0;bottom:0;width:36px;height:3px;border-radius:3px;background:#b7cf2a}
.sf-footer .sf-brand{font-size:28px;font-weight:700;color:#fff;margin-bottom:12px}
.sf-footer .sf-brand span{color:#b7cf2a}
.sf-footer p{color:#cbd9b5;font-size:16px;line-height:1.7;margin:0 0 16px}
.sf-footer ul{list-style:none;margin:0;padding:0}
.sf-footer li{margin-bottom:10px;font-size:16px;color:#cbd9b5}
.sf-footer a{color:#e6efd8;text-decoration:none;transition:.2s}
.sf-footer a:hover{color:#b7cf2a;padding-left:4px}
.sf-footer .sf-links li a::before{content:"\203A";color:#b7cf2a;margin-right:8px;font-weight:700}
.sf-footer .sf-contact li{display:flex;gap:12px;align-items:flex-start}
.sf-footer .sf-contact i{width:34px;height:34px;flex:0 0 34px;border-radius:50%;background:rgba(183,207,42,.18);color:#b7cf2a;display:flex;align-items:center;justify-content:center;font-size:16px}
.sf-footer .sf-bottom{margin-top:40px;padding-top:20px;border-top:1px solid rgba(255,255,255,.14);display:flex;flex-wrap:wrap;gap:10px;justify-content:space-between;font-size:15px;color:#aebf98}
@media(max-width:992px){.sf-footer .sf-grid{grid-template-columns:1fr 1fr}}
@media(max-width:576px){.sf-footer .sf-grid{grid-template-columns:1fr}.sf-footer .sf-bottom{justify-content:center;text-align:center}}
</style>
<footer class="sf-footer">
  <div class="sf-wrap">
    <div class="sf-grid">
      <div>
        <div class="sf-brand">Smart<span>Farming</span></div>
        <p>Practical farming information, tools and guidance to help farmers grow better and earn more.</p>
      </div>

      <div>
        <h3>Explore</h3>
        <ul class="sf-links">
          <li><a href="index.php">Home</a></li>
          <li><a href="about.php">About</a></li>
          <li><a href="service.php">Service</a></li>
          <li><a href="project.php">Projects</a></li>
          <li><a href="testimonail.php">Testimonial</a></li>
          <li><a href="contact.php">Contact us</a></li>
        </ul>
      </div>

      <div>
        <h3>Farming Tools</h3>
        <ul class="sf-links">
          <li><a href="crop_calendar.php">Crop Calendar</a></li>
          <li><a href="calculator.php">Cost Calculator</a></li>
          <li><a href="fertilizer_list.php">Fertilizers</a></li>
          <li><a href="pesticide_list.php">Pesticides</a></li>
          <li><a href="equipment_list.php">Equipment</a></li>
        </ul>
      </div>

      <div>
        <h3>Contact</h3>
        <ul class="sf-contact">
          <li><i class="fa fa-phone" aria-hidden="true"></i><span>01885731571</span></li>
          <li><i class="fa fa-envelope" aria-hidden="true"></i><a href="mailto:md.arif@gmail.com">md.arif@gmail.com</a></li>
        </ul>
      </div>
    </div>

    <div class="sf-bottom">
      <span>&copy; <?= date('Y') ?> Smart Farming. All rights reserved.</span>
      <span>Grow smart. Farm better.</span>
    </div>
  </div>
</footer>
