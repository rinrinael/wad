<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Mahasiswa;
use Illuminate\Support\Facades\Auth;

class MahasiswaController extends Controller
{
    public function index() {
        $mahasiswa = Mahasiswa::all();
        return view('mahasiswa.index', compact('mahasiswa') );
    }

    public function create() {
        return view('mahasiswa.create');

    }

    public function store(Request $request) {
        $request->validate([
            'nim'       => 'required|integer|unique:mahasiswa,nim',
            'jadwal_id' => 'required|exists:jadwal,id',
            'tanggal'   => 'required|date',
            'nama'      => 'required|string|max:255',
            'email'     => 'required|email|unique:mahasiswa,email',
            'password'  => 'required|string|min:6',
            
        ]);

            Mahasiswa::create([
            'nim'       => $request->nim,
            'jadwal_id' => $request->jadwal_id,
            'tanggal'   => $request->tanggal,
            'nama'      => $request->nama,
            'email'     => $request->email,
            'password'  => bcrypt($request->password), // hash password
        ]);
        return redirect()->route('mahasiswa.index')->with('success', 'Mahasiswa created successfully.');
    }

    public function edit($nim) {
        $mahasiswa = Mahasiswa::findOrFail($nim);
        return view('mahasiswa.edit', compact('mahasiswa'));
    }

    public function update(Request $request, $nim) {
        $mahasiswa = Mahasiswa::findOrFail($nim);

        $request->validate([
            'jadwal_id' => 'required|exists:jadwal,id',
            'tanggal'   => 'required|date',
            'nama'      => 'required|string|max:255',
            'email'     => 'required|email|unique:mahasiswa,email,' . $nim . ',nim',
            'password'  => 'nullable|string|min:6',
        ]);

        $data = $request->all();

        if ($request->filled('password')) {
            $data['password'] = bcrypt($request->password);
        } else {
            unset($data['password']);
        }

        $mahasiswa->update($data);

        return redirect()->route('mahasiswa.index')->with('success', 'Mahasiswa updated successfully.');
    }

    public function destroy($nim) {
        $mahasiswa = Mahasiswa::findOrFail($nim);
        $mahasiswa->delete();

        return redirect()->route('mahasiswa.index')->with('success', 'Mahasiswa deleted successfully.');
    }




}