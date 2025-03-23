<form method="post" class="ajax-screen-submit" autocomplete="off" action="{{ route('group_members.switchPayoutPosition', $id) }}" enctype="multipart/form-data">
	{{ csrf_field()}}
	<div class="row px-2">					

		<div class="col-md-12">
			<div class="form-group">
				<label class="control-label">{{ _lang('Input New Position') }}</label>						
				<input type="number" class="form-control" name="new_position" value="{{ $member->payout_position_number }}" required>
			</div>
		</div>
	
		<div class="form-group">
		    <div class="col-md-12">
			    <button type="submit" class="btn btn-primary"><i class="ti-check-box"></i>&nbsp;{{ _lang('Switch Position') }}</button>
		    </div>
		</div>
	</div>
</form>

