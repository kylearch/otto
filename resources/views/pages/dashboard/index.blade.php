@extends('layouts.app')

@section('content')
    <div class="box box-info">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Bank</th>
                    <th>Account Name</th>
                    <th>Account Number</th>
                    <th class="text-right">Recent Transactions</th>
                    <th class="text-right">Current Balance</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($accounts as $account)
                    <tr>
                        <td><img src="{{ $account->logo }}" alt="" height="24"></td>
                        <td>
                            <a href="{{ route('accounts.show', $account) }}">{{ $account->name }}</a></td>
                        <td>{{ $account->mask }}</td>
                        <td class="text-right">{{ money_cell($account->transactions()->today()->sum('amount')) }}</td>
                        <td class="text-right">{{ money_cell($account->current_balance) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="box box-info">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th width="5%">Date</th>
                    <th>Account</th>
                    <th width="95%">Description</th>
                    <th class="text-right">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transactions as $transaction)
                    <tr>
                        <td>{{ $transaction->date->format('Y-m-d') }}</td>
                        <td><img src="{{ $transaction->account->logo }}" alt="" height="24"></td>
                        <td>{{ str_limit($transaction->name, 60) }}</td>
                        <td class="number text-right">{!! money_cell($transaction->amount) !!}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" class="no-padding text-center">
                        {{ $transactions->links() }}
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
@endsection
