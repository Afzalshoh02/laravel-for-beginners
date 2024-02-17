<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateAvatarRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use OpenAI\Laravel\Facades\OpenAI;

class AvatarController extends Controller
{
    public function update(UpdateAvatarRequest $request)
    {
        $path = $request->file('avatar')->store('avatars', 'public');
        if ($oldAvatar = $request->user()->avatar) {
            Storage::disk('public')->delete($oldAvatar);
        }
        auth()->user()->update(['avatar' => $path]);
        return redirect(route('profile.edit'))->with('message', 'Avatar is update');
    }

    public function generate(Request $request)
    {
        $result = OpenAI::images()->create([
            "prompt" => "create image coder hackers",
            "n" => 1,
            "size" => "256x256"
        ]);
        if ($oldAvatar = $request->user()->avatar) {
            Storage::disk('public')->delete($oldAvatar);
        }
        $content = file_get_contents($result->data[0]->url);
        $file_name = Str::random(25);
        Storage::disk('public')->put("avatars/$file_name.jpg", $content);
        auth()->user()->update(['avatar' => "avatars/$file_name.jpg"]);
        return redirect(route('profile.edit'))->with('message', 'Avatar is update');
    }
}
