@extends('layouts.app')
@section('content')
    <div class="container mt-4">
        <div class="row">

            {{-- MESSAGGIO SUCCESSO --}}
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <div class="col-xl-6 col-12 col-md-6">
                <h2 class="mb-3">Scontrini Cassa #{{ $cassa['id'] }}</h2>
                <div class="fs-5 mb-1">
                    <strong>Cassa:</strong> {{ $cassa['name'] }}
                </div>
                <div class="fs-6 text-muted">
                    <strong>Operatore:</strong> {{ $user['name'] }} {{ $user['surname'] }}
                </div>
            </div>


            <div class="col-xl-6 col-12 col-md-6">
                <form method="POST" action="{{ route('order.store.cassa') }}">
                    @csrf

                    <div class="row g-3 p-3">

                        <input type="hidden" name="archive_id" value="{{ $cassa['archive_id'] }}">
                        <input type="hidden" name="cassa_id" value="{{ $cassa['id'] }}">

                        <div class="col-6">
                            <button type="submit" class="btn btn-outline-info w-100">
                                <i class="bi bi-receipt"></i> Nuovo scontrino
                            </button>
                        </div>

                        <div class="col-6">
                            <a href="{{ route('cassa.logout') }}" class="btn btn-outline-danger w-100">
                                <i class="bi bi-door-closed"></i> Chiudi cassa
                            </a>
                        </div>

                    </div>
                </form>
            </div>

        </div>
        {{-- Controllo se ci sono ordini --}}
        @if (empty($orders['orders']['data']['data']))
            <div class="alert alert-info">
                Nessun ordine trovato per questa cassa.
            </div>
        @else
            <div class="table-responsive mt-3">
                <table class="table table-striped table-bordered align-middle">
                    <thead class="table-dark">
                        <tr>
                            {{-- <th>ID</th> --}}
                            <th>Numero Ordine</th>
                            <th>Data</th>
                            <th>Totale</th>
                            <th>Stato</th>
                            <th>Azioni</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($orders['orders']['data']['data'] as $order)
                            <tr>
                                <td>{{ $order['id'] }}</td>

                                {{-- numero ordine (o quello che è) --}}
                                {{-- <td>{{ $order['numero'] ?? '-' }}</td> --}}

                                {{-- data --}}
                                <td>
                                    @if (!empty($order['date_order']))
                                        {{ \Carbon\Carbon::parse($order['date_order'])->format('d/m/Y H:i') }}
                                    @else
                                        -
                                    @endif
                                </td>

                                {{-- totale --}}
                                <td>{{ number_format($order['total_order'] ?? 0, 2, ',', '.') }} €</td>

                                <td>
                                    <span class="badge bg-primary">
                                        {{ $order['read'] == 1 ? 'Incassato' : 'In sospeso' }}
                                    </span>
                                </td>

                                {{-- azioni --}}
                                <td>
                                    <a href="{{ route('cassa.show.order', ['order_id' => $order['id']]) }}"
                                        class="btn btn-sm btn-outline-primary">Dettagli</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>

                </table>
                {{-- PAGINAZIONE API --}}
                @if (!empty($orders['orders']['data']))
                    @php
                        $pagination = $orders['orders']['data'];
                    @endphp

                    <nav class="mt-3">
                        <ul class="pagination justify-content-start">

                            {{-- Pagina precedente --}}
                            <li class="page-item {{ $pagination['prev_page_url'] ? '' : 'disabled' }}">
                                <a class="page-link"
                                    href="{{ $pagination['prev_page_url'] ? url()->current() . '?page=' . ($pagination['current_page'] - 1) : '#' }}">
                                    « Indietro
                                </a>
                            </li>

                            {{-- Numeri di pagina --}}
                            @for ($i = 1; $i <= $pagination['last_page']; $i++)
                                <li class="page-item {{ $i == $pagination['current_page'] ? 'active' : '' }}">
                                    <a class="page-link" href="{{ url()->current() . '?page=' . $i }}">
                                        {{ $i }}
                                    </a>
                                </li>
                            @endfor

                            {{-- Pagina successiva --}}
                            <li class="page-item {{ $pagination['next_page_url'] ? '' : 'disabled' }}">
                                <a class="page-link"
                                    href="{{ $pagination['next_page_url'] ? url()->current() . '?page=' . ($pagination['current_page'] + 1) : '#' }}">
                                    Avanti »
                                </a>
                            </li>

                        </ul>
                    </nav>
                @endif
            </div>
        @endif

    </div>
@endsection
