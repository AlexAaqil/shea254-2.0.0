<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function create()
    {
        return view('pages.users.create');
    }

    public function store(UserRequest $request)
    {
        $validated_data = $request->validated();

        $data['password'] = Hash::make($validated_data['password']);

        User::create($validated_data);

        session()->flash('notify', ['message' => 'user created successfully', 'type' => 'success']);

        return redirect()->route('users.index');
    }

    public function edit(User $user)
    {
        return view('pages.users.edit', compact('user'));
    }

    public function update(UserRequest $request, User $user)
    {
        $validated_data = $request->validated();

        if(!empty($validated_data['password'])) {
            $validated_data['password'] = Hash::make($validated_data['password']);
        } else {
            unset($validated_data['password']);
        }

        $user->update($validated_data);

        session()->flash('notify', ['message' => 'user updated successfully', 'type' => 'success']);

        return redirect()->route('users.index');
    }
}
