<?php

namespace App\Http\Controllers\Bta;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\bta_user;

class UserController extends Controller
{
    public function index()
    {
        $users = bta_user::all();
        return view('bta.users.index', compact('users'));
    }

    public function create()
    {
        return view('bta.users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|string|max:50|unique:bta_users,username',
            'email' => 'required|email|unique:bta_users,email',
            'password' => 'required|string|confirmed|min:6',
            'type' => 'required|in:master,admin,analyst,credam',
        ]);

        bta_user::create([
            'username' => $validated['username'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'type' => $validated['type'],
        ]);

        return redirect()->route('bta.users.index')->with('success', 'User berhasil dibuat!');
    }

    public function show($id)
    {
        $user = bta_user::findOrFail($id); // Ambil user berdasarkan ID atau gagal
        return view('bta.users.show', compact('user')); // Kirim data ke view
    }

    public function edit($id)
    {
        $user = bta_user::findOrFail($id);
        return view('bta.users.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $user = bta_user::findOrFail($id);

        $validated = $request->validate([
            'username' => 'required|string|max:50|unique:bta_users,username,' . $id,
            'email' => 'required|email|unique:bta_users,email,' . $id,
            'password' => 'nullable|string|confirmed|min:6',
            'type' => 'required|in:master,admin,analyst,credam',
        ]);

        $data = [
            'username' => $validated['username'],
            'email' => $validated['email'],
            'type' => $validated['type'],
        ];

        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $user->update($data);

        return redirect()->route('bta.users.index')->with('success', 'User berhasil diperbarui!');
    }

    public function destroy($id)
    {
        bta_user::findOrFail($id)->delete();

        return redirect()->route('bta.users.index')->with('success', 'User berhasil dihapus!');
    }

    public function bulkDelete(Request $request)
    {
        if ($request->ids) {
            bta_user::whereIn('id', $request->ids)->delete();
            return redirect()->route('bta.users.index')->with('success', 'User berhasil dihapus massal!');
        }

        return redirect()->route('bta.users.index')->with('error', 'Tidak ada data dipilih!');
    }

    public function profile()
    {
        $userId = auth('bta')->id();

        $user = bta_user::findOrFail($userId);

        return view('bta.users.profile', compact('user'));
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required'],
            'new_password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'current_password.required' => 'Password lama wajib diisi.',
            'new_password.required' => 'Password baru wajib diisi.',
            'new_password.min' => 'Password baru minimal 8 karakter.',
            'new_password.confirmed' => 'Konfirmasi password baru tidak cocok.',
        ]);

        $userId = auth('bta')->id();
        $user = bta_user::findOrFail($userId);

        // cek password lama
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->with('error', 'Password lama tidak sesuai.');
        }

        // optional: cegah password baru sama dengan password lama
        if (Hash::check($request->new_password, $user->password)) {
            return back()->with('error', 'Password baru tidak boleh sama dengan password lama.');
        }

        // update password baru
        $user->password = Hash::make($request->new_password);
        $user->save();

        return back()->with('success', 'Password berhasil diperbarui.');
    }
}
