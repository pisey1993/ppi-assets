<?php

namespace App\Http\Controllers;

use App\Models\Employees;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = Employees::all();
        return inertia('Employees/Index', ['employees' => $employees]);
    }

    public function create()
    {
        return inertia('Employees/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'position' => 'required|string|max:255',
            'department_id' => 'nullable|integer',
            'view_check_request' => 'nullable|boolean',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        Employees::create($validated);

        return redirect()->route('employees.index');
    }

    public function edit(Employees $employee)
    {
        return inertia('Employees/Edit', ['employee' => $employee]);
    }

    public function update(Request $request, Employees $employee)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $employee->id,
            'password' => 'nullable|string|min:6',
            'position' => 'required|string|max:255',
            'department_id' => 'nullable|integer',
            'view_check_request' => 'nullable|boolean',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $employee->update($validated);

        return redirect()->route('employees.index');
    }

    public function destroy(Employees $employee)
    {
        $employee->delete();
        return redirect()->route('employees.index');
    }
}
