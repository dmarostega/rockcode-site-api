<article class="card">
    <h2>{{ $title }}</h2>

    @if ($rows->isEmpty())
        <p class="muted">{{ $empty }}</p>
    @else
        <table>
            <thead>
            <tr>
                <th>Item</th>
                <th>Total</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($rows as $row)
                <tr>
                    <td>{{ $row->label }}</td>
                    <td>{{ number_format($row->total, 0, ',', '.') }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endif
</article>
