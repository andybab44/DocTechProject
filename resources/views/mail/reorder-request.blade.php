<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
    body  { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #f8f7f4; margin: 0; padding: 24px; color: #1c1917; }
    .wrap { max-width: 620px; margin: 0 auto; background: #fff; border-radius: 12px; overflow: hidden; border: 1px solid #e7e5e4; }
    .hdr  { background: #0f766e; padding: 24px 32px; color: #fff; }
    .hdr h1 { margin: 0; font-size: 20px; font-weight: 600; }
    .hdr p  { margin: 4px 0 0; font-size: 13px; opacity: .8; }
    .body { padding: 28px 32px; }
    .meta { margin-bottom: 20px; font-size: 13px; color: #57534e; }
    .meta strong { color: #1c1917; }
    table { width: 100%; border-collapse: collapse; font-size: 13px; }
    th    { text-align: left; padding: 8px 10px; background: #f5f5f4; color: #78716c; text-transform: uppercase; font-size: 11px; letter-spacing: .05em; border-bottom: 1px solid #e7e5e4; }
    td    { padding: 10px; border-bottom: 1px solid #f5f5f4; color: #292524; }
    tr:last-child td { border-bottom: none; }
    .foot { padding: 16px 32px; background: #fafaf9; border-top: 1px solid #e7e5e4; font-size: 12px; color: #78716c; }
</style>
</head>
<body>
<div class="wrap">
    <div class="hdr">
        <h1>Purchase Order — {{ $reference }}</h1>
        <p>{{ now()->format('d F Y') }}</p>
    </div>
    <div class="body">
        <div class="meta">
            <p><strong>To:</strong> {{ $vendor->name }} &lt;{{ $vendor->email }}&gt;</p>
            @if ($vendor->phone)
            <p><strong>Phone:</strong> {{ $vendor->phone }}</p>
            @endif
            <p style="margin-top:12px"><strong>Reference:</strong> {{ $reference }}</p>
        </div>

        <p style="font-size:14px;margin-bottom:16px;">
            Please supply the following items at your earliest convenience.
        </p>

        <table>
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Category</th>
                    <th>Current Stock</th>
                    <th>Requested Qty</th>
                    <th>Unit</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($lines as $line)
                <tr>
                    <td><strong>{{ $line['item']->name }}</strong></td>
                    <td>{{ $line['item']->category ?? '—' }}</td>
                    <td style="color: {{ $line['item']->isLowStock() ? '#dc2626' : '#57534e' }}">{{ $line['item']->quantity }}</td>
                    <td><strong>{{ $line['quantity'] }}</strong></td>
                    <td>{{ $line['item']->unit }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="foot">
        This is an automated reorder request from {{ config('app.name') }}.
        Please reply to this email to confirm receipt or contact us with any queries.
    </div>
</div>
</body>
</html>
