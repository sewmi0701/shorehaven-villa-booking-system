<?php
require __DIR__ . '/includes/bootstrap.php';
$pageTitle = 'An intimate stay by the sea'; $activePage = 'home';
$featured = db()->query("SELECT * FROM rooms WHERE room_status = 'active' ORDER BY room_id LIMIT 3")->fetchAll();
require __DIR__ . '/includes/header.php';
?>
<section class="hero">
  <div class="hero-art" aria-hidden="true"></div><div class="hero-shade"></div>
  <div class="container hero-content">
    <div class="eyebrow light">WELCOME TO MIRISSA, SRI LANKA <span>✦</span> THE SLOW COAST</div>
    <h1>Where the ocean<br><em>feels like home.</em></h1>
    <p>Wake up to soft ocean breezes, sun-warmed sands and the luxury of doing absolutely nothing.</p>
    <a class="btn btn-cream" href="<?= e(url('rooms.php')) ?>">Explore our rooms <span>↗</span></a>
    <div class="hero-caption">05°56′N 80°28′E <span> / </span> MADE FOR UNHURRIED DAYS</div>
  </div>
</section>
<div class="booking-bar-outer"><div class="container">
  <form class="booking-bar" action="<?= e(url('rooms.php')) ?>" method="get">
    <label>CHECK IN <input type="date" name="check_in" min="<?= date('Y-m-d') ?>" required></label>
    <label>CHECK OUT <input type="date" name="check_out" min="<?= date('Y-m-d', strtotime('+1 day')) ?>" required></label>
    <label>GUESTS <select name="guests"><option value="1">1 Guest</option><option value="2" selected>2 Guests</option><option value="3">3 Guests</option><option value="4">4 Guests</option><option value="5">5 Guests</option><option value="6">6 Guests</option></select></label>
    <button class="btn btn-primary" type="submit">Check availability <span>↗</span></button>
  </form>
</div></div>
<section class="section intro" id="about"><div class="container intro-grid">
  <div><span class="eyebrow">A DIFFERENT PACE OF LIFE</span><h2>Some places just<br><em>stay with you.</em></h2></div>
  <div><p>Hidden along Sri Lanka's southern coast, Shorehaven is a thoughtfully imagined little retreat for curious travellers and quiet-day dreamers. Natural textures, open horizons and warm hospitality invite you to simply be.</p><a class="text-link" href="<?= e(url('rooms.php')) ?>">Discover your escape <span>↗</span></a></div>
</div></section>
<section class="section rooms-section" id="accommodation"><div class="container">
  <div class="section-heading"><div><span class="eyebrow">YOUR SPACE TO UNWIND</span><h2>Stay a little <em>longer.</em></h2></div><a class="text-link" href="<?= e(url('rooms.php')) ?>">View all accommodation ↗</a></div>
  <div class="room-grid">
  <?php foreach ($featured as $room): ?>
    <article class="room-card"><a class="room-image" href="<?= e(url('room.php?id=' . $room['room_id'])) ?>"><img src="<?= e(room_image($room)) ?>" data-fallback="<?= e(room_image_fallback($room)) ?>" loading="lazy" alt="Photograph of <?= e($room['room_name']) ?> room"><span class="image-arrow">↗</span></a><div class="room-card-content"><div class="room-type"><?= e($room['room_type']) ?> · UP TO <?= (int)$room['max_guests'] ?> GUESTS</div><h3><?= e($room['room_name']) ?></h3><div class="room-meta"><span>From <strong><?= e(money((float)$room['price_per_night'])) ?></strong> / night</span><a href="<?= e(url('room.php?id=' . $room['room_id'])) ?>">Details ↗</a></div></div></article>
  <?php endforeach; ?>
  </div>
</div></section>
<section class="experience section" id="experience"><div class="container experience-grid">
  <div class="experience-image"><img src="<?= e(url('assets/images/villa.svg')) ?>" alt="Illustration of a tropical coastal villa"></div>
  <div class="experience-text"><span class="eyebrow">THE SHOREHAVEN WAY</span><h2>Little moments.<br><em>Lasting memories.</em></h2><p>The best days have no itinerary. Have breakfast under the palms, wander barefoot to the beach, explore the coastline, and come home to a room made for rest.</p>
  <div class="experience-list"><div><span>☼</span><strong>Steps from the coast</strong><small>Sea, sand and endless sunsets</small></div><div><span>✳</span><strong>Thoughtfully designed rooms</strong><small>Comfort with a coastal soul</small></div><div><span>♨</span><strong>All the little comforts</strong><small>Wi-Fi, air conditioning and more</small></div></div>
  </div>
</div></section>
<section class="cta"><div class="container"><span class="eyebrow light">YOUR NEXT CHAPTER STARTS HERE</span><h2>Leave room for <em>wonder.</em></h2><p>Good things come to those who take a break.</p><a class="btn btn-cream" href="<?= e(url('rooms.php')) ?>">Find your room ↗</a></div></section>
<?php require __DIR__ . '/includes/footer.php'; ?>
