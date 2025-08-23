<div class="ContactPage">
    <section class="Hero">
        <div class="container">
            <h1>Get in Touch</h1>
            <p>We'd love to hear from you</p>
        </div>
    </section>

    <section class="Contact">
        <div class="container">
            <div class="contact_info">
                <div class="info">
                    <div class="icon">
                        <x-svgs.telephone />
                    </div>

                    <p>
                        <span>Phone</span>
                        <span>{{ config('app.phone_number') }}</span>
                    </p>
                </div>

                <div class="info">
                    <div class="icon">
                        <x-svgs.email />
                    </div>

                    <p>
                        <span>Email</span>
                        <span>{{ config('app.email') }}</span>
                    </p>
                </div>
            </div>

            <div class="contact_form">
                <livewire:pages.contact-messages.form />
            </div>
        </div>
    </section>
</div>
