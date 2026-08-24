<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\HeroSlideRequest;
use App\Models\HeroSlide;

class HeroSlideController extends Controller
{
    public function index()
    {
        $slides = HeroSlide::orderBy('position')->orderBy('id')->paginate(10);

        return view('admin.hero-slides.index', compact('slides'));
    }

    public function create()
    {
        return view('admin.hero-slides.edit', ['slide' => new HeroSlide(['position' => HeroSlide::max('position') + 1])]);
    }

    public function store(HeroSlideRequest $req)
    {
        $data = $req->validated();
        if ($req->hasFile('image_file')) {
            $data['image_path'] = $req->file('image_file')->store('uploads/hero-slides', 'public');
        }
        unset($data['image_file']);

        $slide = HeroSlide::create($data);

        return redirect()->route('admin.hero-slides.edit', $slide)->with('success', 'Hero slide created.');
    }

    public function edit(HeroSlide $hero_slide)
    {
        return view('admin.hero-slides.edit', ['slide' => $hero_slide]);
    }

    public function update(HeroSlideRequest $req, HeroSlide $hero_slide)
    {
        $data = $req->validated();
        if ($req->hasFile('image_file')) {
            $data['image_path'] = $req->file('image_file')->store('uploads/hero-slides', 'public');
        }
        unset($data['image_file']);

        $hero_slide->update($data);

        return redirect()->route('admin.hero-slides.edit', $hero_slide)->with('success', 'Hero slide updated.');
    }

    public function show(HeroSlide $hero_slide)
    {
        return redirect()->route('admin.hero-slides.edit', $hero_slide);
    }

    public function destroy(HeroSlide $hero_slide)
    {
        $hero_slide->delete();

        return redirect()->route('admin.hero-slides.index')->with('success', 'Hero slide deleted.');
    }
}
