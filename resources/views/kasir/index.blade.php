@extends('template.master')


@section('content')
    <style>
        /* Sembunyikan tombol toggle mobile di POS */
        body.pos-mode .pc-mob-drp,
        body.pos-mode .pc-sidebar-popup,
        body.pos-mode .pc-sidebar-collapse {
            display: none !important;
        }
    </style>

    <style>
        :root {
            --pos-muted: #6c757d;
        }

        body {
            background: #f6f7fb;
        }

        .pos-card {
            border: 0;
            box-shadow: 0 6px 18px rgba(0, 0, 0, .06);
            border-radius: 14px;
        }

        .pos-header {
            border-bottom: 1px solid rgba(0, 0, 0, .06);
        }

        .table thead th {
            font-size: .82rem;
            color: var(--pos-muted);
        }

        .table td {
            vertical-align: middle;
        }

        .badge-disc {
            background: #e9f7ef;
            color: #1e7e34;
            border: 1px solid #cfeedd;
            font-weight: 600;
        }

        .money {
            font-variant-numeric: tabular-nums;
        }

        .btn-soft {
            background: #f1f3f5;
            border: 1px solid #e9ecef;
        }

        .btn-soft:hover {
            background: #e9ecef;
        }

        .pay-btn {
            width: 100%;
            text-align: left;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .pay-tag {
            font-size: .78rem;
            color: var(--pos-muted);
        }

        .sticky-right {
            position: sticky;
            top: 16px;
        }

        .item-thumb {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: #e9ecef;
            display: inline-block;
        }

        .search-icon {
            width: 2.75rem;
        }

        .qty-input {
            width: 78px;
        }


        @media (max-width: 991.98px) {
            .sticky-right {
                position: static;
            }
        }
    </style>
    </head>

    <body>
        <div class="container-fluid py-4">
            <div class="row g-3">
                <!-- KIRI: KERANJANG -->
                <div class="col-lg-8">
                    <div class="card pos-card">
                        <div class="card-body">

                            <!-- Bar atas -->
                            <div
                                class="d-flex flex-column flex-md-row gap-2 align-items-stretch align-items-md-center mb-3">
                                <div class="input-group">
                                    <span class="input-group-text bg-white search-icon">🔎</span>
                                    <input id="barcodeInput" type="text" class="form-control"
                                        placeholder="Scan barcode atau cari produk (nama, SKU)" autocomplete="off">
                                    <button class="btn btn-success" type="button" id="btnTambah">Tambah</button>
                                </div>

                                <div class="d-flex gap-2">
                                    

                                    <button class="btn btn-outline-danger" id="batal" type="button">
                                        Batal Transaksi
                                    </button>
                                </div>

                            </div>

                            <!-- Tabel item -->
                            <div class="table-responsive table-bordered">
                                <table class="table align-middle mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th style="min-width:100px;">Item</th>
                                            <th class="text-end" style="min-width:100px;">Harga</th>
                                            <th class="text-center" style="min-width:110px;">Qty</th>
                                            <th class="text-end" style="min-width:110px;">Harga Total</th>

                                            <th class="text-center" style="min-width:100px;">Diskon</th>

                                            <th class="text-end" style="min-width:104px;">Subtotal</th>
                                            <th class="text-center" style="width:60px;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody id="cartBody">


                                    </tbody>
                                </table>
                            </div>

                            <!-- Footer kiri -->
                            <div
                                class="mt-3 d-flex flex-column flex-md-row gap-2 justify-content-between align-items-stretch align-items-md-center">
                                <div class="input-group" style="max-width: 380px;">
                                    <span class="input-group-text bg-white">Pelanggan</span>
                                    <input type="text" class="form-control" id="pelanggan"
                                        placeholder="Nama pelanggan (opsional)">
                                </div>

                                <div class="small text-muted">
                                    Tips: klik input di atas, lalu scan barcode. Enter akan menambahkan item.
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- KANAN: RINGKASAN -->
                <div class="col-lg-4">
                    <div class="card pos-card sticky-right">
                        <div class="card-body">


                            <!-- Subtotal, diskon, voucher -->
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Subtotal</span>
                                <span id="subTotal" class="money">0</span>
                            </div>

                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Diskon Item</span>
                                <span id="discItem" class="money text-danger">0</span>
                            </div>

                            <!-- Voucher -->
                            <div class="border rounded-3 p-2 mb-2 bg-light">
                                <div class="small fw-semibold mb-2">Voucher / Kode Promo</div>
                                <div class="input-group">
                                    <input id="voucherCode" type="text" class="form-control" placeholder="Contoh: HEMAT10">
                                    <button id="btnApplyVoucher" class="btn btn-success" type="button">Pakai</button>

                                </div>
                                <div class="d-flex justify-content-between mt-2">
                                    <span class="text-muted small">Potongan Voucher</span>
                                    <span id="voucherDiscount" class="money small text-danger">- Rp 0</span>
                                </div>
                                <div id="voucherInfo" class="small text-muted mt-1">Voucher opsional. Jika tidak ada,
                                    kosongkan.</div>
                            </div>

                            <hr class="my-3">



                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div class="fw-semibold">Total</div>
                                <div id="grandTotal" class="fs-4 fw-bold money">0</div>
                            </div>
                            @if (App\Helpers\FormatHelper::taxCount() > 0)
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted" id="texttax">Pajak </span>
                                    <span id="tax" class="money text-danger">0</span>
                                </div>
                            @endif

                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div class="fw-semibold">Total Akhir</div>
                                <div id="grandTotalakhir" class="fs-4 fw-bold money">0</div>
                            </div>

                            <!-- Pembayaran -->
                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <div class="border rounded-3 p-2">
                                        <div class="small text-muted">Dibayar</div>
                                        <input type="text" class="form-control money" id="paid">
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="border rounded-3 p-2">
                                        <div class="small text-muted">Kembalian</div>
                                        <div id="kembalian" class="fw-semibold text-danger money">0</div>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-2 fw-semibold">Metode Pembayaran</div>
                            <select name="payment_method" id="paymentMethod" class="form-select mb-2">
                                <option value="">Pilih Metode</option>
                                <option value="cash">Tunai</option>
                                <option value="qris">QRIS</option>
                                <option value="debit">Kartu Debit</option>
                                <option value="credit">Kartu Kredit</option>
                                <option value="transfer">Transfer Bank</option>
                            </select>
                            <div class="d-flex gap-2">
                                <button id="bayar" type="button" class="btn btn-success w-100 py-2 fw-semibold">
                                    Selesaikan Transaksi
                                </button>

                                <button id="cetak" type="button" style="display: none"
                                    class="btn btn-warning w-100 py-2 fw-semibold">
                                    Cetak
                                </button>

                                <button id="transaksiBaru" style="display: none" type="button"
                                    class="btn btn-primary w-100 py-2 fw-semibold">
                                    Transaksi Baru
                                </button>
                            </div>


                        </div>
                    </div>
                </div>
            </div>
        </div>


    </body>

    </html>
@endsection


@section('script')
    <!-- Bootstrap JS -->
    <script>
        $(document).ready(function () {
            $('.money').each(function () {
                let value = parseFloat($(this).text().replace(/[^0-9,-]+/g, "").replace(",", "."));
                let formattedValue = new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0
                }).format(value);
                $(this).text(formattedValue);
            });

            $('#barcodeInput').focus();

            $(document).on('click', '.qty-increase', function () {
                let qtyInput = $(this).closest('td').find('.qty-input');
                let currentQty = parseInt(qtyInput.val());
                qtyInput.val(currentQty + 1).trigger('change');
            });
            $(document).on('click', '.qty-decrease', function () {
                let qtyInput = $(this).closest('td').find('.qty-input');
                let currentQty = parseInt(qtyInput.val());
                if (currentQty > 1) {
                    qtyInput.val(currentQty - 1).trigger('change');
                }
            });

            function submitProduk() {
                let barcode = $('#barcodeInput').val().trim();
                if (!barcode) {
                    swal.fire({
                        icon: 'error',
                        title: 'Gagal menambahkan produk',
                        text: 'Masukkan barcode atau nama produk.',
                        timer: 1000,
                    });
                    return;
                }

                if (window.loadingTambah) return;
                window.loadingTambah = true;

                $.ajax({
                    url: "{{ route('kasir.tambahProduk') }}",
                    method: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        barcode: barcode
                    },
                    success: function (response) {
                        $('#barcodeInput').val('').focus();
                        fetchCart();
                    },
                    error: function (xhr) {
                        swal.fire({
                            icon: 'error',
                            title: 'Gagal menambahkan produk',
                            text: xhr.responseJSON.message || 'Produk tidak ditemukan atau stok habis.',
                            timer: 1000,
                        });
                        $('#barcodeInput').val('').focus();
                    },
                    complete: function () {
                        window.loadingTambah = false;
                    }
                });
            }

            $('#btnTambah').on('click', function () {
                submitProduk();
            });

            $('#barcodeInput').on('keydown', function (e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    submitProduk();
                }
            });


            $(document).on('click', '#btnRemoveVoucher', function () {
                $voucherId = $(this).data('id');
                $transactionId = $(this).data('transaksi');

                $.ajax({
                    url: "{{ route('kasir.removeVoucher') }}",
                    method: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        voucherId: $voucherId,
                        transactionId: $transactionId

                    },
                    success: function (response) {
                        fetchCart();
                        $('#voucherInfo').empty();
                        $('#voucherDiscount').text(0);
                    },
                    error: function (xhr) {
                        swal.fire({
                            icon: 'error',
                            title: 'Gagal menghapus voucher',
                            text: xhr.responseJSON.message || 'Terjadi kesalahan saat menghapus voucher.',
                            timer: 1000,
                        });
                    }
                });
            })

            async function fetchCart() {
                try {
                    let response = await $.ajax({
                        url: "{{ route('kasir.keranjang') }}",
                        method: "GET",
                    });
                    let cartBody = $('#cartBody');
                    cartBody.empty();
                    console.log(response);

                    $('#voucherCode').val(response.vouchers.code);
                    $('#bayar').data('transaksi', response.transactionId);
                    $('#batal').data('transaksi', response.transactionId);

                    if (response.vouchers.code != null) {
                        $('#voucherDiscount').text(-response.vouchers.discount_amount);
                        $('#voucherInfo').html(

                            `<div class="d-flex align-items-center gap-2">
                                                                                                        <button id="btnRemoveVoucher" 
                                                                                                        data-id="${response.vouchers.id}" 
                                                                                                        data-transaksi="${response.transactionId}"
                                                                                                        class="btn btn-sm btn-outline-danger">Hapus Voucher</button>
                                                                                                    </div>`
                        )
                    }


                    response.items.forEach(function (item) {
                        let row = `<tr>
                                                                                                            <td>
                                                                                                                <div class="d-flex align-items-center gap-2">
                                                                                                                    <div>
                                                                                                                        <div class="fw-semibold">${item.product_name}</div>
                                                                                                                        <div class="small text-muted">Stok: ${item.stock} · Barcode: ${item.barcode}</div>
                                                                                                                    </div>
                                                                                                                </div>
                                                                                                            </td>
                                                                                                            <td class="text-end money" data-price="${item.price}">${item.price}</td>
                                                                                                            <td class="text-center">
                                                                                                                <div class="d-inline-flex align-items-center gap-1">
                                                                                                                    <button class="btn btn-sm btn-outline-secondary qty-decrease" data-transaksi="${item.transaction_id}" data-product="${item.product_id}" title="Kurangi Qty">−</button>
                                                                                                                    <input class="form-control form-control-sm text-center qty-input" type="number" min="1" value="${item.quantity}" style="width:60px;">
                                                                                                                    <button class="btn btn-sm btn-outline-secondary qty-increase" data-transaksi="${item.transaction_id}" data-product="${item.product_id}" title="Tambah Qty">+</button>


                                                                                                                </div>
                                                                                                            </td>
                                                                                                            <td class="text-end fw-semibold sub-totalori money" data-subtotal>${item.price * item.quantity}</td>


                                                                                                            <td class="text-center" style="min-width:100px;">
                                                                                                                <div class="discount-box"
                                                                                                                    data-transaksi="${item.transaction_id}"
                                                                                                                    data-product="${item.product_id}">`;

                        const applied = Array.isArray(item.applied_discount_ids) ? item
                            .applied_discount_ids : [];
                        let totalNominalDiskon = 0;
                        (item.discount || []).forEach(function (disc) {
                            const checked = applied.includes(disc.id);
                            const disabled = item.quantity < disc.min_qty;
                            // hitung nominal diskon HANYA jika aktif

                            const nominalDiskon = (disc.percentage / 100) * item.price *
                                item.quantity;
                            totalNominalDiskon += nominalDiskon;


                            row += `
                                                                                    <div class="form-check text-start d-flex align-items-center gap-2">
                                                                                        <input
                                                                                            class="form-check-input discount-check"
                                                                                            type="radio"  
                                                                                            data-nominal="${totalNominalDiskon}"
                                                                                            name="discount-${item.product_id}" 
                                                                                            value="${disc.id}"
                                                                                            ${checked ? 'checked' : ''}
                                                                                            ${disabled ? 'disabled' : ''}
                                                                                        >
                                                                                        <label class="form-check-label d-flex flex-column gap-1">
                                                                                            <div class="d-flex align-items-center gap-2">
                                                                                                <span>${disc.percentage}%</span>
                                                                                                <span class="badge bg-success">Min ${disc.min_qty}</span>
                                                                                            </div>
                                                                                            <small class="text-muted diskon-item">
                                                                                                -Rp ${Math.round(totalNominalDiskon).toLocaleString('id-ID')}
                                                                                            </small>
                                                                                        </label>
                                                                                    </div>
                                                                                `;
                            totalNominalDiskon = 0;
                        });


                        row += `</div>
                                                                                                            </td>

                                                                                                            <td class="text-end fw-semibold money sub-total" data-subtotal>${item.line_total}</td>
                                                                                                            <td class="text-center">
                                                                                                                <button class="btn btn-sm btn-outline-secondary hapus" data-transaksi="${item.transaction_id}" data-product="${item.product_id}" title="Hapus">🗑️</button>
                                                                                                            </td>
                                                                                                        </tr>`;

                        cartBody.append(row);

                    });
                    // Reformat money fields
                    $('.money').each(function () {
                        let value = parseFloat($(this).text().replace(/[^0-9,-]+/g, "").replace(",",
                            "."));
                        let formattedValue = new Intl.NumberFormat('id-ID', {
                            style: 'currency',
                            currency: 'IDR',
                            minimumFractionDigits: 0
                        }).format(value);
                        $(this).text(formattedValue);
                    });

                    let grandTotal = 0;
                    let grandTotalDiskon = 0;

                    $('.sub-totalori').each(function () {
                        const raw = $(this).text()
                            .replace(/[^\d]/g, ''); // ambil angka saja

                        const value = Number(raw) || 0;
                        grandTotal += value;
                    });

                    $('#subTotal').text(
                        new Intl.NumberFormat('id-ID', {
                            style: 'currency',
                            currency: 'IDR',
                            minimumFractionDigits: 0
                        }).format(grandTotal)
                    );

                    $('.discount-check:checked').each(function () {
                        let raw = $(this).data('nominal'); // Ganti const dengan let
                        raw = Math.round(raw); // Ubah nilai raw

                        const value = Number(raw) || 0; // Pastikan raw menjadi angka
                        grandTotalDiskon -= value; // Kurangi grandTotalDiskon dengan nilai diskon

                    });

                    $('#discItem').text(
                        new Intl.NumberFormat('id-ID', {
                            style: 'currency',
                            currency: 'IDR',
                            minimumFractionDigits: 0
                        }).format(grandTotalDiskon)
                    );
                    let vouchers = 0;
                    let totalakhir = 0;
                    if (response.vouchers.discount_amount > 0) {
                        vouchers = response.vouchers.discount_amount;
                        totalakhir = grandTotal - (-grandTotalDiskon + vouchers);
                    } else {
                        vouchers = 0;
                    }

                    $('#grandTotal').text(
                        new Intl.NumberFormat('id-ID', {
                            style: 'currency',
                            currency: 'IDR',
                            minimumFractionDigits: 0
                        }).format(grandTotal - (-grandTotalDiskon + vouchers))
                    );

                    let TotalAkhir = 0;
                    let RatePajak = 0;

                    if ({{ App\Helpers\FormatHelper::taxCount() }} > 0) {
                        RatePajak = {{ App\Helpers\FormatHelper::tax() }};
                        TotalAkhir = grandTotal - (-grandTotalDiskon + vouchers);
                        percenTax = RatePajak + ' %';
                        $('#texttax').text('{{ App\Helpers\FormatHelper::taxName() }}' + ' (' + percenTax +
                            ')');
                        $('#tax').text(
                            new Intl.NumberFormat('id-ID', {
                                style: 'currency',
                                currency: 'IDR',
                                minimumFractionDigits: 0
                            }).format(parseInt(TotalAkhir * RatePajak / 100))
                        )
                        $('#grandTotalakhir').text(
                            new Intl.NumberFormat('id-ID', {
                                style: 'currency',
                                currency: 'IDR',
                                minimumFractionDigits: 0
                            }).format(TotalAkhir + (parseInt(TotalAkhir * RatePajak / 100)))
                        );
                    } else {
                        TotalAkhir = grandTotal - (-grandTotalDiskon + vouchers);
                        $('#grandTotalakhir').text(
                            new Intl.NumberFormat('id-ID', {
                                style: 'currency',
                                currency: 'IDR',
                                minimumFractionDigits: 0
                            }).format(TotalAkhir)
                        );
                    }


                    let paid = $('#paid').val();

                    if (!paid) {
                        paid = 0;
                    }

                    // ambil angka saja
                    paid = parseInt(paid.toString().replace(/\D/g, '')) || 0;

                    // hitung kembalian
                    let kembalian = paid - TotalAkhir;

                    // tampilkan
                    $('#kembalian').text(
                        new Intl.NumberFormat('id-ID', {
                            style: 'currency',
                            currency: 'IDR',
                            minimumFractionDigits: 0
                        }).format(kembalian > 0 ? kembalian : 0)
                    );

                } catch (error) {
                    console.error('Error fetching cart:', error);
                }
            }

            fetchCart();

            $(document).on('input', '#paid', function () {
                const tagihan = $('#grandTotalakhir').text().replace(/[^\d]/g, '');
                const bayar = $(this).val().replace(/[^\d]/g, '');
                const kembalian = bayar - tagihan;
                $('#kembalian').text(
                    new Intl.NumberFormat('id-ID', {
                        style: 'currency',
                        currency: 'IDR',
                        minimumFractionDigits: 0
                    }).format(kembalian)
                );

            });

            Inputmask({
                alias: "numeric",
                groupSeparator: ".",
                autoGroup: true,
                prefix: "Rp ",
                rightAlign: false,
                placeholder: "0"
            }).mask('#paid');

            $(document).on('change', '.discount-check', function () {

                const checkbox = $(this);
                const box = checkbox.closest('.discount-box');

                const transactionId = box.data('transaksi');
                const productId = box.data('product');
                const discountId = checkbox.val();
                const action = checkbox.is(':checked') ? 'attach' : 'detach';

                $.ajax({
                    url: "{{ route('kasir.updateDiskon') }}",
                    method: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        transactionId: transactionId,
                        productId: productId,
                        discountId: discountId,
                        action: action
                    },
                    success: function (response) {
                        fetchCart(); // refresh subtotal & total
                    },
                    error: function (xhr) {
                        swal.fire({
                            icon: 'error',
                            title: 'Gagal memperbarui diskon',
                            text: xhr.responseJSON.message || 'Terjadi kesalahan saat memperbarui diskon.',
                            timer: 1000,
                        });
                        checkbox.prop('checked', !checkbox.is(':checked')); // rollback UI
                    }
                });
            });


            $(document).on('click', '.hapus', function () {
                const transactionId = $(this).data('transaksi');
                const productId = $(this).data('product');
                $.ajax({
                    url: "{{ route('kasir.hapusKeranjang') }}",
                    method: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        transactionId: transactionId,
                        productId: productId
                    },
                    success: function (response) {
                        fetchCart();
                    },
                    error: function (xhr) {
                        swal.fire({
                            icon: 'error',
                            title: 'Gagal menghapus produk',
                            text: xhr.responseJSON.message || 'Terjadi kesalahan saat menghapus produk.',
                            timer: 1000,
                        });
                    }
                });
            });

            $(document).on('click', '.qty-increase', function () {
                const input = $(this).closest('td').next().find('.qty-input');

                const transactionId = $(this).data('transaksi');
                const productId = $(this).data('product');
                input.val(parseInt(input.val()) + 1);

                $.ajax({
                    url: "{{ route('kasir.qtyProduk') }}",
                    method: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        barcode: $('#barcodeInput').val().trim(),
                        transactionId: transactionId,
                        productId: productId,
                        increase: true
                    },
                    success: function (response) {
                        fetchCart();
                    },
                    error: function (xhr) {
                        fetchCart();
                        swal.fire({
                            icon: 'error',
                            title: 'Gagal menambah produk',
                            text: xhr.responseJSON.message || 'Produk tidak ditemukan atau stok habis.',
                            timer: 1000,
                        });
                    }
                });
            });
            $(document).on('click', '.qty-decrease', function () {
                const input = $(this).closest('td').next().find('.qty-input');
                const currentVal = parseInt(input.val());
                if (currentVal > 1) {
                    input.val(currentVal - 1);
                }
                const transactionId = $(this).data('transaksi');
                const productId = $(this).data('product');
                $.ajax({
                    url: "{{ route('kasir.qtyProduk') }}",
                    method: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        barcode: $('#barcodeInput').val().trim(),
                        transactionId: transactionId,
                        productId: productId,
                        decrease: true
                    },
                    success: function (response) {
                        fetchCart();
                    },
                    error: function (xhr) {
                        fetchCart();
                        swal.fire({
                            icon: 'error',
                            title: 'Gagal mengurangi produk',
                            text: xhr.responseJSON.message || 'Produk tidak ditemukan atau stok habis.',
                            timer: 1000,
                        });
                    }
                });
            });

            $(document).on('click', '#btnApplyVoucher', function () {
                const voucher = $('#voucherCode').val();
                const transactionId = $(this).data('transaksi');

                $.ajax({
                    url: "{{ route('kasir.voucher') }}",
                    method: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        voucher: voucher
                    },
                    success: function (response) {
                        fetchCart();
                        if (response.success === false) {
                            {
                                swal.fire({
                                    icon: 'error',
                                    title: 'Gagal menerapkan voucher',
                                    text: response.message || 'Voucher tidak valid atau tidak memenuhi syarat.',
                                    timer: 1000,
                                });
                            }
                        }
                    },
                    error: function (xhr) {
                        swal.fire({
                            icon: 'error',
                            title: 'Gagal menerapkan voucher',
                            text: xhr.responseJSON.message || 'Terjadi kesalahan saat menerapkan voucher.',
                            timer: 1000,
                        });
                    }

                });
            });

            $(document).on('click', '#bayar', function () {
                const transactionId = $(this).data('transaksi');
                const subTotal = $('#subTotal').text().replace(/[^0-9,-]+/g, "").replace(",", "");
                const discItem = $('#discItem').text().replace(/[^0-9,-]+/g, "").replace(",", "");
                const grandTotal = $('#grandTotalakhir').text().replace(/[^0-9,-]+/g, "").replace(",",
                    "");
                const paid = $('#paid').val().replace(/[^0-9.-]+/g, "").replace(/,/g,
                    ""); // Hapus karakter selain angka dan hilangkan koma
                const kembalian = $('#kembalian').text().replace(/[^0-9,-]+/g, "");
                const paymentMethod = $('#paymentMethod').val();
                const pelanggan = $('pelanggan').val();
                const tax_rate = {{ App\Helpers\FormatHelper::tax() }};
                const tax_amount = $('#tax').text().replace(/[^0-9,-]+/g, "").replace(",", "");

                if (paid == "") {
                    swal.fire({
                        icon: 'error',
                        title: 'Gagal melakukan pembayaran',
                        text: 'Masukkan jumlah pembayaran.',
                        timer: 1000,
                    });
                    return false;
                }

                if (paymentMethod == "") {
                    swal.fire({
                        icon: 'error',
                        title: 'Gagal melakukan pembayaran',
                        text: 'Pilih Metode Pembayaran.',
                        timer: 1000,
                    });
                    return false;
                }
                if (parseFloat(paid) < parseFloat(grandTotal)) {
                    swal.fire({
                        icon: 'error',
                        title: 'Gagal melakukan pembayaran',
                        text: 'Jumlah pembayaran kurang dari total tagihan.',
                        timer: 1000,
                    });
                    return false;
                }

                $.ajax({
                    url: "{{ route('kasir.bayar') }}",
                    method: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        transactionId: transactionId,
                        subTotal: subTotal,
                        discItem: discItem,
                        grandTotal: grandTotal,
                        paid: paid,
                        kembalian: kembalian,
                        paymentMethod: paymentMethod,
                        pelanggan: pelanggan,
                        tax_rate: tax_rate,
                        tax_amount: tax_amount
                    },
                    success: function (response) {
                        if (response.success) {
                            // Print the receipt
                            printReceipt(response.receiptContent);
                            $('#bayar').prop('disabled', true);
                            $('#batal').prop('disabled', true);
                            $('#btnTambah').prop('disabled', true);
                            $('#btnApplyVoucher').prop('disabled', true);
                            $('#btnRemoveVoucher').prop('disabled', true);
                            $('#pelanggan').prop('disabled', true);
                            $('#paymentMethod').prop('disabled', true);
                            $('.qty-input').prop('disabled', true);
                            $('.qty-increase').prop('disabled', true);
                            $('.qty-decrease').prop('disabled', true);
                            $('#discount-check').prop('disabled', true);
                            $('#cetak').css('display', 'block');
                            $('#transaksiBaru').css('display', 'block');
                            $('#cetak').attr('data-transaksi', transactionId);
                            $('#voucherCode').attr('disabled', true);

                        }
                    },
                    error: function (xhr) {
                        swal.fire({
                            icon: 'error',
                            title: 'Gagal melakukan pembayaran',
                            text: xhr.responseJSON.message || 'Terjadi kesalahan saat memproses pembayaran.',
                            timer: 1000,
                        });
                    }
                });
            });

            $(document).on('click', '#transaksiBaru', function () {
                location.reload();
            })

            $(document).on('click', '#batal', function () {
                $transactionId = $(this).data('transaksi');
                $.ajax({
                    url: "{{ route('kasir.batal') }}",
                    method: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        transactionId: $transactionId
                    },
                    success: function (response) {
                        location.reload();
                    },
                    error: function (xhr) {
                        swal.fire({
                            icon: 'error',
                            title: 'Gagal membatalkan transaksi',
                            text: xhr.responseJSON.message || 'Terjadi kesalahan saat membatalkan transaksi.',
                            timer: 1000,
                        });
                    }
                });
            })


            function printReceipt(receiptContent) {
                const iframe = document.createElement('iframe');
                iframe.style.position = 'fixed';
                iframe.style.right = '0';
                iframe.style.bottom = '0';
                iframe.style.width = '0';
                iframe.style.height = '0';
                iframe.style.border = '0';

                document.body.appendChild(iframe);

                const doc = iframe.contentWindow.document;
                doc.open();
                doc.write(`
                                                <html>
                                                <head>
                                                    <title>Cetak Struk</title>
                                                    <style>
                                                        @page {
                                                            size: 58mm auto;
                                                            margin: 0;
                                                        }
                                                        body {
                                                            width: 58mm;
                                                            font-family: monospace;
                                                            font-size: 11px;
                                                            white-space: pre;
                                                            margin: 0;
                                                            padding: 4px;
                                                        }
                                                    </style>
                                                </head>
                                                <body>
                                        ${receiptContent}
                                                </body>
                                                </html>
                                            `);
                doc.close();

                iframe.onload = function () {
                    iframe.contentWindow.focus();
                    iframe.contentWindow.print();
                    setTimeout(() => document.body.removeChild(iframe), 1000);
                };
            }


            $(document).on('click', '#cetak', function () {
                const transactionId = $(this).data('transaksi');
                window.open(`/kasir/cetak/${transactionId}`, '_blank');
            });
        });

    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            // hanya di halaman kasir
            if (!document.body.classList.contains('pos-mode')) {
                document.body.classList.add('pos-mode', 'hide-sidebar');
            }

            // paksa fullscreen (Electron pasti tembus)
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen().catch(() => { });
            }

        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {

            const kasirLink = document.getElementById('kasir-trigger');

            if (kasirLink) {
                kasirLink.addEventListener('click', function () {

                    /* simpan state supaya kebawa ke page kasir */
                    sessionStorage.setItem('pos_mode', '1');

                    /* fullscreen (allowed karena klik user) */
                    if (!document.fullscreenElement && document.documentElement.requestFullscreen) {
                        document.documentElement.requestFullscreen().catch(() => { });
                    }
                });
            }

            /* === AKTIF SAAT HALAMAN KASIR TERBUKA === */
            if (sessionStorage.getItem('pos_mode') === '1') {
                document.body.classList.add('hide-sidebar', 'pos-mode');
            }

        });
    </script>



@endsection