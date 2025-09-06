<?php
use App\Models\EssayReview;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function stats()
    {
        return response()->json([
            'users' => Users::count(),
            'students' => Users::where('role', 'Student')->count(),
            'teachers' => Users::where('role', 'Teacher')->count(),
            'active' => Users::where('is_active', 1)->count(),
            'chart' => [120, 200, 300, 500, 700, 850, 900], // Replace with real data if needed
        ]);
    }
    public function store(Request $request)
    {

    }
}
