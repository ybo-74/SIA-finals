<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class ProfessorController extends Controller
{
    /**
     * Display a listing of professors.
     */
    public function index()
    {
        $professors = User::role('professor')->orderBy('last_name')->orderBy('first_name')->get();
        return view('professors.index', compact('professors'));
    }

    /**
     * Show the form for creating a new professor.
     */
    public function create()
    {
        return view('professors.create');
    }

    /**
     * Store a newly created professor.
     */
    public function store(Request $request)
    {
        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'middle_name' => $request->middle_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->assignRole('professor');

        return redirect()->route('professors.index')
            ->with('success', 'Professor registered successfully!');
    }

    /**
     * Show the form for editing the professor.
     */
    public function edit(User $professor)
    {
        // Ensure user is a professor
        if (!$professor->hasRole('professor')) {
            abort(404);
        }

        return view('professors.edit', compact('professor'));
    }

    /**
     * Update the professor.
     */
    public function update(Request $request, User $professor)
    {
        // Ensure user is a professor
        if (!$professor->hasRole('professor')) {
            abort(404);
        }

        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $professor->id],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
        ]);

        // Update name fields
        $professor->first_name = $request->first_name;
        $professor->last_name = $request->last_name;
        $professor->middle_name = $request->middle_name;
        $professor->email = $request->email;

        // Update password only if provided
        if ($request->filled('password')) {
            $professor->password = Hash::make($request->password);
        }

        $professor->save();

        return redirect()->route('professors.index')
            ->with('success', 'Professor updated successfully!');
    }

    /**
     * Remove the professor.
     */
    public function destroy(User $professor)
    {
        // Ensure user is a professor
        if (!$professor->hasRole('professor')) {
            abort(404);
        }

        DB::transaction(fn () => $professor->delete());

        return redirect()->route('professors.index')
            ->with('success', 'Professor deleted successfully!');
    }
}
