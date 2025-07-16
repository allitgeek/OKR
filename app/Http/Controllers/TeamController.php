<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    public function index()
    {
        $teams = Team::with('leader')->get();
        return view('teams.index', compact('teams'));
    }

    public function create()
    {
        $users = User::all();
        $teams = Team::all();
        return view('teams.create', compact('users', 'teams'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'leader_id' => 'required|exists:users,id',
            'parent_team_id' => 'nullable|exists:teams,id',
        ]);

        Team::create($validated);

        return redirect()->route('teams.index')->with('success', 'Team created successfully.');
    }

    public function show(Team $team)
    {
        $team->load('leader', 'parentTeam', 'members');
        return view('teams.show', compact('team'));
    }

    public function edit(Team $team)
    {
        $users = User::all();
        $teams = Team::where('id', '!=', $team->id)->get();
        return view('teams.edit', compact('team', 'users', 'teams'));
    }

    public function update(Request $request, Team $team)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'leader_id' => 'required|exists:users,id',
            'parent_team_id' => 'nullable|exists:teams,id',
        ]);

        $team->update($validated);

        return redirect()->route('teams.index')->with('success', 'Team updated successfully.');
    }

    public function destroy(Team $team)
    {
        $team->delete();
        return redirect()->route('teams.index')->with('success', 'Team deleted successfully.');
    }
}
