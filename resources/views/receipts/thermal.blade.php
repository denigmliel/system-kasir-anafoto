<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Struk - {{ $transaction->code }}</title>
    <style>
        @page {
            size: 80mm auto;
            margin: 0;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            line-height: 1.4;
            padding: 10px;
            width: 80mm;
        }

        .receipt {
            width: 100%;
        }

        .header {
            text-align: center;
            margin-bottom: 10px;
            border-bottom: 2px dashed #000;
            padding-bottom: 10px;
        }

        .store-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .store-info {
            font-size: 10px;
        }

        .transaction-info {
            margin: 10px 0;
            font-size: 11px;
            border-bottom: 1px dashed #000;
            padding-bottom: 10px;
        }

        .items {
            margin: 10px 0;
        }

        .item {
            margin-bottom: 8px;
        }

        .item-name {
            font-weight: bold;
        }

        .item-details {
            font-size: 11px;
            padding-left: 5px;
        }

        .totals {
            border-top: 1px dashed #000;
            padding-top: 10px;
            margin-top: 10px;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            margin: 3px 0;
        }

        .grand-total {
            font-size: 14px;
            font-weight: bold;
            border-top: 2px solid #000;
            border-bottom: 2px solid #000;
            padding: 5px 0;
            margin: 5px 0;
        }

        .payment {
            margin-top: 10px;
            border-top: 1px dashed #000;
            padding-top: 10px;
        }

        .account-info {
            font-size: 11px;
            margin: 6px 0;
            padding-left: 5px;
            line-height: 1.3;
        }

        .footer {
            text-align: center;
            margin-top: 15px;
            padding-top: 10px;
            border-top: 2px dashed #000;
            font-size: 11px;
        }

        @media print {
            body {
                width: 80mm;
            }

            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="receipt">
        <div class="header">
            <div class="store-name">{{ $storeName }}</div>
            <div class="store-info">
                <strong>{{ $storeAddress }}</strong><br>
                <strong>Telp: {{ $storePhone }}</strong>
            </div>
        </div>

        <div class="transaction-info">
            <div><strong>No. Transaksi: {{ $transaction->code }}</strong></div>
            <div><strong>Tanggal: {{ $transaction->transaction_date->timezone('Asia/Jakarta')->format('d/m/Y H:i:s') }}</strong></div>
        </div>

        <div class="items">
            @foreach($transaction->details as $detail)
            <div class="item">
                <div class="item-name">{{ $detail->product_name }}</div>
                <div class="item-details">
                    <strong>{{ $detail->quantity }}{{ $detail->unit ? ' ' . $detail->unit : '' }}</strong>
                    x <strong>Rp{{ number_format($detail->price, 0, ',', '.') }}</strong> =
                    <strong>Rp{{ number_format($detail->subtotal, 0, ',', '.') }}</strong>
                </div>
            </div>
            @endforeach
        </div>

        <div class="totals">
            <div class="total-row grand-total">
                <span>TOTAL:</span>
                <span>Rp{{ number_format($transaction->total_amount, 0, ',', '.') }}</span>
            </div>
        </div>

        @php
            $showBankAccount = in_array(strtolower($transaction->payment_method), ['transfer', 'rekening'], true);
        @endphp

        <div class="payment">
            <div class="total-row">
                <span><strong>Bayar ({{ ucfirst($transaction->payment_method) }}):</strong></span>
                <span><strong>Rp{{ number_format($transaction->payment_amount, 0, ',', '.') }}</strong></span>
            </div>
            @if ($showBankAccount)
                <div class="account-info">
                    <div><strong>Rekening:</strong></div>
                    <div><strong>Jhonny Situmorang</strong></div>
                    <div><strong>723801007592539 (BRI)</strong></div>
                </div>
            @endif
            <div class="total-row">
                <span><strong>Kembali:</strong></span>
                <span><strong>Rp{{ number_format($transaction->change_amount, 0, ',', '.') }}</strong></span>
            </div>
        </div>

        <div class="footer">
            <strong>{{ $receiptFooter }}</strong><br>
            <strong>Silahkan datang kembali 😊</strong>
        </div>
    </div>

    <div class="no-print" style="margin-top: 20px; text-align: center;">
        <button onclick="window.print()" style="padding: 10px 20px; font-size: 14px; cursor: pointer;">
            PRINT STRUK
        </button>
        <button onclick="window.close()" style="padding: 10px 20px; font-size: 14px; cursor: pointer; margin-left: 10px;">
            TUTUP
        </button>
    </div>

    <script>
        (function () {
            function triggerAutoPrint() {
                setTimeout(function () {
                    window.focus();
                    window.print();
                }, 100);

                function closeAfterPrint() {
                    setTimeout(function () {
                        window.close();
                    }, 300);
                }

                if ('onafterprint' in window) {
                    window.addEventListener('afterprint', closeAfterPrint, { once: true });
                } else {
                    setTimeout(closeAfterPrint, 1500);
                }
            }

            window.addEventListener('load', function () {
                var params = new URLSearchParams(window.location.search);
                if (params.has('auto_print')) {
                    triggerAutoPrint();
                }
            });
        })();
    </script>
</body>
</html>
