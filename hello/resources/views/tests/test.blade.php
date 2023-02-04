Hello, Laravel!

<hr/>

<ul>
@foreach($values as $value)
  <li>ID: {{ $value->id }}</li>
  <li>Text: {{ $value->text }}</li>
@endforeach
</ul>
