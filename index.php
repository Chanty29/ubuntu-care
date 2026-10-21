 <?php
session_start();
require 'db.php';
$ngos     = $conn->query("SELECT COUNT(*) c FROM ngo_profiles")->fetch_assoc()["c"];
$requests = $conn->query("SELECT COUNT(*) c FROM beneficiary_requests")->fetch_assoc()["c"];
$volun    = $conn->query("SELECT COUNT(*) c FROM volunteer_signups")->fetch_assoc()["c"];
$pledged  = $conn->query("SELECT COALESCE(SUM(amount),0) c FROM donor_pledges")->fetch_assoc()["c"];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Ubuntu Care - I am because we are</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'navbar.php'; ?>
</main>

<header class="hero"><div class="hero-in">
  <h1>Help is closer than you think.</h1>
  <p>Ubuntu Care connects people in need with NGOs, donors and volunteers across South Africa, using AI to find the right help, near you, in seconds.</p>
  <div class="row">
    <a class="btn amber auto" href="register.php">Get started</a>
    <a class="btn ghost auto" href="login.php">Log in</a>
  </div>
</div></header>

<section class="section">
  <h2>How it works</h2><p class="sub">Four steps from "I need help" to "help is on the way"</p>
  <div class="grid">
    <div class="step"><div class="n">1</div><h3>Describe your need</h3><p>Write what you need in your own words. No forms full of jargon.</p></div>
    <div class="step"><div class="n">2</div><h3>AI understands it</h3><p>Our matching engine works out the type of help you need.</p></div>
    <div class="step"><div class="n">3</div><h3>Get ranked matches</h3><p>See the closest, most relevant organisations first.</p></div>
    <div class="step"><div class="n">4</div><h3>Everyone joins in</h3><p>Donors pledge, volunteers sign up, and NGOs see it all live.</p></div>
  </div>
</section>

<section class="band"><div class="section" style="padding:36px 20px">
  <div class="stats">
    <div class="stat"><strong><?php echo $ngos; ?></strong>Organisations</div>
    <div class="stat"><strong><?php echo $requests; ?></strong>Requests made</div>
    <div class="stat"><strong><?php echo $volun; ?></strong>Volunteer sign-ups</div>
    <div class="stat"><strong>R<?php echo number_format($pledged); ?></strong>Pledged</div>
  </div>
</div></section>

<section class="section">
  <h2>One platform, four roles</h2><p class="sub">Everyone in the community has a place</p>
  <div class="grid">
    <div class="card"><h3>Beneficiaries</h3>Ask for help and get matched to nearby organisations.</div>
    <div class="card"><h3>NGOs</h3>Manage your profile and see matches, pledges and volunteers live.</div>
    <div class="card"><h3>Donors</h3>Pledge support and see the impact it has.</div>
    <div class="card"><h3>Volunteers</h3>Pick a task and give your time where it counts.</div>
  </div>
</section>

<section class="section" style="padding-top:0">
  <div class="gallery">
    <img src="images/volunteers.jpg" alt="Volunteers" onerror="this.style.display='none'">
    <img src="images/food.jpg" alt="Food donation" onerror="this.style.display='none'">
    <img src="images/community.jpg" alt="Community" onerror="this.style.display='none'">
  </div>
</section>

<div class="foot">Ubuntu Care &middot; "I am because we are" &middot; Final year IT project, Richfield Graduate Institute of Technology</div>
</body>
</html>