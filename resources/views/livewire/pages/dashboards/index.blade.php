<div class="Dashboard">
    <section class="Hero">
        <div class="container">
            <p>Hi, {{ auth()->user()->first_name }}</p>
        </div>
    </section>

    @if(auth()->user()->isAdmin())
        <livewire:pages.dashboards.admin />
    @else
        <livewire:pages.dashboards.user />
    @endif
</div>
