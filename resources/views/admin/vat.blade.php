@extends('layouts.admin')

@section('content')
<div class="container">
    <h2>Tax Settings List</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Tax Rate (%)</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($taxSettings as $setting)
                <tr>
                    <td>{{ $setting->id }}</td>
                    <td>{{ $setting->tax_rate }}%</td>
                    <td>
                        <a href="{{ route('tax-settings.edit', $setting->id) }}" class="btn btn-sm btn-primary">Edit</a>
                        {{-- Optional Delete Button --}}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
