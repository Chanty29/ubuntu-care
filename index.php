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
  <title>Ubuntu Care - The right help, matched in seconds</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'navbar.php'; ?>
</main>

<header class="hero"><div class="hero-in">
  <div class="hero-text">
    <p class="eyebrow">AI-POWERED COMMUNITY SUPPORT</p>
    <h1>The right help, matched in seconds.</h1>
    <p class="lead">One platform for beneficiaries, NGOs, donors and volunteers. Requests are understood by AI, ranked by relevance and distance, and tracked live.</p>
    <div class="btns">
      <a class="btn coral auto" href="register.php">Get started</a>
      <a class="btn ghost auto" href="login.php">Log in</a>
    </div>
  </div>
  <div class="win">
    <div class="win-bar"><i></i><i></i><i></i><span style="margin-left:10px">NGO dashboard &middot; preview with sample data</span></div>
    <div class="win-body">
      <div class="win-side"><b>Soweto Food Hub</b><div class="on">Overview</div><div>Requests</div><div>Pledges</div><div>Volunteers</div></div>
      <div class="win-main">
        <div class="tiles">
          <div><strong>12</strong>Matched requests</div>
          <div><strong>R4 500</strong>Pledged</div>
          <div><strong>5</strong>Volunteers</div>
        </div>
        <div style="font-weight:700;font-size:14px;margin-bottom:6px">Incoming matched requests</div>
        <div class="win-row"><span>My children go to bed without eating</span><strong>97%</strong></div>
        <div class="win-row"><span>Need food parcels for the week</span><strong>92%</strong></div>
        <div class="win-row"><span>Hungry and no money for groceries</span><strong>90%</strong></div>
      </div>
    </div>
  </div>
</div></header>

<section class="trust"><div class="trust-in">
  <div><i></i>Requests understood by AI</div>
  <div><i></i>Live status tracking for everyone</div>
  <div><i></i>Works on any phone browser</div>
</div></section>

<section class="section" style="padding-bottom:24px">
  <h2>How it works</h2>
  <div class="steps">
    <div class="s"><div class="n">01</div><h3>Describe your need</h3><p>Write what you need in your own words.</p></div>
    <div class="s"><div class="n">02</div><h3>AI understands it</h3><p>The matching engine works out the type of help.</p></div>
    <div class="s"><div class="n">03</div><h3>Get ranked matches</h3><p>The closest, most relevant organisations come first.</p></div>
    <div class="s"><div class="n">04</div><h3>Track progress</h3><p>See each request move from awaiting, to in progress, to completed.</p></div>
  </div>
</section>

<section class="section" style="padding-top:32px;padding-bottom:24px">
  <div class="stats">
    <div class="stat"><strong><?php echo $ngos; ?></strong>Organisations</div>
    <div class="stat"><strong><?php echo $requests; ?></strong>Requests made</div>
    <div class="stat"><strong><?php echo $volun; ?></strong>Volunteer sign-ups</div>
    <div class="stat"><strong>R<?php echo number_format($pledged); ?></strong>Pledged</div>
  </div>
</section>

<section class="section">
  <h2>Built for four roles</h2>
  <div class="roles">
    <div class="role"><div class="ico"><svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#0057B8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2 M12 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8"/></svg></div>
      <div><h3>Beneficiaries</h3><p>Ask for help, get matched to nearby organisations, and track your request.</p><a href="register.php">Request support &rarr;</a></div></div>
    <div class="role"><div class="ico"><svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#0057B8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 21h18 M5 21V7l7-4 7 4v14 M9 21v-6h6v6"/></svg></div>
      <div><h3>NGOs</h3><p>Manage your profile, update request statuses, and see pledges and volunteers live.</p><a href="register.php">Register an organisation &rarr;</a></div></div>
    <div class="role"><div class="ico"><svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#0057B8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.7l-1-1.1a5.5 5.5 0 0 0-7.8 7.8l1 1.1L12 21l7.8-7.5 1-1.1a5.5 5.5 0 0 0 0-7.8z"/></svg></div>
      <div><h3>Donors</h3><p>Pledge support and see when it is received and what impact it has.</p><a href="register.php">Pledge support &rarr;</a></div></div>
    <div class="role"><div class="ico"><svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#0057B8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2 M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8 M23 21v-2a4 4 0 0 0-3-3.9 M16 3.1a4 4 0 0 1 0 7.8"/></svg></div>
      <div><h3>Volunteers</h3><p>Pick a task, get confirmed, and give your time where it counts.</p><a href="register.php">Find a task &rarr;</a></div></div>
  </div>
</section>

<section class="cta-band"><div class="cta-in">
  <h2>Ready to see Ubuntu Care in action?</h2>
  <a class="btn auto" href="register.php" style="padding:15px 30px">Create an account</a>
</div></section>

<div class="foot">Ubuntu Care &middot; Final year IT project, Richfield Graduate Institute of Technology</div>
</body>
</html>