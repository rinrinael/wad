<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use App\Models\Mahasiswa;
use App\Models\Konselor;
use App\Models\User;

use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $feedback = Feedback::all();
        return response()->json($feedback, 200);
    }

    public function edit($id)
    {
        $feedback = Feedback::findOrFail($id);
        $mahasiswa = Mahasiswa::all();
        $konselor = Konselor::all();
        return view('feedback.edit', compact('feedback', 'mahasiswa', 'konselor'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request){
        $request->validate([
            'komentar' => 'required|string',
            'rating' => 'required|numeric|min:1|max:5',
            'nim' => 'required|exists:mahasiswa,nim',
            'konselor_id' => 'required|exists:konselor,id'
        ]);
        
        User::create($request->all());
        return redirect()->route('users.index')->with('success', 'Feedback created successfully.');

    }

    /**
     * Display the specified resource.
     */
    public function create(){
        return view('feedback.create', compact('mahasiswa', 'konselor'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);
        $request->validate([
            // isi disini
            'komentar' => 'required',
            'rating' => 'required',
            'nim' => 'required',
            'konselor_id' => 'required'
        ]);

        $user->update($request->all());
        return redirect()->route('users.index')->with('success', 'Feedback updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $feedback = Feedback::findOrFail($id);
        $feedback->delete();
        return redirect()->route('feedback.index')->with('success', 'Feedback berhasil dihapus.');
    }
}
