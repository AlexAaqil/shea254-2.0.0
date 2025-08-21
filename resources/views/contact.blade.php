<x-app-layout>
@include('partials.navbar')
<main class="Contact">
    <div class="contact-wrapper">
        @include('partials.messages')
        
        <div class="contact-card">
            <div class="contact-header">
                <h1>Get in Touch</h1>
                <p>We'd love to hear from you</p>
            </div>

            <div class="contact-content">
                <div class="contact-details">
                    <div class="contact-info">
                        <div class="contact-item">
                            <div class="icon-circle">
                                <i class="fas fa-phone"></i>
                            </div>
                            <div class="info">
                                <h3>Phone</h3>
                                <p>+254 711 894 267</p>
                            </div>
                        </div>
                        <div class="contact-item">
                            <div class="icon-circle">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div class="info">
                                <h3>Email</h3>
                                <p>info@shea254.com</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="contact-form">
                    <div class="custom_form">
                        <form action="{{ route('comments.store') }}" method="post">
                            @csrf
                            <div class="form-grid">
                                <div class="input_group">
                                    <label for="full_name">Full Name</label>
                                    <input type="text" name="full_name" id="full_name" placeholder="Enter your full name" value="{{ old('full_name') }}" autofocus />
                                    <span class="inline_alert">{{ $errors->first('full_name') }}</span>
                                </div>
                                
                                <div class="input_group">
                                    <label for="email">Email Address</label>
                                    <input type="email" name="email" id="email" placeholder="Enter your email" value="{{ old('email') }}" />
                                    <span class="inline_alert">{{ $errors->first('email') }}</span>
                                </div>
                            </div>

                            <div class="input_group">
                                <label for="phone_number">Phone Number</label>
                                <input type="text" name="phone_number" id="phone_number" placeholder="Enter your phone number" value="{{ old('phone_number') }}" />
                                <span class="inline_alert">{{ $errors->first('phone_number') }}</span>
                            </div>

                            <div class="input_group">
                                <label for="message">Message</label>
                                <textarea name="message" id="message" rows="5" placeholder="How can we help you?">{{ old('message') }}</textarea>
                                <span class="inline_alert">{{ $errors->first('message') }}</span>
                            </div>

                            <button type="submit">
                                Send Message
                                <i class="fas fa-arrow-right"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
@include('partials.footer')
</x-app-layout>