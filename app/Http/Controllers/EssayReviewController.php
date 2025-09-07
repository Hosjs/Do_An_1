<?php
use App\Models\EssayReview;
use Illuminate\Http\Request;

class EssayReviewController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'test_detail_id' => 'required|exists:test_details,id',
            'reviewed_by' => 'required|exists:users,id',
            'score' => 'required|numeric',
            'work_time' => 'nullable|date_format:H:i:s'
        ]);

        EssayReview::create([
            'test_detail_id' => $request->test_detail_id,
            'reviewed_by'   => $request->reviewed_by,
            'score'         => $request->score,
            'work_time'     => $request->work_time,
            'reviewed_at'   => now()
        ]);
        
        return response()->json(['message' => 'Saved successfully', 'data' => $review]);
    }
}
