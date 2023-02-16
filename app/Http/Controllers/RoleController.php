<?php

namespace App\Http\Controllers;

use App\Http\Requests\RoleStoreRequest;
use App\Http\Requests\RoleUpdateRequest;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index()
    {
        $roles = Role::orderBy('name', 'asc')->paginate(20);
        return view('role.index', [
            'roles' => $roles
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
     */
    public function create()
    {
        $role = new Role();
        $permissions = Permission::orderBy('name')->get();
        $options = [];
        return view('role.create', [
            'role' => $role,
            'options' => $options,
            'permissions' => $permissions
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param RoleStoreRequest $request
     *
     * @return RedirectResponse
     */
    public function store(RoleStoreRequest $request)
    {
        $validated = $request->safe()->merge([

        ])->toArray();
        //dd($validated);
        /** @var Role $role */
        $role = Role::create(['name' => $validated['name']]);
        foreach ($validated['permissions'] as $id) {
            $role->permissions()->attach($id);
        }
        return redirect()->route('role.index')->with('success', 'The role has been created');
    }

    /**
     * Display the specified resource.
     *
     * @param Role $role
     *
     * @return RedirectResponse
     */
    public function show(Role $role)
    {
        return redirect()->route('role.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Role $role
     *
     * @return Application|Factory|View
     */
    public function edit(Role $role)
    {
        $permissions = Permission::orderBy('name')->get();
        $options = [];
        return view('role.edit', [
            'role' => $role,
            'options' => $options,
            'permissions' => $permissions
        ]);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param RoleUpdateRequest $request
     * @param Role $role
     *
     * @return RedirectResponse
     */
    public function update(RoleUpdateRequest $request, Role $role)
    {
        $validated = $request->safe()->merge([

        ])->toArray();
        $role->name = $validated['name'];
        $role->save();
        $role->permissions()->detach();
        foreach ($validated['permissions'] as $id) {
            $role->permissions()->attach($id);
        }
        return redirect()->route('role.index')->with('success', 'The role has been saved');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Role  $role
     *
     * @return RedirectResponse
     */
    public function destroy(Role $role)
    {
        $role->delete();
        return redirect()->route('role.index')->with('success', 'The role has been deleted');
    }
}
