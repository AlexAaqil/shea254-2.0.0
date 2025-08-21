<x-app-layout>
    @include('partials.navbar')

    <section class="Dashboard" style="background: linear-gradient(135deg, #f8fafc, #ffffff); padding: 4em 1em;">
    <div class="container" style="max-width: 1200px; margin: 0 auto; text-align: center;">
        <h1 style="font-size: clamp(2rem, 5vw, 2.5rem); color: #1a237e; font-weight: 800; margin-bottom: 1em;">Hi {{ Auth::user()->first_name .' '. Auth::user()->last_name }}</h1>
        
        <div class="actions" style="display: flex; justify-content: center; gap: 1em; margin-bottom: 2em;">
            <a href="{{ route('profile.edit') }}" class="btn_link" style="display: inline-block; background: white; color: #1a237e; text-decoration: none; padding: 0.75em 1.5em; border-radius: 24px; font-size: clamp(0.9rem, 4vw, 1rem); box-shadow: 0 4px 30px rgba(0, 0, 0, 0.05), 0 2px 4px rgba(0, 0, 0, 0.02); transition: background 0.3s ease, color 0.3s ease;">
                Update Profile
            </a>
            <div class="custom_form">
                <form action="{{ route('logout') }}" method="post" style="display: inline-block;">
                    @csrf
    
                    <button type="submit" class="btn_danger" style="display: inline-block; background: #ef4444; color: white; text-decoration: none; padding: 0.75em 1.5em; border-radius: 24px; font-size: clamp(0.9rem, 4vw, 1rem); border: none; cursor: pointer; box-shadow: 0 4px 30px rgba(0, 0, 0, 0.05), 0 2px 4px rgba(0, 0, 0, 0.02); transition: background 0.3s ease;">Logout</button>
                </form>
            </div>
        </div>

        <div class="dashboard-cards" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2em;">
            <div class="card" style="background: white; border-radius: 24px; box-shadow: 0 4px 30px rgba(0, 0, 0, 0.05), 0 2px 4px rgba(0, 0, 0, 0.02); padding: 2em; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);">
                <div class="icon" style="background: linear-gradient(135deg, #1a237e, #5c6bc0); width: 60px; height: 60px; border-radius: 16px; display: flex; align-items: center; justify-content: center; margin-bottom: 1.5em;">
                    <i class="fas fa-shopping-bag" style="color: white; font-size: 1.5em;"></i>
                </div>
                <h2 style="font-size: clamp(1.2rem, 4.5vw, 1.5rem); color: #1a237e; margin-bottom: 0.5em;">Order History</h2>
                <p style="font-size: clamp(0.9rem, 4vw, 1rem); color: #64748b; line-height: 1.6;">View and manage your past orders.</p>
                <a href="{{ route('orders.index') }}" class="btn_link" style="display: inline-block; background: #1a237e; color: white; text-decoration: none; padding: 0.75em 1.5em; border-radius: 24px; font-size: clamp(0.9rem, 4vw, 1rem); margin-top: 1em; transition: background 0.3s ease;">
                    View Orders
                </a>
            </div>
            <div class="card" style="background: white; border-radius: 24px; box-shadow: 0 4px 30px rgba(0, 0, 0, 0.05), 0 2px 4px rgba(0, 0, 0, 0.02); padding: 2em; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);">
                <div class="icon" style="background: linear-gradient(135deg, #1a237e, #5c6bc0); width: 60px; height: 60px; border-radius: 16px; display: flex; align-items: center; justify-content: center; margin-bottom: 1.5em;">
                    <i class="fas fa-clipboard-list" style="color: white; font-size: 1.5em;"></i>
                </div>
                <h2 style="font-size: clamp(1.2rem, 4.5vw, 1.5rem); color: #1a237e; margin-bottom: 0.5em;">Product Reviews</h2>
                <p style="font-size: clamp(0.9rem, 4vw, 1rem); color: #64748b; line-height: 1.6;">View and manage your product reviews.</p>
                <a href="{{ route('product-reviews.index') }}" class="btn_link" style="display: inline-block; background: #1a237e; color: white; text-decoration: none; padding: 0.75em 1.5em; border-radius: 24px; font-size: clamp(0.9rem, 4vw, 1rem); margin-top: 1em; transition: background 0.3s ease;">
                    View Reviews
                </a>
            </div>
        </div>
    </div>
</section>

@include('partials.footer')

</x-app-layout>