<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
    body {
        background: #ffffff;
    }

    .settings-card {
        background: #102236;
        border: 1px solid #1E3A5F;
        border-radius: 16px;
        padding: 40px;
        box-shadow: 0 0 25px rgba(0, 0, 0, 0.25);
    }

    .brand-title {
        font-size: 28px;
        font-weight: 700;
        color: #4EA6FF;
        letter-spacing: 1px;
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

    label {
        color: #d0e0ff !important;
        font-weight: 500;
    }
</style>

<div class="container d-flex align-items-center justify-content-center mb-5" style="min-height: 100vh;">
    <div class="col-12 col-md-8 col-lg-6">

        <!-- LOGO -->
        <div class="text-center mb-4">
            <img src="{{ asset('default_image/logo_cassa.png') }}"
                 alt="logo_meteora_cassa"
                 style="width: 220px;">
        </div>

        <!-- TITOLO -->
        <div class="text-center mb-4">
            <div class="brand-title">CONFIGURAZIONE CASSA</div>
            <p class="text-white-50 mt-2">Configura i parametri della postazione</p>
        </div>

        <div class="settings-card">

            {{-- MESSAGGIO SUCCESSO --}}
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            {{-- ERRORI GLOBALI --}}
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="m-0 ps-3">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('option.store') }}">
                @csrf
                @method('POST')
                <div class="row mb-2">
                    <!-- ENDPOINT METEORA -->
                    <div class="col-xl-12 col-md-12 col-sm-12 mb-3">
                        <label class="form-label">Endpoint Meteora *</label>
                        <input type="text"
                               name="endpoint_meteora"
                               class="form-control @error('endpoint_meteora') is-invalid @enderror"
                               placeholder="Es: https://"
                               value="{{ old('endpoint_meteora', $options['endpoint_meteora'] ?? '') }}">
                    </div>

                    <!-- NUMERO ARCHIVIO -->
                    <div class="col-xl-4 col-md-12 col-sm-12 mb-3">
                        <label class="form-label">N° Archivio *</label>
                        <input type="text"
                               name="numero_archivio"
                               class="form-control @error('numero_archivio') is-invalid @enderror"
                               placeholder="Es: 1"
                               value="{{ old('numero_archivio', $options['numero_archivio'] ?? '') }}">
                    </div>

                    <!-- IP -->
                    <div class="col-xl-4 col-md-12 col-sm-12 mb-3">
                        <label class="form-label">TCP IP *</label>
                        <input type="text"
                               name="stampante_ip"
                               class="form-control @error('stampante_ip') is-invalid @enderror"
                               placeholder="Es: 192.168.1.50"
                               value="{{ old('stampante_ip', $options['stampante_ip'] ?? '') }}">
                    </div>

                    <!-- PORTA -->
                    <div class="col-xl-4 col-md-12 col-sm-12 mb-3">
                        <label class="form-label">Porta *</label>
                        <input type="text"
                               name="stampante_porta"
                               class="form-control @error('stampante_porta') is-invalid @enderror"
                               placeholder="Es: 9100"
                               value="{{ old('stampante_porta', $options['stampante_porta'] ?? '') }}">
                    </div>

                    <!-- TOKEN CASSA -->
                    <div class="col-xl-12 col-md-12 col-sm-12 mb-3">
                        <label class="form-label">Token cassa</label>
                        <input type="text"
                               name="token_cassa"
                               class="form-control @error('token_cassa') is-invalid @enderror"
                               placeholder="Es: ds78cghsdc..."
                               value="{{ old('token_cassa', $options['token_cassa'] ?? '') }}">
                    </div>

                </div>

                <hr class="border-secondary my-4">

                <!-- REPARTI -->
                <div class="row">

                    @for($i = 1; $i <= 5; $i++)
                        <div class="col-xl-4 col-md-12 mb-3">
                            <label class="form-label">Reparto {{ $i }}</label>
                            <input type="text"
                                   name="reparto_{{ $i }}"
                                   class="form-control"
                                   placeholder="Es: {{ $i * 5 }}"
                                   value="{{ old("reparto_$i", $options["reparto_$i"] ?? '') }}">
                        </div>
                    @endfor

                </div>

                <button type="submit" class="btn btn-primary w-100 py-2 mt-2">
                    Salva
                </button>

            </form>

        </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
