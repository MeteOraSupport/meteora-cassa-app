@extends('layouts.app')
@section('content')
    <style>
        body {
            background: #ffffff;
            /* background: #1c1a22; */
        }

        .login-card {
            background: #102236;
            border: 1px solid #1E3A5F;
            border-radius: 16px;
            padding: 40px;
            box-shadow: 0 0 25px rgba(0, 0, 0, 0.25);
        }

        .brand-title {
            font-size: 32px;
            font-weight: 700;
            color: #4EA6FF;
            letter-spacing: 1px;
        }

        .form-control {
            /* background: #0A1A2F !important; */
            border: 1px solid #2A4B6B !important;
            color: #000000 !important;
        }

        .form-control:focus {
            border-color: #4EA6FF !important;
            box-shadow: 0 0 0 0.25rem rgba(78, 166, 255, 0.25) !important;
        }

        .btn-primary {
            background: #4EA6FF;
            border-color: #4EA6FF;
            font-weight: 600;
        }

        .btn-primary:hover {
            background: #73bbff;
            border-color: #73bbff;
        }
    </style>
    <form method="GET" action="{{ route('cassa.check.code') }}">
        <div class="container d-flex align-items-center justify-content-center" style="min-height: 100vh;">
            <div class="col-12 col-md-6 col-lg-4">
                <div class="text-center mb-4">
                    <img src="{{ asset('default_image/logo_restyle_2.png') }}" alt="logo_meteora_cassa" style="width: 220px;">
                </div>
                <div class="text-center mb-4">
                    <div class="brand-title">ACCESSO CASSA</div>
                    <!-- <div><span>Vers. {{ config('nativephp.version') }}</span></div> -->
                    <p class="text-white-50 mt-2">Inserisci il tuo codice per continuare</p>
                </div>

                <div class="login-card">
                    {{-- <form method="POST" action="{{ route('operator.login') }}"> --}}
                    @csrf

                    <div class="mb-4">
                        <label class="form-label text-white-50">Codice Operatore</label>
                        <input type="password" name="access_code" class="form-control" placeholder="••••••••" autofocus>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2">
                        Entra
                    </button>

                    @error('access_code')
                        <div class="text-danger mt-2" style="font-size: 14px;">
                            {{ $message }}
                        </div>
                    @enderror

                    {{-- eventuali messaggi --}}
                    @if (session('error'))
                        <div class="alert alert-danger mt-3 p-2 text-center">
                            {{ session('error') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </form>
@endsection
