<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KogiProfile;

class KogiProfileController extends Controller
{
    public function show()
    {
        $profile = auth()->user()->kogiProfile;

        if (!$profile) {
            return response()->json(['message' => 'No profile found'], 404);
        }

        return response()->json($profile);
    }

    public function update(Request $request)
    {
        $profile = KogiProfile::updateOrCreate(
            ['user_id' => auth()->id()],
            $request->only([
                'dietary_restrictions',
                'food_allergies',
                'skill_level',
                'pantry_staples',
                'kitchen_equipment'
            ])
        );

        if ($request->expectsJson()) {
            return response()->json($profile);
        }

        return redirect()->route('profile.edit')->with('status', 'kogi-profile-updated');
    }
}
