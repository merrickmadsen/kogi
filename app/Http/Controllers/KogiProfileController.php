<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class KogiProfileController extends Controller
{
    public function show()
    {
        $profile = auth()->user()->kogiProfile;
        return response()->json($profile);
    }


    public function update(Request $request)
    {
        $profile = auth()->user()->kogiProfile()->updateOrCreate(
            ['user_id' => auth()->id()],
            $request->all()
        );
        return response()->json($profile);
    }
}
