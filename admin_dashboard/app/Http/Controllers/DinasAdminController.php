<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\ChatHistory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class DinasAdminController extends Controller
{
    /**
     * Dashboard Dinas Admin.
     */
    public function dashboard(Request $request)
    {
        $user = $request->user();
        $dinas = $user->dinas;

        $totalChats = ChatHistory::where('dinas', $dinas)->count();
        $monthlyChats = ChatHistory::where('dinas', $dinas)->whereMonth('created_at', now()->month)->count();
        $staffCount = User::where('dinas', $dinas)->where('role', 'staff')->count();

        $recentChats = ChatHistory::where('dinas', $dinas)
            ->latest()
            ->take(8)
            ->get();

        return view('admin.dashboard', compact('user', 'dinas', 'totalChats', 'monthlyChats', 'staffCount', 'recentChats'));
    }

    /**
     * Manajemen Staff dalam dinas terkait saja.
     */
    public function staff(Request $request)
    {
        $user = $request->user();
        $dinas = $user->dinas;

        $staffList = User::where('dinas', $dinas)
            ->where('role', 'staff')
            ->latest()
            ->paginate(10);

        return view('admin.staff', compact('staffList', 'dinas'));
    }

    /**
     * Tambah staff baru untuk dinas bersangkutan.
     */
    public function storeStaff(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', Password::defaults()],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'staff',
            'dinas' => $user->dinas,
        ]);

        return back()->with('status', 'Staff berhasil didaftarkan untuk ' . ucfirst($user->dinas));
    }

    /**
     * Riwayat chat khusus dinas bersangkutan.
     */
    public function chatHistory(Request $request)
    {
        $user = $request->user();
        $dinas = $user->dinas;

        $histories = ChatHistory::where('dinas', $dinas)
            ->latest()
            ->paginate(15);

        return view('admin.chat-history', compact('histories', 'dinas'));
    }

    /**
     * Upload dokumen pengetahuan ke KB dinas.
     */
    public function uploadDocument(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'document' => 'required|file|mimes:pdf,txt|max:10240',
        ]);

        $user = $request->user();
        $file = $request->file('document');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $file->storeAs('knowledge_base/' . $user->dinas, $fileName, 'local');

        return back()->with('status', "Dokumen '{$request->title}' berhasil diupload ke Knowledge Base {$user->dinas}.");
    }
}

