<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    public function index(Request $request): Response
    {
        $users = User::query()
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString()
            ->through(fn (User $user) => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'created_at' => $user->created_at?->translatedFormat('Y-m-d H:i'),
            ]);

        return Inertia::render('Admin/Users/Index', [
            'users' => $users,
            'translations' => [
                'title' => __('users.title'),
                'create' => __('users.create'),
                'empty' => __('users.empty'),
                'fields' => __('users.fields'),
                'actions' => __('users.actions'),
                'roles' => __('users.roles'),
                'confirm_delete' => __('users.confirm_delete'),
                'dashboardTitle' => __('dashboard.title'),
            ],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Users/Create', [
            'translations' => [
                'title' => __('users.create'),
                'save' => __('users.actions.save'),
                'cancel' => __('users.actions.cancel'),
                'fields' => __('users.fields'),
                'roles' => __('users.roles'),
                'listTitle' => __('users.title'),
                'dashboardTitle' => __('dashboard.title'),
            ],
        ]);
    }

    public function store(UserRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);

        User::create($data);

        return redirect()
            ->route('admin.users.index')
            ->with('success', __('users.created'));
    }

    public function edit(User $user): Response
    {
        return Inertia::render('Admin/Users/Edit', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ],
            'translations' => [
                'title' => __('users.edit'),
                'save' => __('users.actions.save'),
                'cancel' => __('users.actions.cancel'),
                'fields' => __('users.fields'),
                'roles' => __('users.roles'),
                'listTitle' => __('users.title'),
                'password_hint' => __('users.password_hint'),
                'dashboardTitle' => __('dashboard.title'),
            ],
        ]);
    }

    public function update(UserRequest $request, User $user): RedirectResponse
    {
        $data = $request->validated();

        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);

        return redirect()
            ->route('admin.users.index')
            ->with('success', __('users.updated'));
    }

    public function destroy(User $user): RedirectResponse
    {
        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('success', __('users.deleted'));
    }
}
