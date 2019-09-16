@extends('layouts.app')

@section('content')
    <div class="box">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th colspan="2">Name</th>
                    <th>Date</th>
                    <th colspan="2" width="64">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($documents as $document)
                    <tr>
                        <td><img src="{{ $document->getFirstMediaUrl('file', 'thumb') ?? asset('img/pdf-icon.png') }}" alt="{{ $document->name }}" height="40px"></td>
                        <td style="vertical-align: middle;">{{ $document->name }}</td>
                        <td>{{ $document->created_at->format('Y-m-d') }}</td>
                        <td><a href="{{ route('documents.show', $document) }}" target="_blank"><i class="fa fa-eye"></i></a></td>
                        <td><a href="{{ route('documents.download', $document) }}"><i class="fa fa-download"></i></a></td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="col-lg-12">
            {!! $documents->links() !!}
        </div>
    </div>
@endsection
