<?php

namespace App\Http\Controllers;

use App\Models\WebMedia;
use Illuminate\Http\Request;
use Inertia\Inertia;

class WebMediaController extends Controller
{
    public function index()
    {
        $web_media = WebMedia::all()->groupBy('section');

        return Inertia::render('WebMedia', [
            'web_media' => $web_media
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'content' => ['required'],
            'content_section' => ['required']
        ]);

        $web_section = WebMedia::where('section', $request->content_section)->first();

        WebMedia::create([
            'page' => $web_section->page,
            'section' => $request->content_section,
            'link' => $request->content,
            'type' => $web_section->type
        ]);

        return to_route('web-media.index');
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => ['required'],
            'content' => ['required']
        ]);

        $web_media = WebMedia::find($request->id);

        $web_media->update([
            'link' => $request->content
        ]);

        return to_route('web-media.index');
    }
}
