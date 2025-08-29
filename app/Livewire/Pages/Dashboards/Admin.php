<?php

namespace App\Livewire\Pages\Dashboards;

use Livewire\Component;
use App\Enums\UserRoles;
use App\Models\User;
use App\Models\Sales\Sale;
use App\Models\Sales\OrderDelivery;
use App\Models\Sales\OrderItem;
use App\Models\Products\Product;
use App\Models\Products\ProductCategory;
use App\Models\Deliveries\DeliveryLocation;
use App\Models\Deliveries\DeliveryArea;
use App\Models\Blogs\Blog;
use App\Models\Blogs\BlogCategory;
use App\Models\Comment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

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



        $gross_sales = Sale::sum('total_amount');
        $net_sales = Sale::sum('total_amount') - Sale::sum('discount');
        $cost_of_sales = OrderItem::sum('buying_price');
        $gross_profit = $net_sales - $cost_of_sales;

        $monthly_sales = Sale::selectRaw("MONTH(created_at) as month, SUM(total_amount) as total_sales")
            // ->where('status', 'processed')
            // ->where('paid', true)
            ->whereYear('created_at', Carbon::now()->year)
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total_sales', 'month');

        $sales_data = [];

        for ($month = 1; $month <= 12; $month++) {
            if (isset($monthly_sales[$month])) {
                $sales_data[] = $monthly_sales[$month];
            } else {
                $sales_data[] = 0;
            }
        }

        $locations_data = OrderDelivery::select('location', DB::raw('COUNT(*) as total_orders'))
        ->groupBy('location')
        ->orderBy('total_orders', 'desc')
        ->get();

        $locations_labels = $locations_data->pluck('location')->toArray();
        $locations_orders = $locations_data->pluck('total_orders')->toArray();

        $sales_data = [];

        for ($month = 1; $month <= 12; $month++) {
            $sales_data[] = $monthly_sales[$month] ?? 0;
        }


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

            'gross_sales' => $gross_sales,
            'net_sales' => $net_sales,
            'cost_of_sales' => $cost_of_sales,
            'gross_profit' => $gross_profit,

            'sales_data' => $sales_data,
            'locations_labels' => $locations_labels,
            'locations_orders' => $locations_orders,
        ]);
    }
}
