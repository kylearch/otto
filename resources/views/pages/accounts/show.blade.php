@extends('layouts.app')

@section('content')
    <div class="box box-info">
        <div class="box-header">
            <h3 class="box-title" style="display: block">
                <img src="{{ $account->logo }}" alt="" height=24>
                {{ $account->name }}

                <span class="pull-right">
                    <strong>{{ money_cell($account->current_balance, true, true) }}</strong>
                </span>
            </h3>
        </div>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Transaction Date</th>
                    <th>Description</th>
                    <th>Tags</th>
                    <th>Amount</th>
                </tr>
                <tr>
                    <th colspan="3" align="right">Balance:</th>
                    <th class="number text-right"></th>
                </tr>
            </thead>
            <tbody>
                @foreach($transactions as $transaction)
                    <tr>
                        <td>{{ $transaction->date->format('Y-m-d') }}</td>
                        <td>{{ str_limit($transaction->name, 60) }}</td>
                        <td>{{ $transaction->tags->pluck('name')->implode(', ') }}</td>
                        <td class="number text-right">{!! money_cell($transaction->amount) !!}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" class="text-center">
                        {{ $transactions->links("pagination::bootstrap-4") }}
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
@endsection
