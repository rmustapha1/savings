<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\GroupMember;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Validator;

class GroupController extends Controller
{
    public function __construct()
    {
        date_default_timezone_set(get_option('timezone', 'Africa/Accra'));
    }

    public function index()
    {
        return view('backend.groups.list');
    }

    public function get_table_data()
    {
        $groups = Group::with(['groupMembers']) // Load related members
        ->select('groups.*') // Select all columns from groups
        ->withoutGlobalScopes(['status'])
        ->withCount('groupMembers') // Count total members in each group
        ->withSum('groupMembers', 'total_contributed') // Sum contributions
        ->withSum('groupMembers', 'amount_received') // Sum received amount
        ->addSelect([
            'balance_remaining' => GroupMember::selectRaw('COALESCE(SUM(total_contributed - amount_received), 0)')
                ->whereColumn('groups.id', 'group_members.group_id')
        ])
        ->withCount(['groupMembers as total_payouts' => function ($query) {
            $query->where('has_received_payout', 1); // Count members who received payouts
        }])
        ->orderBy('groups.id', 'desc');
    
        return Datatables::eloquent($groups)
            ->editColumn('status', function ($group) {
                return status($group->status);
            })
            ->addColumn('name', function ($group) {
                return $group['group_name'];
            })
            ->addColumn('monthly_contribution', function ($group) {
                return $group['monthly_contribution'];
            })

            ->addColumn('total_members', function ($group) {
                return $group->group_members_count; // Members count
            })
            ->addColumn('total_contributed', function ($group) {
                return decimalPlace($group->group_members_sum_total_contributed, 2); // Sum of contributions
            })
            ->addColumn('total_payouts', function ($group) {
                return $group->total_payouts; // Number of members who received payouts
            })
            ->addColumn('action', function ($group) {
                return '<div class="dropdown text-center">'
                    . '<button class="btn btn-primary btn-xs dropdown-toggle" type="button" data-toggle="dropdown">' . _lang('Action')
                    . '&nbsp;</button>'
                    . '<div class="dropdown-menu">'
                    . '<a class="dropdown-item ajax-modal" href="' . route('groups.edit', $group['id']) . '" data-title="' . _lang('Edit Group') . '"><i class="ti-pencil-alt"></i> ' . _lang('Edit') . '</a>'
                    . '<a class="dropdown-item ajax-modal" href="' . route('groups.show', $group['id']) . '" data-title="' . _lang('Group Details') . '"><i class="ti-eye"></i>  ' . _lang('View') . '</a>'
                    . '<a class="dropdown-item" href="' . route('group_members.index', $group['id']) . '" data-title="' . _lang('Group Members') . '"><i class="ti-user"></i>  ' . _lang('Members') . '</a>'
                    . '<form action="' . route('groups.destroy', $group['id']) . '" method="post">'
                    . csrf_field()
                    . '<input name="_method" type="hidden" value="DELETE">'
                    . '<button class="dropdown-item btn-remove" type="submit"><i class="ti-trash"></i> ' . _lang('Delete') . '</button>'
                    . '</form>'
                    . '</div>'
                    . '</div>';
            })
            ->setRowId(function ($group) {
                return "row_" . $group->id;
            })
            ->rawColumns(['status', 'action'])
            ->make(true);
    }
    
    

    public function create(Request $request)
    {
        if (!$request->ajax()) {
            return back();
        } else {
            return view('backend.groups.modal.create');
        }
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'group_name'            => 'required|unique:groups|max:100',
            'monthly_contribution'  => 'required|numeric|min:1',
            'status'                => 'required|in:1,0',
        ]);

        if ($validator->fails()) {
            return response()->json(['result' => 'error', 'message' => $validator->errors()->all()]);
        }

        $group = new Group();
        $group->group_name = $request->input('group_name');
        $group->monthly_contribution = $request->input('monthly_contribution');
        $group->total_members = 0;
        $group->status = $request->input('status');
        $group->created_user_id    = auth()->id();
        $group->save();

        return response()->json(['result' => 'success', 'message' => _lang('Group Saved Successfully'), 'data' => $group]);
    }
/**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $group = Group::withoutGlobalScopes(['status'])->find($id);
        // add parameters
        $amount_received = DB::table('group_members')->where('group_id', $id)->sum('amount_received');
        $total_contributed = DB::table('group_members')->where('group_id', $id)->sum('total_contributed');
        $balance_remaining = $total_contributed - $amount_received;
        $total_payouts = DB::table('group_members')->where('group_id', $id)->where('has_received_payout', 1)->count();

        if (!$request->ajax()) {
            return view('backend.groups.view', compact('group', 'id', 'amount_received', 'total_contributed', 'balance_remaining', 'total_payouts'));
        } else {
            return view('backend.groups.modal.view', compact('group', 'id', 'amount_received', 'total_contributed', 'balance_remaining', 'total_payouts'));
        }
    }

    

    public function edit(Request $request, $id)
    {
        $group = Group::find($id);
        return view('backend.groups.modal.edit', compact('group', 'id'));
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'group_name'            => ['required', Rule::unique('groups')->ignore($id)],
            'monthly_contribution'  => 'required|numeric|min:1',
            'status'                => 'required|in:1,2',
        ]);

        if ($validator->fails()) {
            return response()->json(['result' => 'error', 'message' => $validator->errors()->all()]);
        }

        $group = Group::find($id);
        $group->group_name = $request->input('group_name');
        $group->monthly_contribution = $request->input('monthly_contribution');
        $group->status = $request->input('status');
        $group->save();

        return response()->json(['result' => 'success', 'message' => _lang('Updated Successfully'), 'data' => $group]);
    }

    public function destroy($id)
    {
        $group = Group::find($id);
        $group->delete();
        return redirect()->route('groups.index')->with('success', _lang('Deleted Successfully'));
    }
}
