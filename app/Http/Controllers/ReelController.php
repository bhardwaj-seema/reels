<?php

namespace App\Http\Controllers;

use App\Models\Reel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ReelController extends Controller
{

    public function index(){
        $reels =Cache::remember('reels',60, function () {
            return Reel::with('user')->latest()->paginate(10);
        });
        return response()->json($reels);
    }
    public function store(Request $request){

        $request->validate([

            'title' => 'required|string|max:255',
            'video'=> 'required|mimes:mp4,mov,avi|max:20000'
        ]);

        $videopath= $request->file('video')->store('videos','public');

        $reel = Reel::create([
            'user_id'=> auth()->id(),
            'title' => $request->title,
            'video_path' => $videopath
        ]);

        Cache::forget('reels');
        return response()->json([

            'message' => 'Reel Uploaded successfully',
            'reel'=> $reel
        ]);
    }

    public function show(Reel $reel){
        $reeldata = Cache::remember('reel_{$reel->id}',60,function() use ($reel){
            return $reel;
        });
        return response()->json($reeldata);
    }
}
