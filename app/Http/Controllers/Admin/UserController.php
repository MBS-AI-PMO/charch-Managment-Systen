<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\UserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $req)
    {
        $q = User::with('roles');
        if ($s = $req->query('search')) {
            $q->where(function ($qq) use ($s) {
                $qq->where('name', 'like', "%$s%")->orWhere('email', 'like', "%$s%");
            });
        }
        if ($role = $req->query('role')) {
            $q->whereHas('roles', fn ($qq) => $qq->where('name', $role));
        }
        $users = $q->orderBy('name')->paginate(25)->withQueryString();
        $roles = Role::where('guard_name', 'admin')->orderBy('name')->get();
        return view('admin.users.index', compact('users', 'roles'));
    }

    public function create()
    {
        $user = new User();
        $roles = Role::where('guard_name', 'admin')->orderBy('name')->get();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function store(UserRequest $req)
    {
        $data = $req->validated();
        $user = new User();
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->is_admin = $data['is_admin'] ?? true;
        $user->password = Hash::make($data['password']);
        $user->save();

        if (!empty($data['roles'])) {
            $user->syncRoles($data['roles']);
        }

        return redirect()->route('admin.users.index')->with('success', 'User created.');
    }

    public function edit(User $user)
    {
        $roles = Role::where('guard_name', 'admin')->orderBy('name')->get();
        $user->load('roles');
        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(UserRequest $req, User $user)
    {
        $this->guardImmutable($req, $user, 'edit');

        $data = $req->validated();
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->is_admin = $data['is_admin'] ?? $user->is_admin;
        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }
        $user->save();

        // Don't allow stripping Site Admin from user #1 to avoid lockout.
        if ($user->id === 1) {
            $roles = array_unique(array_merge($data['roles'] ?? [], ['Site Admin']));
        } else {
            $roles = $data['roles'] ?? [];
        }
        $user->syncRoles($roles);

        return redirect()->route('admin.users.edit', $user)->with('success', 'User updated.');
    }

    public function show(User $user)
    {
        return redirect()->route('admin.users.edit', $user);
    }

    public function destroy(Request $req, User $user)
    {
        $this->guardImmutable($req, $user, 'delete');
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'User deleted.');
    }

    protected function guardImmutable(Request $req, User $user, string $action): void
    {
        $actor = $req->user('admin');
        if ($action === 'delete') {
            if ($actor && $actor->id === $user->id) {
                throw ValidationException::withMessages(['user' => 'You cannot delete your own account.']);
            }
            if ($user->id === 1) {
                throw ValidationException::withMessages(['user' => 'The bootstrap Site Admin cannot be deleted.']);
            }
        }
    }
}
