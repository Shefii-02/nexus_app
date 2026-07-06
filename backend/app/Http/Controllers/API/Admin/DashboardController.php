<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\OtpVerification;
use App\Services\Dashboard\DashboardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(protected DashboardService $dashboardService) {}

    /**
     * Single combined payload for the admin dashboard.
     * GET /api/dashboard-status
     */
    public function status(): JsonResponse
    {
        // return response()->json([
        //     'success' => true,
        //     'data' => [
        //         'stats' => [
        //             'total_courses' => [
        //                 'value' => 128,
        //                 'growth' => '+12.5%',
        //                 'trend' => [8, 10, 9, 12, 14, 13, 16],
        //             ],
        //             'total_students' => [
        //                 'value' => 964,
        //                 'growth' => '+8.2%',
        //                 'trend' => [120, 128, 130, 135, 142, 150, 158],
        //             ],
        //             'enrollments' => [
        //                 'value' => 2340,
        //                 'growth' => '+18.7%',
        //                 'trend' => [280, 310, 295, 340, 360, 355, 400],
        //             ],
        //             'revenue' => [
        //                 'value' => 845000,
        //                 'formatted' => '₹8,45,000',
        //                 'growth' => '+24.6%',
        //                 'trend' => [90000, 105000, 98000, 120000, 132000, 128000, 145000],
        //             ],
        //         ],

        //         'enrollments_chart' => [
        //             'range' => '30d',
        //             'labels' => ['Jun 06', 'Jun 11', 'Jun 16', 'Jun 21', 'Jun 26', 'Jul 01', 'Jul 05'],
        //             'values' => [42, 58, 51, 67, 73, 65, 80],
        //         ],

        //         'revenue_chart' => [
        //             'range' => '30d',
        //             'labels' => ['Web Development', 'Data Science', 'UI/UX Design', 'Marketing', 'Mobile Dev'],
        //             'values' => [320000, 210000, 145000, 98000, 72000],
        //         ],

        //         'top_courses' => [
        //             ['id' => 1, 'name' => 'React Bootcamp', 'price' => '₹1,999', 'sales_count' => 342],
        //             ['id' => 2, 'name' => 'Python for Data Science', 'price' => '₹1,499', 'sales_count' => 298],
        //             ['id' => 3, 'name' => 'UI/UX Design Masterclass', 'price' => '₹1,299', 'sales_count' => 251],
        //             ['id' => 4, 'name' => 'Flutter App Development', 'price' => '₹1,799', 'sales_count' => 210],
        //             ['id' => 5, 'name' => 'Digital Marketing 101', 'price' => '₹999', 'sales_count' => 187],
        //         ],

        //         'notifications' => [
        //             ['id' => 1, 'message' => 'New course "Advanced Laravel" added', 'type' => 'course', 'created_at' => '10 minutes ago'],
        //             ['id' => 2, 'message' => '12 new enrollments in React Bootcamp', 'type' => 'enrollment', 'created_at' => '45 minutes ago'],
        //             ['id' => 3, 'message' => 'Payout of ₹42,500 completed', 'type' => 'payment', 'created_at' => '2 hours ago'],
        //             ['id' => 4, 'message' => 'New 5-star review on UI/UX Design', 'type' => 'review', 'created_at' => '3 hours ago'],
        //             ['id' => 5, 'message' => '8 new enrollments in Python for Data Science', 'type' => 'enrollment', 'created_at' => '5 hours ago'],
        //             ['id' => 6, 'message' => 'New course "Node.js Fundamentals" added', 'type' => 'course', 'created_at' => 'Yesterday'],
        //         ],
        //     ],
        // ]);

        $data = $this->dashboardService->getDashboardSummary();

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }


    public function otpUsages(Request $request)
    {
        $request->validate([
            'phone' => 'nullable|string',
        ]);

        $otpList = OtpVerification::where('phone', 'like', '%' . $request->phone . '%')
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $otpList,
        ]);
    }
}
