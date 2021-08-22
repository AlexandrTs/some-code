<body>// изменение суммы заказа

@foreach ($data as $request)
    <b>{{ $request['header'] }}</b><br><br>
    {!! $request['content'] !!}
    <hr>
@endforeach
</body>
