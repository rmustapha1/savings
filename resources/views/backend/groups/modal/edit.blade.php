<form method="post" class="ajax-screen-submit" autocomplete="off" action="{{ route('groups.update', $id) }}" enctype="multipart/form-data">
	{{ csrf_field()}}
	<div class="row px-2">
        <div class="col-md-12">
            <div class="form-group">
                <label class="control-label">{{ _lang('Group Name') }}</label>                        
                <input type="text" class="form-control" name="group_name" value="{{ $group->group_name }}" required>
            </div>
        </div>

        <div class="col-md-12">
            <div class="form-group">
                <label class="control-label">{{ _lang('Monthly Contribution') }}</label>                        
                <input type="number" class="form-control float-field" name="monthly_contribution" value="{{ $group->monthly_contribution }}" required>
            </div>
        </div>

        <div class="col-md-12">
            <div class="form-group">
                <label class="control-label">{{ _lang('Status') }}</label>                        
                <select class="form-control auto-select" data-selected="{{ $group->status }}" name="status" required>
                    <option value="1">{{ _lang('Active') }}</option>
                    <option value="0">{{ _lang('Deactivated') }}</option>
                </select>
            </div>
        </div>


        <div class="col-md-12">
            <div class="form-group">
                <button type="submit" class="btn btn-primary"><i class="ti-check-box"></i>&nbsp;{{ _lang('Update') }}</button>
            </div>
        </div>
    </div>
</form>

