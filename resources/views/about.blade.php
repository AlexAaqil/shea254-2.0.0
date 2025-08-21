<x-app-layout>
@include('partials.navbar')
<main class="about-main">
    <!-- Hero Section -->
    <div class="about-hero" style="position: relative; min-height: 20vh; display: flex;align-items: center;justify-content: center;padding: 6em 2em;overflow: hidden;">
    <div class="hero-content" style="position: relative;z-index: 2;text-align: center;">
      <h1 style="font-size: clamp(2.5em, 5vw, 4em);font-weight: 800;margin-bottom: 0.5em;color: #1a237e;">
        <span class="gradient-text" style="background: linear-gradient(135deg, #1a237e, #5c6bc0);-webkit-background-clip: text;color: transparent;">Why Shea254?</span>
      </h1>
      <div class="subtitle" style="font-size: 1.2em;color: #64748b;max-width: 600px;margin: 0 auto;">Transforming natural beauty through innovation</div>
    </div>
    <div class="hex-grid" style="position: absolute;top: 0;left: 0;width: 100%;height: 100%;background: linear-gradient(120deg, #ffffff 0%, #ffffff 100%) center center / cover no-repeat,radial-gradient(circle, rgba(92, 107, 192, 0.1) 0%, rgba(26, 35, 126, 0.05) 100%);opacity: 0.5;"></div>
  </div>

    <!-- Mission Statements -->
    <section class="mission-section">
        <div class="container">
            <div class="mission-cards">
                <div class="mission-card" data-aos="fade-up" data-aos-delay="100">
                    <div class="card-icon">
                        <i class="fas fa-leaf"></i>
                    </div>
                    <h2>Natural</h2>
                    <p>Ingredients are obtained from natural resources and maintain their original chemical shape and structure.</p>
                    <div class="card-decoration"></div>
                </div>

                <div class="mission-card" data-aos="fade-up" data-aos-delay="200">
                    <div class="card-icon">
                        <i class="fas fa-truck-fast"></i>
                    </div>
                    <h2>Naturally Delivered</h2>
                    <p>Ingredients are harvested or picked and made to undergo some form of chemical reaction e.g. fermentation or distillation.</p>
                    <div class="card-decoration"></div>
                </div>

                <div class="mission-card" data-aos="fade-up" data-aos-delay="300">
                    <div class="card-icon">
                        <i class="fas fa-flask"></i>
                    </div>
                    <h2>Nature Identical</h2>
                    <p>Ingredients are synthetically processed but identical in their chemical structure to the ingredients found in nature.</p>
                    <div class="card-decoration"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Location Section -->
    <section class="location-section" style="padding: 6em 2em;background: linear-gradient(180deg, #f8fafc, #ffffff);">
    <div class="container" style="max-width: 1200px;margin: 0 auto;display: grid;grid-template-columns: 1fr;gap: 3em;">
      <div class="location-content" data-aos="fade-right" style="position: relative;z-index: 2;text-align: center;">
        <div class="location-info">
          <h2 style="font-size: 2em;color: #1a237e;margin-bottom: 1.5em;">Visit Our Store</h2>
          <div class="info-card" style="background: white;padding: 2em;border-radius: 24px;box-shadow: 0 4px 30px rgba(0, 0, 0, 0.05),0 2px 4px rgba(0, 0, 0, 0.02);">
            <div class="info-item" style="display: flex;align-items: flex-start;gap: 1em;padding: 1em 0;">
              <i class="fas fa-location-dot" style="font-size: 1.2em;color: #5c6bc0;margin-top: 0.2em;"></i>
              <p style="color: #64748b;line-height: 1.6;">Nairobi CBD<br>Sasa Mall, Moi Avenue<br>Shop G20</p>
            </div>
            <div class="info-item" style="display: flex;align-items: flex-start;gap: 1em;padding: 1em 0;">
              <i class="fas fa-clock" style="font-size: 1.2em;color: #5c6bc0;margin-top: 0.2em;"></i>
              <p style="color: #64748b;line-height: 1.6;">Business Hours:<br>08:00 A.M - 05:00 P.M</p>
            </div>
          </div>
        </div>
      </div>
      
      <div class="map-container" data-aos="fade-left" style="position: relative;padding-top: 56.25%;border-radius: 24px;overflow: hidden;box-shadow: 0 4px 30px rgba(0, 0, 0, 0.05),0 2px 4px rgba(0, 0, 0, 0.02);">
        <iframe src="https://www.google.com/maps/embed?pb=!4v1712003822208!6m8!1m7!1snjt8vM8lwBBNAfQHF3ccfw!2m2!1d-1.282796377812188!2d36.82287824180283!3f28.984308039843892!4f-0.7431016627225233!5f0.7820865974627469" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade" style="position: absolute;top: 0;left: 0;width: 100%;height: 100%;border: none;"></iframe>
      </div>
    </div>
  </section>
</main>
@include('partials.footer')
</x-app-layout>