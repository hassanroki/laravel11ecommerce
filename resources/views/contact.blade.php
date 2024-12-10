@extends('layouts.app')

@section('content')
    <main class="pt-90">
        <div class="mb-4 pb-4"></div>
        <section class="contact-us container">
            <div class="mw-930">
                <h2 class="page-title">CONTACT US</h2>
            </div>
        </section>

        <hr class="mt-2 text-secondary " />
        <div class="mb-4 pb-4"></div>

        <section class="contact-us container">
            <div class="mw-930">
                <div class="contact-us__form">
                    @include('_message')
                    <form name="contact-us-form" action="{{ route('home.contact.store') }}" class="needs-validation"
                        novalidate="" method="POST">
                        @csrf
                        <h3 class="mb-5">Get In Touch</h3>
                        <div class="form-floating my-4">
                            <input type="text" value="{{ old('name') }}" class="form-control" id="name"
                                name="name" placeholder="Name *" required="">
                            <label for="name">Name *</label>
                            @error('name')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror

                        </div>

                        <div class="form-floating my-4">
                            <input type="email" {{ old('email') }} class="form-control" id="email" name="email"
                                placeholder="Email address *" required="">
                            <label for="email">Email address *</label>
                            @error('email')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-floating my-4">
                            <input type="text" {{ old('phone') }} class="form-control" id="phone" name="phone"
                                placeholder="Phone *" required="">
                            <label for="phone">Phone *</label>
                            @error('phone')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="my-4">
                            <textarea class="form-control form-control_gray" id="comment" name="comment" placeholder="Your Message *"
                                cols="30" rows="8" required="">{{ old('comment') }}</textarea>
                            @error('comment')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="my-4">
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </main>
@endsection
