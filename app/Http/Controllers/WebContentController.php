<?php

namespace App\Http\Controllers;

use App\Models\WebContent;
use Illuminate\Http\Request;
use Inertia\Inertia;

class WebContentController extends Controller
{
    public function index()
    {
        $web_contents = WebContent::all();

        return Inertia::render('WebContent', [
            'web_contents' => $web_contents
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => ['required'],
            'content' => ['required']
        ]);

        $web_content = WebContent::find($request->id);

        $web_content->update([
            'content' => $request->content
        ]);

        return to_route('web-content.index');
    }
}
