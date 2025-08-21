
<footer style="background-color: #1a237e; color: #fff; padding: 60px 0;">
    <div class="container" style="max-width: 1200px; margin: 0 auto; display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 40px;">
        <div class="branding" style="display: flex; flex-direction: column; align-items: center; text-align: center;">
            <img src="{{ asset('assets/images/logo.jpg') }}" alt="Logo" class="rounded" style="max-width: 100px; height: auto; border-radius: 50%;">
            <h1 style="font-size: 24px; margin-top: 20px;">{{ config('app.name') }}</h1>
            <p class="slogan" style="font-size: 16px; margin-top: 10px;">Suppliers of Raw Shea, Body butter, Cocoa Butter, Black Soap, Essential & Carrier Oils</p>
            <p class="location" style="font-size: 16px; margin-top: 10px;">Nairobi CBD <br> Sasa Mall, Moi Avenue <br> Shop G20</p>
        </div>

        <div class="links" style="display: flex; flex-direction: column; align-items: center;">
            <h1 style="font-size: 24px; margin-bottom: 20px;">Explore</h1>
            <ul class="list_style_none" style="list-style: none; padding: 0; text-align: center;">
                <li style="margin-bottom: 10px;"><a href="{{ route('shop') }}" style="color: #fff; text-decoration: none;">Shop</a></li>
                <li style="margin-bottom: 10px;"><a href="{{ route('about') }}" style="color: #fff; text-decoration: none;">About</a></li>
                <li style="margin-bottom: 10px;"><a href="{{ route('contact') }}" style="color: #fff; text-decoration: none;">Contacts</a></li>
                <li style="margin-bottom: 10px;"><a href="{{ route('users.blogs') }}" style="color: #fff; text-decoration: none;">Blogs</a></li>
            </ul>
        </div>

        <div class="contacts" style="display: flex; flex-direction: column; align-items: center;">
            <h1 style="font-size: 24px; margin-bottom: 20px;">Contacts</h1>
            <div class="enquiries" style="text-align: center;">
                <p style="margin-bottom: 10px;">+254 711 894 267</p>
                <p style="margin-bottom: 10px;">info@shea254.com</p>
            </div>
            <div class="socials" style="display: flex; justify-content: center; margin-top: 20px;">
                <a href="https://wa.me/254711894267" target="_blank" style="margin-right: 10px;">
                    <img src="{{ asset('assets/images/whatsapp.png') }}" alt="Whats App" style="max-width: 30px; height: auto;">
                </a>

                <a href="https://www.instagram.com/shea.254" target="_blank" style="margin-right: 10px;">
                    <img src="{{ asset('assets/images/instagram.png') }}" alt="Instagram" style="max-width: 30px; height: auto;">
                </a>

                <a href="https://www.facebook.com/profile.php?id=100056436834853" target="_blank" style="margin-right: 10px;">
                    <img src="{{ asset('assets/images/facebook.png') }}" alt="Facebook" style="max-width: 30px; height: auto;">
                </a>

                <a href="https://www.tiktok.com/@shea.254?_t=8gJ9b2q8TP4&_r=1" target="_blank" style="margin-right: 10px;">
                    <img src="{{ asset('assets/images/tiktok.png') }}" alt="Tiktok" style="max-width: 30px; height: auto;">
                </a>
            </div>
        </div>
    </div>
    <p class="copyright" style="text-align: center; font-size: 14px; margin-top: 40px;">&copy; 2024 | Shea254 | All rights reserved</p>
</footer>