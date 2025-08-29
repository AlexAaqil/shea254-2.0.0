<?php

namespace App\Livewire\Pages\Dashboards;

use Livewire\Component;
use App\Enums\UserRoles;
use App\Models\User;
use App\Models\Sales\Sale;
use App\Models\Products\Product;
use App\Models\Products\ProductCategory;
use App\Models\Deliveries\DeliveryLocation;
use App\Models\Deliveries\DeliveryArea;
use App\Models\Blogs\Blog;
use App\Models\Blogs\BlogCategory;
use App\Models\Comment;

class Admin extends Component
{
    public function render()
    {
        $user_counts = User::selectRaw("
            COUNT(CASE WHEN user_level = ? THEN 1 END) as super_admins,
            COUNT(CASE WHEN user_level = ? THEN 1 END) as admins,
            COUNT(CASE WHEN user_level NOT IN (?, ?) THEN 1 END) as users
        ", [
            UserRoles::SUPER_ADMIN->value,
            UserRoles::ADMIN->value,
            UserRoles::SUPER_ADMIN->value,
            UserRoles::ADMIN->value
        ])->first();

        $count_orders = Sale::whereHas('order_delivery')->count();

        $count_products = Product::count();
        $count_product_categories = ProductCategory::count();

        $count_delivery_locations = DeliveryLocation::count();
        $count_delivery_areas = DeliveryArea::count();

        $count_blogs = Blog::count();
        $count_blog_categories = BlogCategory::count();

        $count_messages = Comment::count();


        return view('livewire.pages.dashboards.admin', [
            'count_super_admins' => $user_counts->super_admins,
            'count_admins' => $user_counts->admins,
            'count_users' => $user_counts->users,

            'count_orders' => $count_orders,

            'count_products' => $count_products,
            'count_product_categories' => $count_product_categories,

            'count_delivery_locations' => $count_delivery_locations,
            'count_delivery_areas' => $count_delivery_areas,

            'count_blogs' => $count_blogs,
            'count_blog_categories' => $count_blog_categories,

            'count_messages' => $count_messages,
        ]);
    }
}
