@extends ('layouts.app')

@section('content')
    <form action="{{ route('documents.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="file" name="document">
        <input type="submit">
    </form>
@endsection
