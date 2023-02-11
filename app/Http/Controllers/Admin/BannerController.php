<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;

class BannerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.banners.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
           'image'  => 'required|mimes:jpg,jpeg,png,svg',
           'priority'  => 'required|integer',
           'type'  => 'required',
        ]);

        $imageFileName = generateFileName($request->image->getClientOriginalName());
        $request->image->move(public_path(env('BANNER_IMAGES_UPLOAD_PATH')), $imageFileName);

        Banner::create([
           'image' => $imageFileName,
           'title' => $request->title,
           'text' => $request->text,
           'priority' => $request->priority,
           'is_active' => $request->is_active,
           'type' => $request->type,
           'button_text' => $request->button_text,
           'button_link' => $request->button_link,
           'button_icon' => $request->button_icon,
        ]);
        alert()->success( 'با تشکر', 'بنر مورد نظر ایجاد شد.');
        return redirect()->route('admin.banners.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
