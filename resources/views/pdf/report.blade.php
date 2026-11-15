<h1>{{ $title }}</h1>
<p>{{ $company['name_bn'] }} / {{ $company['name'] }}</p>
<table>
    <thead><tr>@foreach ($table['headers'] as $header)<th>{{ $header }}</th>@endforeach</tr></thead>
    <tbody>
    @foreach ($table['rows'] as $row)
        <tr>@foreach ($row as $cell)<td>{{ $cell }}</td>@endforeach</tr>
    @endforeach
    </tbody>
</table>
