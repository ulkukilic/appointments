@extends('layouts.app')

@section('title', 'Staff Members')
@section('page_title', 'Manage Staff Members')

@section('content')
  @include('layouts.alerts')

  <table class="table table-striped">
    <thead>
      <tr>
        <th>#</th>
        <th>Full Name</th>
        <th>Experience Level</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      @foreach($staff as $s)
        <tr>
          <td>{{ $loop->iteration }}</td>
          <td>{{ $s->full_name }}</td>
          <td>{{ $s->experience_level }}</td>
          <td>
            <form method="POST" action="{{ route('admin.staff.delete', $s->staff_member_uni_id) }}">
              @csrf
              @method('DELETE')
              <button class="btn btn-danger btn-sm">Sil</button>
            </form>
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>

  <h4 class="mt-4">Add New Staff Member</h4>
  <form method="POST" action="{{ route('admin.staff.add') }}" class="row g-2">
    @csrf
    <div class="col-md-5">
      <input type="text" name="full_name" class="form-control" placeholder="Full Name" required>
    </div>
    <div class="col-md-5">
      <input type="text" name="experience_level" class="form-control" placeholder="Experience Level" required>
    </div>
    <div class="col-md-2">
      <button type="submit" class="btn btn-primary w-100">Ekle</button>
    </div>
  </form>
@endsection