<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessCsvUpload;
use App\Models\Upload;
use Illuminate\Http\Request;

class UploadController extends Controller
{
    public function index()
    {
        $uploads = Upload::orderBy('created_at','desc')->get();
        return view('uploads.index', compact('uploads'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt',
        ]);

        $file = $request->file('file');
        $fileName = time().'_'.$file->getClientOriginalName();
        $file->storeAs('uploads', $fileName);

        $upload = Upload::create([
            'file_name' => $fileName,
            'status' => 'pending'
        ]);

        // dispatch job
        ProcessCsvUpload::dispatch($upload);

        return redirect()->back()->with('success', 'File uploaded and processing in background!');
    }
}
