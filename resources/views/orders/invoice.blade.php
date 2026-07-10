<!DOCTYPE html>
<html>

<head>

    <style>
        body {
            font-family: DejaVu Sans;
            font-size: 14px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table,
        th,
        td {
            border: 1px solid #ddd;
        }

        th,
        td {
            padding: 10px;
        }

        h2 {
            text-align: center;
        }
    </style>

</head>

<body>

    <h2>INVOICE</h2>

    <hr>

    <p>

        <strong>Invoice No :</strong>

        {{ $order->order_number }}

    </p>

    <p>

        <strong>Date :</strong>

        {{ $order->created_at->format('d M Y') }}

    </p>

    <hr>

    <h3>Customer</h3>

    <p>

        {{ $order->user->name }}

        <br>

        {{ $order->user->email }}

    </p>

    <table>

        <tr>

            <th>Description</th>

            <th>Status</th>

            <th>Total</th>

        </tr>

        <tr>

            <td>Customer Order</td>

            <td>{{ ucfirst($order->status) }}</td>

            <td>${{ number_format($order->total_amount,2) }}</td>

        </tr>

    </table>

    <br>

    <h3 style="text-align:right">

        Grand Total :

        ${{ number_format($order->total_amount,2) }}

    </h3>

    <br><br>

    <p align="center">

        Thank you for shopping with us.

    </p>

</body>

</html>