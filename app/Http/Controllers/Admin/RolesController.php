<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Permission;
use App\Services\RoleService;
use App\Traits\LogsActivity;
use Auth;
use Session;
use Validator;

class RolesController extends Controller
{
    use LogsActivity;

    protected $roleService;

    public function __construct(RoleService $roleService)
    {
        $this->roleService = $roleService;
    }

    /**
     * Show list of roles
     */
    public function index()
    {
        $data = [];
        $data['pageTitle'] = "Roles";
        $data['current_module_name'] = "Roles";
        $data['module_name'] = "Roles";
        $data['module_url'] = route('admin.roles.index');
        $data['roles'] = $this->roleService->getAllRoles();
        $data['currentModule'] = '';

        return view('Admin/Roles/index', $data);
    }

    /**
     * Show create form
     */
    public function create()
    {
        $data = [];
        $data['pageTitle'] = "Add Role";
        $data['current_module_name'] = "Add Role";
        $data['module_name'] = "Add Role";
        $data['module_url'] = route('admin.roles.index');
        $data['permissions'] = $this->roleService->getPermissionsGrouped();

        return view('Admin/Roles/create', $data);
    }

    /**
     * Store role
     */
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:255|unique:roles,name',
            'slug' => 'nullable|string|max:255|unique:roles,slug',
            'description' => 'nullable|string',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $role = $this->roleService->createRole($request->all());
            $this->logCreated($role, 'Role created');
            Session::flash('success', 'Role created successfully.');
            return redirect(route('admin.roles.index'));
        } catch (\Exception $e) {
            Session::flash('error', $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    /**
     * Show edit form
     */
    public function edit($id)
    {
        $id = base64_decode($id);
        $role = Role::findOrFail($id);

        // Prevent editing super-admin role
        if ($role->slug === 'super-admin' && !Auth::guard('admin')->user()->isSuperAdmin()) {
            Session::flash('error', 'You do not have permission to edit this role.');
            return redirect(route('admin.roles.index'));
        }

        $data = [];
        $data['pageTitle'] = "Edit Role";
        $data['current_module_name'] = "Edit Role";
        $data['module_name'] = "Edit Role";
        $data['module_url'] = route('admin.roles.index');
        $data['role'] = $role;
        $data['permissions'] = $this->roleService->getPermissionsGrouped();
        $data['rolePermissions'] = $role->rolePermissions->pluck('id')->toArray();

        return view('Admin/Roles/edit', $data);
    }

    /**
     * Update role
     */
    public function update(Request $request, $id)
    {
        $id = base64_decode($id);
        $role = Role::findOrFail($id);

        // Prevent editing super-admin role
        if ($role->slug === 'super-admin' && !Auth::guard('admin')->user()->isSuperAdmin()) {
            Session::flash('error', 'You do not have permission to edit this role.');
            return redirect(route('admin.roles.index'));
        }

        $rules = [
            'name' => 'required|string|max:255|unique:roles,name,' . $id,
            'slug' => 'nullable|string|max:255|unique:roles,slug,' . $id,
            'description' => 'nullable|string',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $oldData = $role->toArray();
            $role = $this->roleService->updateRole($id, $request->all());
            $this->logUpdated($role, $oldData, $role->toArray(), 'Role updated');
            Session::flash('success', 'Role updated successfully.');
            return redirect(route('admin.roles.index'));
        } catch (\Exception $e) {
            Session::flash('error', $e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    /**
     * Delete role
     */
    public function delete($id)
    {
        $id = base64_decode($id);
        $role = Role::findOrFail($id);

        try {
            $this->roleService->deleteRole($id);
            $this->logDeleted($role, 'Role deleted');
            Session::flash('success', 'Role deleted successfully.');
        } catch (\Exception $e) {
            Session::flash('error', $e->getMessage());
        }

        return redirect()->back();
    }
}

