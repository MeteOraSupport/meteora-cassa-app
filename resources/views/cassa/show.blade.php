@extends('layouts.app')
@section('content')
    <div class="row p-2" id="row_first">
        <div class="col-1 col-lg-1 div-button-save-return"></div>
    </div>

    <div class="row g-3">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <form method="POST" action="{{ route('order.store.cassa') }}" method="post">
                        @csrf
                        <div class="row g-3 p-3">
                            <div class="col-4">
                                <input type="hidden" name="archive_id" value="{{ $cassa['archive_id'] }}">
                                <input type="hidden" name="cassa_id" value="{{ $cassa['id'] }}">
                                <button type="submit" class="btn btn-outline-info w-100">
                                    <i class="bi bi-receipt"></i> Nuovo scontrino
                                </button>
                            </div>
                            <div class="col-4">
                                <a href="{{ route('cassa.index') }}" class="btn btn-outline-primary w-100">
                                    <i class="bi bi-card-checklist"></i> Lista scontrini
                                </a>
                            </div>
                            <div class="col-4">
                                <a href="{{ route('cassa.logout') }}" class="btn btn-outline-danger w-100">
                                    <i class="bi bi-door-closed"></i> Chiudi cassa
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        {{-- AREA SINISTRA --}}
        <div class="col-12 col-xl-10">
            <div class="card shadow-sm">
                <div class="card-body">

                    {{-- Barra input rapidi --}}
                    <div class="row g-3 p-2">

                        {{-- EAN --}}
                        <div class="col-12 col-md-4">
                            <label class="form-label fw-semibold">EAN / Barcode veloce</label>
                            <div class="input-group input-group-sm shadow-sm position-relative">
                                <span class="input-group-text bg-primary text-white">
                                    <i class="fa-solid fa-barcode"></i>
                                </span>
                                <input type="text" class="form-control eanVarInput pe-5"
                                    placeholder="Scansiona o digita EAN..." @disabled($order['read'] == 1)>
                                <span class="clear-input-btn">&times;</span>
                            </div>
                        </div>

                        {{-- UPC --}}
                        <div class="col-12 col-md-4">
                            <label class="form-label fw-semibold">UPC-A / veloce</label>
                            <div class="input-group input-group-sm shadow-sm position-relative">
                                <span class="input-group-text bg-primary text-white">
                                    <i class="fa-solid fa-barcode"></i>
                                </span>
                                <input type="text" class="form-control upcVarInput pe-5"
                                    placeholder="Scansiona o digita UPC-A..." @disabled($order['read'] == 1)>
                                <span class="clear-input-btn">&times;</span>
                            </div>
                        </div>

                        {{-- EAN Manuale --}}
                        <div class="col-12 col-md-4">
                            <label class="form-label fw-semibold">EAN / Barcode manuale</label>
                            <div class="input-group input-group-sm shadow-sm position-relative">
                                <span class="input-group-text bg-primary text-white">
                                    <i class="fa-solid fa-barcode"></i>
                                </span>
                                <input type="text" class="form-control eanVarInputManual pe-5"
                                    placeholder="Digita Barcode..." @disabled($order['read'] == 1)>
                                <span class="clear-input-btn">&times;</span>
                            </div>
                        </div>

                        {{-- SKU --}}
                        <div class="col-12">
                            <label class="form-label fw-semibold">SKU / Codice articolo</label>
                            <div class="input-group input-group-lg position-relative">
                                <input type="text" id="input_sku" class="form-control skuInputManual pe-5"
                                    @disabled($order['read'] == 1)>
                            </div>
                        </div>

                    </div>

                    {{-- Tabella carrello --}}
                    <div class="table-responsive mt-4">
                        <table class="table table-striped align-middle" id="table_fiscal">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center" style="width: 220px;">Articolo</th>
                                    <th class="text-center" style="width: 20px;">Reparto</th>
                                    <th class="text-center" style="width: 20px;">Prezzo</th>
                                    <th class="text-center" style="width: 25px;">Qtà</th>
                                    <th class="text-center" style="width: 30px;">Sconto</th>
                                    <th class="text-center" style="width: 30px;">Totale</th>
                                    <th class="text-center" style="width: 56px;">Azione</th>
                                </tr>
                            </thead>

                            <tbody id="cart_body"></tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>

        {{-- AREA DESTRA --}}
        <div class="col-12 col-xl-2">
            {{-- Box pagamento --}}
            <div class="card shadow-sm position-sticky" style="top: 1rem;">
                <div class="card-body">
                    {{-- Info operatore --}}
                    <div class="row g-3 align-items-center mb-1">
                        <div class="col-12">
                            <div class="fs-5 mb-1">
                                <strong>Cassa:</strong> {{ $cassa['name'] }}
                            </div>
                            <div class="fs-6 text-muted">
                                <strong>Operatore:</strong> {{ $user['name'] }} {{ $user['surname'] }}
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div id="alert_cash" class="alert alert-warning p-2 text-center small mb-3 d-none">
                        Inserisci o seleziona un importo contante prima di incassare.
                    </div>

                    {{-- Totali --}}
                    <div class="mb-2 d-flex justify-content-between border-bottom pb-1">
                        <span class="text-muted">Subtotale</span>
                        <span class="fw-semibold text-secondary" id="subtotal_label">0,00€</span>
                    </div>

                    <div id="discount_row" class="mb-2 d-flex justify-content-between border-bottom pb-1 d-none">
                        <span class="text-muted">Sconto</span>
                        <span class="fw-semibold text-secondary" id="total_discount_label">0,00€</span>
                    </div>

                    <div class="mt-2 d-flex justify-content-between fs-6">
                        <span class="fw-bold">Da pagare</span>
                        <span class="fw-bold text-success" id="total_label">0,00€</span>
                    </div>

                    <hr>

                    {{-- Bottoni pagamento --}}
                    <div class="d-grid gap-2">

                        @php
                            $paymentName = $order['payment']['name_payment'] ?? null;

                            $cashActive = $paymentName === 'contanti' && $order['read'] == 1;
                            $posActive = $paymentName === 'elettronico' && $order['read'] == 1;
                        @endphp

                        {{-- CASH --}}
                        <button type="button"
                            class="btn payment-btn {{ $cashActive ? 'btn-secondary active' : 'btn-outline-secondary' }}"
                            id="btn_payment_cash" data-value="contanti">
                            <i class="bi bi-cash"></i> Pagamento contanti
                        </button>

                        {{-- POS --}}
                        <button type="button"
                            class="btn payment-btn {{ $posActive ? 'btn-info active' : 'btn-outline-info' }}"
                            id="btn_payment_pos" data-value="elettronico">
                            <i class="bi bi-credit-card"></i> Pagamento POS
                        </button>

                        {{-- Importo --}}
                        <div id="cash_section" class="mt-3 {{ $cashActive ? '' : 'd-none' }}">
                            <label class="form-label fw-semibold">Importo consegnato</label>

                            <div class="d-flex flex-wrap gap-2 mb-2">
                                @foreach ([2, 5, 10, 20, 50, 100] as $val)
                                    <button type="button" class="btn btn-outline-dark cash-btn"
                                        data-value="{{ $val }}">
                                        {{ $val }}€
                                    </button>
                                @endforeach
                            </div>

                            <div class="input-group">
                                <span class="input-group-text">€</span>
                                <input type="number" min="0" step="0.01" id="cash_given" name="cash_given"
                                    class="form-control" value="{{ $order['order_deposit'] }}"
                                    placeholder="Inserisci importo">
                            </div>
                        </div>

                        <button type="button" class="btn btn-success btn-lg mt-3"
                            @if ($order['read'] == 1) disabled @endif id="btn_checkout">
                            <i class="bi bi-cash-stack"></i> Incassa
                        </button>

                    </div>

                    <input type="hidden" name="type_payment" id="type_payment" value="">
                </div>
            </div>
        </div>

    </div>

    {{-- CSS --}}
    <style>
        #table_cart td,
        #table_cart th {
            font-size: 1.05rem;
        }

        .payment-btn.active,
        .cash-btn.active {
            color: #fff !important;
            border-color: #198754 !important;
            background-color: #198754 !important;
        }

        .eanVarInput:focus,
        .upcVarInput:focus {
            box-shadow: 0 0 8px rgba(13, 110, 253, 0.5);
            border-color: #0d6efd;
        }

        #discount_row {
            transition: opacity .3s ease;
        }

        .clear-input-btn {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 1.2rem;
            color: #bbb;
            display: none;
            cursor: pointer;
            z-index: 10;
        }

        .clear-input-btn:hover {
            color: #333;
        }

        .input-group input:not(:placeholder-shown)+.clear-input-btn {
            display: block;
        }

        /* Suggestions SKU */
        .sku-suggestions {
            position: absolute;
            top: 100%;
            left: 0;
            width: 100%;
            max-height: 300px;
            overflow-y: auto;
            z-index: 2000;
            background: #fff;
            border: 1px solid #dee2e6;
            border-top: none;
            border-radius: 0 0 .5rem .5rem;
            animation: fadeIn .15s ease;
        }

        .sku-suggestions .list-group-item {
            cursor: pointer;
            font-size: .9rem;
        }

        .sku-suggestions .list-group-item:hover {
            background: #f1f3f5;
        }

        .col-td-data {
            text-align: center;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-4px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
    <script>
        document.addEventListener("DOMContentLoaded", () => {

            /* ==========================
               CONFIGURAZIONE ENDPOINT API
               ========================== */
            const endpoint = "{{ rtrim($endpoint, '/') }}"; // Es: https://api.meteora.it
            const archiveId = "{{ $order['archive_id'] }}"; // ID archivio per tutte le API

            // Config axios (CSRF ecc.)
            if (typeof axios !== 'undefined') {
                const tokenMeta = document.querySelector('meta[name="csrf-token"]');
                if (tokenMeta) {
                    axios.defaults.headers.common['X-CSRF-TOKEN'] = tokenMeta.content;
                }
                axios.defaults.headers.common['Accept'] = 'application/json';
                axios.defaults.headers.common['Content-Type'] = 'application/json';
                axios.defaults.withCredentials = true;
            }

            /* ==========================
               INIZIALIZZAZIONE DATATABLE
               ========================== */
            let table_fiscal = $('#table_fiscal').DataTable({
                dom: 'lBfrtip',
                stateSave: true,
                // buttons: [{
                //     extend: 'colvis',
                //     text: 'Colonne'
                // }],
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('order.product.index', ['order_id' => $order['id']]) }}"
                },
                columns: [{
                        data: "name_product"
                    },
                    {
                        data: "iva_prod",
                        className: "col-td-data"
                    },
                    {
                        data: "gross_selling_price_var",
                        className: "col-td-data"
                    },
                    {
                        data: "quantity_order",
                        className: "col-td-data"
                    },
                    {
                        data: "discount"
                    },
                    {
                        data: "gross_selling_price_total",
                        className: "col-td-data"
                    },
                    {
                        data: "delete_prod_comb"
                    }
                ],
                language: {
                    "url": {!! json_encode(url('/json/italian.json')) !!}
                }
            });

            // 👇 IL TUO EVENTO DEVE ESSERE FUORI DALLA CONFIGURAZIONE
            table_fiscal.on('draw', function() {
                const forms = document.getElementsByClassName("form_product_single");
                if (forms.length > 0) {
                    for (let i = 0; i < forms.length; i++) {
                        forms[i].addEventListener('submit', function(e) {
                            e.preventDefault();

                            let order_id = forms[i].querySelectorAll("input[type=hidden]")[0].value;
                            let prod_combination_delete_id = forms[i].querySelectorAll(
                                    "input[type=hidden]")[1]
                                .value;
                            let stock_variation_id = forms[i].querySelectorAll(
                                "input[type=hidden]")[2].value;

                            fetch("{{ route('order.product.cassa.delete') }}", {
                                    headers: {
                                        'Accept': 'application/json',
                                        'Content-Type': 'application/json',
                                        "X-Requested-With": "XMLHttpRequest",
                                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                                            'content')
                                    },
                                    method: 'DELETE',
                                    body: JSON.stringify({
                                        stock_combination_delete_id: stock_variation_id,
                                        product_combination_id: prod_combination_delete_id,
                                        order_id: order_id
                                    })
                                })
                                .then(response => response.json())
                                .then(res => {
                                    if (res.status === 200) {
                                        // 🔄 Aggiorna le tabelle se esistono
                                        table_fiscal.draw(false);
                                    } else if (res.status === 400) {
                                        console.warn("Errore eliminazione riga:", res.msg);
                                    }
                                })
                                .catch(error => console.error(error));
                        });
                    }
                }
            });

            /* ==========================
               AGGIORNAMENTO TOTALI
               ========================== */
            table_fiscal.on('draw', function() {
                const data = table_fiscal.ajax.json();
                if (!data) return;

                const cleanNumber = v => parseFloat((v ?? "0").replace(/,/g, ""));
                const formatEuro = v => v.toLocaleString('it-IT', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }) + ' €';

                const subtotal = cleanNumber(data.totalSubGross);
                const discount = cleanNumber(data.totalDiscountGross);
                const total = cleanNumber(data.totalGross);

                $('#subtotal_label').text(formatEuro(subtotal));
                $('#total_discount_label').text((discount > 0 ? '-' : '') + formatEuro(discount));
                $('#total_label').text(formatEuro(total));

                $('#discount_row').toggleClass('d-none', discount <= 0);
            });

            /* ==========================
               RICERCA PRODOTTO VIA EAN / UPC (scanner)
               ========================== */
            let inputTimer;
            $('.upcVarInput, .eanVarInput').on('input', function() {
                clearTimeout(inputTimer);
                const currentInput = $(this);

                inputTimer = setTimeout(() => {
                    const value = currentInput.val().trim();
                    if (value === '') return;

                    const url = currentInput.hasClass('eanVarInput') ?
                        `${endpoint}/api/v1/${archiveId}/combination/ean/${value}` :
                        `${endpoint}/api/v1/${archiveId}/combination/upc/${value}`;

                    axios.get(url)
                        .then(response => {
                            const result = response.data;
                            if (!result || !result.product_combination_id) {
                                console.error('Nessun product_combination_id ricevuto');
                                return;
                            }

                            return axios.post("{{ route('order.product.store') }}", {
                                order_id: {{ $order['id'] }},
                                product_combination_id: result.product_combination_id,
                                product_combination_qty: 1
                            });
                        })
                        .then(response => {
                            if (!response) return;
                            const res = response.data;
                            if (res.status === 200) {
                                table_fiscal.draw(false);
                            }
                        })
                        .catch(err => console.error(err));

                    currentInput.val('');
                }, 300);
            });

            /* ============================
               RICERCA EAN MANUALE (debounce, senza AbortController)
               ============================ */
            let inputTimerManual;

            $('.eanVarInputManual').on('input', function() {
                clearTimeout(inputTimerManual);
                const $input = $(this);
                const value = $input.val().trim();
                if (value.length < 3) return;

                inputTimerManual = setTimeout(() => {
                    const url =
                        `${endpoint}/api/v1/${archiveId}/combination/ean/${encodeURIComponent(value)}`;

                    axios.get(url)
                        .then(response => {
                            const result = response.data;
                            if (!result || !result.product_combination_id) {
                                $input.removeClass('is-valid').addClass('is-invalid');
                                return null;
                            }
                            return axios.post("{{ route('order.product.store') }}", {
                                order_id: {{ $order['id'] }},
                                product_combination_id: result.product_combination_id,
                                product_combination_qty: 1
                            });
                        })
                        .then(response => {
                            if (!response) return;
                            const res = response.data;
                            if (res.status === 200) {
                                table_fiscal.draw(false);
                                $input.removeClass('is-invalid').addClass('is-valid');
                            } else {
                                $input.removeClass('is-valid').addClass('is-invalid');
                            }
                        })
                        .catch(err => {
                            console.error(err);
                            $input.removeClass('is-valid').addClass('is-invalid');
                        });
                }, 400);
            });

            /* ==========================
               AUTOCOMPLETE SKU MANUALE
               ========================== */
            let inputTimerSkuManual;

            $('.skuInputManual').on('input', function() {
                clearTimeout(inputTimerSkuManual);
                const $input = $(this);
                const value = $input.val().trim();

                if (value.length < 2) {
                    removeSkuDropdown();
                    return;
                }

                inputTimerSkuManual = setTimeout(() => {
                    const url =
                        `${endpoint}/api/v1/${archiveId}/combination/sku/${encodeURIComponent(value)}`;

                    axios.get(url)
                        .then(response => {
                            const result = response.data;
                            if (!Array.isArray(result) || result.length === 0) {
                                removeSkuDropdown();
                                return;
                            }
                            showSkuDropdown($input, result);
                        })
                        .catch(err => {
                            console.error(err);
                            removeSkuDropdown();
                        });
                }, 300);
            });

            function showSkuDropdown($input, products) {
                removeSkuDropdown();
                const $dropdown = $('<div class="sku-suggestions list-group"></div>');

                products.forEach(p => {
                    const $item = $(`
                        <button type="button" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" data-id="${p.product_combination_id}">
                            <div>
                                <div class="fw-semibold">${p.name_product}</div>
                                <small class="text-muted">${p.sku} ${p.variant ? '— ' + p.variant : ''}</small>
                            </div>
                            <span class="badge bg-success">${Number(p.price).toFixed(2)} €</span>
                        </button>
                    `);
                    $dropdown.append($item);
                });

                $input.closest('.input-group').append($dropdown);
            }

            function removeSkuDropdown() {
                $('.sku-suggestions').remove();
            }

            // Click su elemento SKU
            $(document).on('click', '.sku-suggestions .list-group-item', function() {
                const id = $(this).data('id');
                removeSkuDropdown();

                axios.post("{{ route('order.product.store') }}", {
                        order_id: {{ $order['id'] }},
                        product_combination_id: id,
                        product_combination_qty: 1
                    })
                    .then(response => {
                        const res = response.data;
                        if (res.status === 200) {
                            table_fiscal.draw(false);
                        }
                    })
                    .catch(err => console.error(err));
            });

            // Chiudi dropdown se clicchi fuori
            $(document).on('click', function(e) {
                if (!$(e.target).closest('.skuInputManual, .sku-suggestions').length) {
                    removeSkuDropdown();
                }
            });

            function showSkuFeedback(message, type = 'info') {
                const colors = {
                    info: 'alert-primary',
                    success: 'alert-success',
                    warning: 'alert-warning',
                    danger: 'alert-danger'
                };

                $('#row_first .alert[data-origin="sku"]').remove();

                const alertDiv = $(`
            <div class="alert ${colors[type] || 'alert-secondary'} shadow-sm rounded d-flex align-items-center py-2 px-3 my-2"
                data-origin="sku" role="alert" style="font-size:1rem;">
                <div class="flex-grow-1">${message}</div>
                <button type="button" class="btn-close ms-2" data-bs-dismiss="alert" aria-label="Chiudi"></button>
            </div>
        `);

                $('#row_first').append(alertDiv);
                setTimeout(() => {
                    alertDiv.fadeOut(500, () => alertDiv.remove());
                }, 5000);
            }

            /* ==========================
               UPDATE QUANTITÀ + SCONTO
               ========================== */
            function updateOrderQuantities() {
                let stock_comb_id = [];
                let prod_comb_id = [];
                let discount = [];
                let qty = [];

                $('.quantity_order_client').each(function(index) {
                    qty[index] = $(this).val();
                });
                $('.product_comb_id').each(function(index) {
                    prod_comb_id[index] = $(this).val();
                });
                $('.stock_variation_id').each(function(index) {
                    stock_comb_id[index] = $(this).val();
                });
                $('.discount').each(function(index) {
                    discount[index] = $(this).val().replace(',', '.');
                });

                axios.put("{{ route('order.product.discount.qty', ['order_id' => $order['id']]) }}", {
                        stock_order_single: qty,
                        product_combination_id: prod_comb_id,
                        stock_combination_id: stock_comb_id,
                        discount: discount
                    })
                    .then(response => {
                        const res = response.data;

                        let alertDiv = document.createElement('div');
                        alertDiv.classList.add(
                            'alert', 'alert-info', 'rounded',
                            'shadow-sm', 'mb-3', 'p-4', 'd-flex', 'mt-3'
                        );
                        alertDiv.setAttribute('data-turbo-temporary', '');
                        alertDiv.textContent = res.msg ?? 'Quantità aggiornata correttamente.';

                        let closeButton = document.createElement('button');
                        closeButton.type = 'button';
                        closeButton.classList.add('btn-close', 'ms-auto');
                        closeButton.setAttribute('data-bs-dismiss', 'alert');
                        closeButton.setAttribute('aria-label', 'Close');
                        alertDiv.appendChild(closeButton);

                        let row = document.getElementById('row_first');
                        if (row) {
                            row.appendChild(alertDiv);
                            setTimeout(() => {
                                alertDiv.classList.add('fade');
                                alertDiv.addEventListener('transitionend', () => alertDiv.remove());
                            }, 5000);
                        }

                        if (res.status === 200) {
                            if (typeof table_fiscal !== 'undefined') table_fiscal.draw(false);
                            if (typeof tableOrderClient !== 'undefined') tableOrderClient.draw(false);
                            if (typeof tableOrderClientModal !== 'undefined') tableOrderClientModal.draw(false);
                        }
                    })
                    .catch(error => console.log('Errore:', error));
            }

            let updateTimerQty;
            $(document).on('input', '.quantity_order_client', function() {
                clearTimeout(updateTimerQty);

                const currentValue = $(this).val();
                if (currentValue === '' || isNaN(currentValue) || parseFloat(currentValue) <= 0) return;

                updateTimerQty = setTimeout(() => {
                    updateOrderQuantities();
                }, 500);
            });

            function updateOrderDiscount() {
                let prod_comb_id = [];
                let discount = [];

                $('.product_comb_id').each(function(index) {
                    prod_comb_id[index] = $(this).val();
                });
                $('.discount').each(function(index) {
                    discount[index] = $(this).val().replace(',', '.');
                });

                axios.put("{{ route('order.product.discount.update', ['order_id' => $order['id']]) }}", {
                        product_combination_id: prod_comb_id,
                        discount: discount
                    })
                    .then(response => {
                        const res = response.data;

                        let alertDiv = document.createElement('div');
                        alertDiv.classList.add(
                            'alert', 'alert-info', 'rounded',
                            'shadow-sm', 'mb-3', 'p-4', 'd-flex', 'mt-3'
                        );
                        alertDiv.setAttribute('data-turbo-temporary', '');
                        alertDiv.textContent = res.msg ?? 'Quantità aggiornata correttamente.';

                        let closeButton = document.createElement('button');
                        closeButton.type = 'button';
                        closeButton.classList.add('btn-close', 'ms-auto');
                        closeButton.setAttribute('data-bs-dismiss', 'alert');
                        closeButton.setAttribute('aria-label', 'Close');
                        alertDiv.appendChild(closeButton);

                        let row = document.getElementById('row_first');
                        if (row) {
                            row.appendChild(alertDiv);
                            setTimeout(() => {
                                alertDiv.classList.add('fade');
                                alertDiv.addEventListener('transitionend', () => alertDiv.remove());
                            }, 5000);
                        }

                        if (res.status === 200) {
                            if (typeof table_fiscal !== 'undefined') table_fiscal.draw(false);
                            if (typeof tableOrderClient !== 'undefined') tableOrderClient.draw(false);
                            if (typeof tableOrderClientModal !== 'undefined') tableOrderClientModal.draw(false);
                        }
                    })
                    .catch(error => console.log('Errore:', error));
            }

            let updateTimerDiscount;
            $(document).on('input', '.discount', function() {
                clearTimeout(updateTimerDiscount);

                const currentValue = $(this).val();
                if (currentValue === '' || isNaN(currentValue)) return;

                updateTimerDiscount = setTimeout(() => {
                    updateOrderDiscount();
                }, 500);
            });

            /* ==========================
               TIPO PAGAMENTO E SEZIONE CASH
               ========================== */
            function buttonPaymentSelected() {
                const paymentButtons = document.querySelectorAll('.payment-btn');
                const hiddenInput = document.getElementById('type_payment');
                const cashSection = document.getElementById('cash_section');

                paymentButtons.forEach(btn => {
                    btn.addEventListener('click', () => {
                        paymentButtons.forEach(b => b.classList.remove('active'));
                        btn.classList.add('active');
                        hiddenInput.value = btn.dataset.value;

                        if (btn.dataset.value === 'contanti') {
                            cashSection.classList.remove('d-none');
                        } else {
                            cashSection.classList.add('d-none');
                            document.getElementById('cash_given').value = '';
                            document.querySelectorAll('.cash-btn').forEach(b => b.classList.remove(
                                'active'));
                        }
                    });
                });
            }

            function handleCashSection() {
                const cashButtons = document.querySelectorAll('.cash-btn');
                const cashInput = document.getElementById('cash_given');

                cashButtons.forEach(btn => {
                    btn.addEventListener('click', () => {
                        cashButtons.forEach(b => b.classList.remove('active'));
                        btn.classList.add('active');

                        cashInput.value = parseFloat(btn.dataset.value).toFixed(2);
                        $('#cash_given').trigger('input');
                    });
                });
            }

            document.getElementById('btn_checkout').addEventListener('click', (e) => {
                const type = document.getElementById('type_payment').value;
                const alertCash = document.getElementById('alert_cash');

                if (type === 'contanti') {
                    const cashInput = document.getElementById('cash_given');
                    let value = (cashInput.value || '').trim();

                    if (value === '' || parseFloat(value) <= 0) {
                        let totalText = $('#total_label').text() || '0';
                        totalText = totalText.replace(/[^\d,.-]/g, '').replace(',', '.');
                        const total = parseFloat(totalText) || 0;

                        cashInput.value = total.toFixed(2);
                        console.log(`💶 Impostato automaticamente importo contante = ${total.toFixed(2)}€`);
                    }
                }
                alertCash.classList.add('d-none');
            });

            buttonPaymentSelected();
            handleCashSection();

            // preserva il valore dell'input EAN manuale tra i redraw
            $('#table_fiscal').on('preDraw.dt', function() {
                const $m = $('.eanVarInputManual');
                if ($m.length) $m.data('preserve', $m.val());
            });

            $('#table_fiscal').on('draw.dt', function() {
                const $m = $('.eanVarInputManual');
                if ($m.length) {
                    const v = $m.data('preserve');
                    if (typeof v !== 'undefined') $m.val(v);
                }
            });

            let type_payment_value = null;

            // selezione metodo pagamento
            $(document).on('click', '.payment-btn', function() {
                $('.payment-btn').removeClass('active');
                $(this).addClass('active');
                type_payment_value = $(this).data('value');
            });

            // calcolo resto in tempo reale
            $(document).on('input', '#cash_given', function() {
                if (type_payment_value !== 'contanti') return;

                let totalLabelText = $('#total_label').text() || $('#total_label').val() || "0";
                totalLabelText = (totalLabelText + '').replace(/[^\d,.-]/g, '').replace(',', '.');
                const totalLabel = !isNaN(parseFloat(totalLabelText)) ? parseFloat(totalLabelText) : 0;

                let cashGivenText = $(this).val() || "0";
                cashGivenText = (cashGivenText + '').replace(/[^\d,.-]/g, '').replace(',', '.');
                const cashGiven = !isNaN(parseFloat(cashGivenText)) ? parseFloat(cashGivenText) : 0;

                const change = cashGiven - totalLabel;

                let changeDiv = $('#cash_change');
                if (changeDiv.length === 0) {
                    $('#cash_section').append('<div id="cash_change" class="mt-2 fw-bold"></div>');
                    changeDiv = $('#cash_change');
                }

                if (!cashGivenText || cashGiven === 0) {
                    changeDiv.html('');
                    return;
                }

                const formattedChange = change.toLocaleString('it-IT', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });

                if (change < 0) {
                    changeDiv.html(`Resto: <span style="color:red;">${formattedChange} €</span>`);
                } else {
                    changeDiv.html(`Resto: <span style="color:green;">${formattedChange} €</span>`);
                }
            });

            // INCASSO
            $(document).on('click', '#btn_checkout', function() {
                if (!type_payment_value) {
                    alert('⚠️ Seleziona prima una modalità di pagamento.');
                    return;
                }

                if (type_payment_value === 'contanti') {
                    const totalLabel = parseFloat($('#total_label').text()) || parseFloat($('#total_label')
                        .val()) || 0;
                    const cashGiven = parseFloat($('#cash_given').val()) || 0;
                    const change = cashGiven - totalLabel;

                    if (cashGiven === 0 || isNaN(cashGiven)) {
                        alert('⚠️ Inserisci l\'importo ricevuto dal cliente.');
                        return;
                    }

                    if (change < 0) {
                        alert('⚠️ L\'importo ricevuto è inferiore al totale. Non è possibile incassare.');
                        return;
                    }
                }

                let order_deposit = document.getElementById('cash_given').value;
                let total_order_raw = document.getElementById('total_label').innerText ||
                    document.getElementById('total_label').value;
                let total_order = total_order_raw.replace(/[^\d.,]/g, '').replace(',', '.');

                const eanVarInput = document.getElementsByClassName('eanVarInput');
                const upcVarInput = document.getElementsByClassName('upcVarInput');
                const eanVarInputManual = document.getElementsByClassName('eanVarInputManual');
                const inputSku = document.getElementById('input_sku');

                const quantity_order_client = document.getElementsByClassName('quantity_order_client');
                const discount = document.getElementsByClassName('discount');
                const stockIdVariation = document.getElementsByClassName('stockIdVariation');

                const btn = $('#btn_checkout');
                btn.prop('disabled', true).text('Elaborazione...');

                Array.from(quantity_order_client).forEach(el => el.disabled = true);
                Array.from(discount).forEach(el => el.disabled = true);
                Array.from(stockIdVariation).forEach(el => el.disabled = true);
                Array.from(eanVarInput).forEach(el => el.disabled = true);
                Array.from(upcVarInput).forEach(el => el.disabled = true);
                Array.from(eanVarInputManual).forEach(el => el.disabled = true);
                if (inputSku) inputSku.disabled = true;

                axios.put(
                        "{{ route('order.cassa.update', ['order_id' => $order['id']]) }}", {
                            order_deposit: order_deposit,
                            type_payment_value: type_payment_value,
                            total_order: total_order
                        })
                    .then(response => {
                        const res = response.data;

                        btn.text('Incassato ✅').addClass('btn-success').removeClass('btn-primary');

                        let alertDiv = document.createElement('div');
                        alertDiv.classList.add(
                            'alert', 'alert-info', 'rounded',
                            'shadow-sm', 'mb-3', 'p-4', 'd-flex', 'mt-3'
                        );
                        alertDiv.textContent = res.msg ?? 'Scontrino concluso.';

                        let closeButton = document.createElement('button');
                        closeButton.type = 'button';
                        closeButton.classList.add('btn-close', 'ms-auto');
                        closeButton.setAttribute('data-bs-dismiss', 'alert');
                        closeButton.setAttribute('aria-label', 'Close');
                        alertDiv.appendChild(closeButton);

                        let row = document.getElementById('row_first');
                        if (row) {
                            row.appendChild(alertDiv);
                            setTimeout(() => {
                                alertDiv.classList.add('fade');
                                alertDiv.addEventListener('transitionend', () => alertDiv
                                    .remove());
                            }, 5000);
                        }
                    })
                    .catch(error => {
                        btn.prop('disabled', false).html('<i class="bi bi-cash-stack"></i> Incassa');
                        console.error('Errore:', error);
                        alert('Si è verificato un errore durante l’incasso.');
                    });
            });
        });
    </script>
@endsection
