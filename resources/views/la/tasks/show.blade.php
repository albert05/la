@extends('la.layouts.app')

@section('htmlheader_title')
	Task View
@endsection


@section('main-content')
<div id="page-content" class="profile2">
	<div class="bg-success clearfix">
		<div class="col-md-4">
			<div class="row">
				<div class="col-md-3">
					<img class="profile-image" src="{{ Gravatar::fallback(asset('/img/avatar5.png'))->get(Auth::user()->email, ['size'=>400]) }}" alt="">
				</div>
				<div class="col-md-9">
					<h4 class="name">{{ $task->$view_col }}</h4>
					<div class="row stats">
						<div class="col-md-6 stat"><div class="label2" data-toggle="tooltip" data-placement="top" title="Designation">{{ $task->designation }}</div></div>
						<div class="col-md-6 stat"><i class="fa fa-map-marker"></i> {{ $task->city or "NA" }}</div>
					</div>
					<p class="desc">{{ substr($task->about, 0, 33) }}@if(strlen($task->about) > 33)...@endif</p>
				</div>
			</div>
		</div>
		<div class="col-md-3">
			<div class="dats1"><i class="fa fa-envelope-o"></i> {{ $task->email }}</div>
			<div class="dats1"><i class="fa fa-phone"></i> {{ $task->mobile }}</div>
			<div class="dats1"><i class="fa fa-clock-o"></i> Joined on {{ date("M d, Y", strtotime($task->date_hire)) }}</div>
		</div>
		<div class="col-md-4">
			<div class="teamview">
				<a class="face" data-toggle="tooltip" data-placement="top" title="John Doe"><img src="{{ asset('la-assets/img/user1-128x128.jpg') }}" alt=""><i class="status-online"></i></a>
				<a class="face" data-toggle="tooltip" data-placement="top" title="John Doe"><img src="{{ asset('la-assets/img/user2-160x160.jpg') }}" alt=""></a>
				<a class="face" data-toggle="tooltip" data-placement="top" title="John Doe"><img src="{{ asset('la-assets/img/user3-128x128.jpg') }}" alt=""></a>
				<a class="face" data-toggle="tooltip" data-placement="top" title="John Doe"><img src="{{ asset('la-assets/img/user4-128x128.jpg') }}" alt=""><i class="status-online"></i></a>
				<a class="face" data-toggle="tooltip" data-placement="top" title="John Doe"><img src="{{ asset('la-assets/img/user5-128x128.jpg') }}" alt=""></a>
				<a class="face" data-toggle="tooltip" data-placement="top" title="John Doe"><img src="{{ asset('la-assets/img/user6-128x128.jpg') }}" alt=""></a>
				<a class="face" data-toggle="tooltip" data-placement="top" title="John Doe"><img src="{{ asset('la-assets/img/user7-128x128.jpg') }}" alt=""></a>
				<a class="face" data-toggle="tooltip" data-placement="top" title="John Doe"><img src="{{ asset('la-assets/img/user8-128x128.jpg') }}" alt=""></a>
				<a class="face" data-toggle="tooltip" data-placement="top" title="John Doe"><img src="{{ asset('la-assets/img/user5-128x128.jpg') }}" alt=""></a>
				<a class="face" data-toggle="tooltip" data-placement="top" title="John Doe"><img src="{{ asset('la-assets/img/user6-128x128.jpg') }}" alt=""><i class="status-online"></i></a>
				<a class="face" data-toggle="tooltip" data-placement="top" title="John Doe"><img src="{{ asset('la-assets/img/user7-128x128.jpg') }}" alt=""></a>
			</div>
			
		</div>
		<div class="col-md-1 actions">
			@la_access("Tasks", "edit")
				<a href="{{ url(config('laraadmin.adminRoute') . '/tasks/'.$task->id.'/edit') }}" class="btn btn-xs btn-edit btn-default"><i class="fa fa-pencil"></i></a><br>
			@endla_access
			
			@la_access("Tasks", "delete")
				{{ Form::open(['route' => [config('laraadmin.adminRoute') . '.tasks.destroy', $task->id], 'method' => 'delete', 'style'=>'display:inline']) }}
					<button class="btn btn-default btn-delete btn-xs" type="submit"><i class="fa fa-times"></i></button>
				{{ Form::close() }}
			@endla_access
		</div>
	</div>

	<ul data-toggle="ajax-tab" class="nav nav-tabs profile" role="tablist">
		<li class=""><a href="{{ url(config('laraadmin.adminRoute') . '/tasks') }}" data-toggle="tooltip" data-placement="right" title="Back to Tasks"><i class="fa fa-chevron-left"></i></a></li>
		<li class="active"><a role="tab" data-toggle="tab" class="active" href="#tab-info" data-target="#tab-info"><i class="fa fa-bars"></i> General Info</a></li>
		<li class=""><a role="tab" data-toggle="tab" href="#tab-timeline" data-target="#tab-timeline"><i class="fa fa-clock-o"></i> Timeline</a></li>
		@if($task->id == Auth::user()->id || Entrust::hasRole("SUPER_ADMIN"))
			<li class=""><a role="tab" data-toggle="tab" href="#tab-account-settings" data-target="#tab-account-settings"><i class="fa fa-key"></i> Account settings</a></li>
		@endif
	</ul>

	<div class="tab-content">
		<div role="tabpanel" class="tab-pane active fade in" id="tab-info">
			<div class="tab-content">
				<div class="panel infolist">
					<div class="panel-default panel-heading">
						<h4>General Info</h4>
					</div>
					<div class="panel-body">
						@la_display($module, 'name')
						@la_display($module, 'designation')
						@la_display($module, 'gender')
						@la_display($module, 'mobile')
						@la_display($module, 'mobile2')
						@la_display($module, 'email')
						@la_display($module, 'dept')
						@la_display($module, 'city')
						@la_display($module, 'address')
						@la_display($module, 'about')
						@la_display($module, 'date_birth')
						@la_display($module, 'date_hire')
						@la_display($module, 'date_left')
						@la_display($module, 'salary_cur')
					</div>
				</div>
			</div>
		</div>
	</div>
	</div>
	</div>
</div>
@endsection

@push('scripts')
<script>
$(function () {
});
</script>
@endpush
