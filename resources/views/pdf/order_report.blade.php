<!-- resources/views/pdf/order_report.blade.php -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Report</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            margin: 20px;
            color: #333;
            background-color: #f9f9f9;
        }

        h1, h2, h3 {
            color: #3498db;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }

        th {
            background-color: #3498db;
            color: #fff;
        }

        tr:nth-child(even) {
            background-color: #f5f5f5;
        }
    </style>
</head>
<body>
<h1 style="text-align: center; margin-bottom: 20px;">Order Report</h1>

@foreach($orders as $order)
    <div style="background-color: #fff; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); padding: 20px; margin-bottom: 20px; border-radius: 8px;">
        <h2 style="color: #3498db;">Order #{{ $order->id }}</h2>
        <p><strong>User Name:</strong> {{ $order->user }}</p>
        <p><strong>Order Date:</strong> {{ $order->order_date }}</p>
        <p><strong>Status:</strong> {{ $order->status }}</p>
        <p><strong>Payment Status:</strong> {{ $order->payment_status }}</p>

        <h3 style="margin-top: 20px;">Order Items:</h3>
        <table>
            <thead>
            <tr>
                <th>ID</th>
                <th>Medicine</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Total Price</th>
            </tr>
            </thead>
            <tbody>
            @foreach($order['CartItem'] as $item)
                <tr>
                    <td>{{ $item->id }}</td>
                    <td>{{$item->medicine}}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>SYP{{ $item->price }}</td>
                    <td>SYP{{ $item->total_price }}</td>

                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endforeach
</body>
</html>
