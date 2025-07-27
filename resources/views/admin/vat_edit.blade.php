@extends('layouts.admin') {{-- অথবা আপনার layout এর নাম --}}

@section('content')
<div class="container">
    <h2>Edit Tax Rate</h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('tax-settings.update', $taxSetting->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="tax_rate">Tax Rate (%)</label>
            <input type="number" name="tax_rate" id="tax_rate"
                   class="form-control"
                   value="{{ old('tax_rate', $taxSetting->tax_rate) }}"
                   step="0.01" required>
            @error('tax_rate')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary mt-2">Update</button>
    </form>
</div>
@endsection
