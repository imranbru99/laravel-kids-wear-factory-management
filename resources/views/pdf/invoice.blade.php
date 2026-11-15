<h1>{{ $invoice->type === 'packing_list' ? 'Packing list' : 'Commercial invoice' }} {{ $invoice->number }}</h1>
<p>{{ $company['name'] }} · {{ $company['name_bn'] }}</p>
<p>{{ $company['address'] }}</p>
<p>Buyer: {{ $invoice->buyer->name }} · {{ $invoice->invoice_date->toDateString() }} · {{ $invoice->currency }}</p>
<table>
    <thead><tr><th>Description</th><th>Sizes</th><th>Qty</th><th>Price</th><th>Amount</th></tr></thead>
    <tbody>
    @foreach ($invoice->items as $item)
        <tr>
            <td>{{ $item->description }}</td>
            <td>{{ $matrixLabel($item->size_matrix ?? []) }}</td>
            <td>{{ $item->quantity }}</td>
            <td>{{ $item->unit_price }}</td>
            <td>{{ $item->amount }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
<p>মোট / Total: {{ $invoice->total }} {{ $invoice->currency }}</p>
<p>{{ $invoice->notes }}</p>
