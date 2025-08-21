<x-app-layout>
    @include('partials.navbar')
    <main class="Order_success" style="background: linear-gradient(135deg, #f8fafc, #ffffff); padding: 4em 1em;">
    <div class="container" style="max-width: 800px; margin: 0 auto; text-align: center;">
        <div class="success-checkmark" style="margin-bottom: 2em;">
            <div class="check-icon" style="width: 80px; height: 80px; position: relative; display: inline-block; vertical-align: top; border-radius: 50%; background: #34d399;">
                <span class="icon-line line-tip" style="position: absolute; top: 46%; left: 14%; width: 25px; height: 2px; background-color: #fff; transform-origin: left center; animation: tip-animating 0.5s;">
                </span>
                <span class="icon-line line-long" style="position: absolute; top: 38%; left: 28%; width: 47px; height: 2px; background-color: #fff; transform-origin: left center; animation: long-animating 0.5s;">
                </span>
                <div class="icon-circle" style="position: absolute; top: -4px; left: -4px; z-index: 10; width: 80px; height: 80px; border-radius: 50%; background-color: rgba(52, 211, 153, 0.2);"></div>
                <div class="icon-fix" style="position: absolute; top: 8px; left: 26px; width: 28px; height: 28px; background-color: #34d399; z-index: 1; border-radius: 50%;"></div>
            </div>
        </div>

        <h1 style="font-size: clamp(2rem, 5vw, 2.5rem); color: #1a237e; font-weight: 800; margin-bottom: 0.5em;">Success</h1>
        <p style="font-size: clamp(1rem, 4.5vw, 1.2rem); color: #64748b; margin-bottom: 1em;">Your order (<strong>{{ $order_number }}</strong>) has been submitted.</p>
        <p style="font-size: clamp(1rem, 4.5vw, 1.2rem); color: #64748b; margin-bottom: 1em;">Please enter your M-PESA PIN to complete this order.</p>
        <p style="font-size: clamp(1rem, 4.5vw, 1.2rem); color: #64748b; margin-bottom: 2em;">We will contact you in case we need any clarification.</p>

        <div class="actions">
            <a href="{{ route('shop') }}" class="action_btn" style="display: inline-block; background: #1a237e; color: white; text-decoration: none; padding: 0.75em 1.5em; border-radius: 24px; font-size: clamp(0.9rem, 4vw, 1rem); transition: background 0.3s ease;">Continue Shopping</a>
        </div>
    </div>
</main>

@include('partials.footer')
</x-app-layout>