<h1>Bundle {{ $bundle->code }}</h1>
<p>{{ $company['name'] }}</p>
<p>Order {{ $bundle->productionOrder?->number }}</p>
<p>Style {{ $bundle->style?->style_code }} {{ $bundle->style?->name }}</p>
<p>Size {{ $bundle->size_code }} · {{ $bundle->quantity }} pcs</p>
<p>Stage {{ $bundle->current_stage }}</p>
<p>Track: {{ route('track', $bundle->code) }}</p>
