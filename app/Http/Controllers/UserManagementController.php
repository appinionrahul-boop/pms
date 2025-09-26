<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!Auth::check() || Auth::user()->is_super != 1) {
                abort(403, 'Unauthorized');
            }
            return $next($request);
        });
    }

    public function index()
    {
        $users = User::orderBy('id','ASC')->get();
        return view('users.management', compact('users'));
    }

    public function create()
    {
        return view('users.form'); // create a simple form view (name, email, password, phone, location, is_super, about_me)
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'      => ['required','string','max:255'],
            'email'     => ['required','email','max:255','unique:users,email'],
            'password'  => ['required','string','min:6','confirmed'],
            'phone'     => ['nullable','numeric'],
            'location'  => ['nullable','string','max:255'],
            'is_super'  => ['nullable', Rule::in(['0','1',0,1])],
            'about_me'  => ['nullable','string'],
        ]);

        $data['password'] = Hash::make($data['password']);
        User::create($data);

        return redirect()->route('users.management')->with('success','User created.');
    }

    public function edit(User $user)
    {
        return view('users.form', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name'      => ['required','string','max:255'],
            'email'     => ['required','email','max:255', Rule::unique('users','email')->ignore($user->id)],
            'phone'     => ['nullable','numeric'],
            'location'  => ['nullable','string','max:255'],
            'is_super'  => ['nullable', Rule::in(['0','1',0,1])],
            'about_me'  => ['nullable','string'],
        ]);

        $user->update($data);

        return redirect()->route('users.management')->with('success','User updated.');
    }

    public function updatePassword(Request $request, User $user)
    {
        $validated = $request->validate([
            'password' => ['required','string','min:6','confirmed'],
        ]);

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('users.management')->with('success','Password updated.');
    }
}
