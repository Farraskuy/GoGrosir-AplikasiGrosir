<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Struk | {{ nama_aplikasi() }}</title>
    <style>
        body {
            font-size: 11px;
            font-family: helvetica;
        }

        table {
            font-size: 11px;
        }

        .container {
            max-width: 155px;
        }

        header {
            text-align: center;
            margin: auto;
        }

        footer {
            width: 100%;
            text-align: center;
        }

        hr {
            margin: 10px 0;
            padding: 0;
            height: 1px;
            border: 0;
            border-bottom: 1px solid rgb(49, 49, 49);
            width: 100%;

        }

        .nama-item {
            font-weight: bold;
        }

        .harga-item {
            display: flex;
            justify-content: flex-end;
            margin: 0;
            padding: 0;
        }


        table {
            border-collapse: collapse;
        }

        table td {
            border: 0;
        }

        .text-right {
            text-align: right;
        }

        .text-bold {
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="container">
        <header>
            <strong style="margin-bottom: 0">Toko Grosir Zafira</strong>
            <p style="margin: 0">Terminal Parompong, Jl. Kolonel Masturi</p>
            <p style="margin: 0">No:{{ $data->no_trans }}</p>
            <hr>
            <table>
                <tr>
                    <td style="text-align: left">Tanggal</td>
                    <td style="padding: 2px 0"> : </td>
                    <td style="text-align: left">{{ $data->created_at }}</td>
                </tr>
                <tr>
                    <td style="text-align: left">Kasir</td>
                    <td style="padding: 0 2px"> : </td>
                    <td style="text-align: left">{{ $data->user->nama }}</td>
                </tr>
            </table>
        </header>
        <hr>
        <main style="width: 100%">
            <table style="width: 100%">
                <tbody>
                    @foreach ($data->detailPenjualan as $item)
                        <tr>
                            <td colspan="4"><span class="nama-item">{{ $item->barang->nama }}</span></td>
                        </tr>
                        <tr class="text-right">
                            <td>{{ number_format($item->harga_jual, 0, ',', '.') }}</td>
                            <td style="width:1px;padding-left:5px">x</td>
                            <td style="width:5px;padding-left:5px">{{ number_format($item->jumlah_beli, 0, ',', '.') }}</td>
                            <td style="width:50px;padding-left:10px">{{ number_format($item->harga_jual * $item->jumlah_beli, 0, ',', '.') }}</td>
                        </tr>
                        
                        @if ($item->potongan > 0)
                            <tr class="text-right">
                                <td colspan="3">Potongan</td>
                                <td style="width:50px;padding-left:10px">{{ number_format($item->potongan, 0, ',', '.') }}</td>
                            </tr>
                        @endif
                    @endforeach
                    <tr>
                        <td colspan="4">
                            <hr>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3">Sub Total</td>
                        <td class="text-right">{{ number_format($data->total_bayar, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td colspan="3">Total Potongan</td>
                        <td class="text-right">{{ number_format($data->total_potongan, 0, ',', '.') }}</td>
                    </tr>
                    <tr class="text-bold">
                        <td colspan="3">Total</td>
                        <td class="text-right">{{ number_format($data->total_bayar - $data->total_potongan, 0, ',', '.') }}</td>
                    </tr>
                    <tr class="text-bold">
                        <td colspan="3">Dibayar</td>
                        <td class="text-right">{{ number_format($data->bayar, 0, ',', '.') }}</td>
                    </tr>
                    <tr class="text-bold">
                        <td colspan="3">Kembali</td>
                        <td class="text-right">{{ number_format($data->kembalian, 0, ',', '.') }}</td>
                    </tr>
                </tbody>
            </table>
        </main>
        <hr>
        <footer>
            Terima kasih telah berbelanja di Toko kami, kepuasan anda adalah tujuan kami. <br> ♥ ♥ ♥
        </footer>
    </div>
</body>

</html>
