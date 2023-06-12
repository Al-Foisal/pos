<?php
namespace App;
use App\Models\TransactionHistory;

class TransactionStrategy {
    public static function strategy() {
        $data = [];

        $total_transaction_history = TransactionHistory::where('user_id', auth()->user()->user_id)
            ->with(
                'order',
                'purchase',
                'income_fc',
                'expense_ts',
                'returnOrder',
                'returnPurchase'
            )->get();

        $total_income_sell              = 0;
        $total_income_purchase_discount = 0;
        $total_income_purchase_return   = 0;
        $total_income_others_income     = 0;
        $total_sell_return_income       = 0;

        $total_expense_purchase       = 0;
        $total_expense_purchase_vat   = 0;
        $total_expense_sell_return    = 0;
        $total_expense_other_expenses = 0;

        foreach ($total_transaction_history as $transaction) {

            if ($transaction->order) {
                $total_income_sell += $transaction->order->total;

                foreach ($transaction->order->orderDetails as $orderDetails) {
                    $total_expense_purchase += ($orderDetails->quantity * $orderDetails->buying_price);
                }

            } elseif ($transaction->income_fc) {

                if ($transaction->income_fc->income_id == 12) {
                    $total_income_others_income += 0;
                } else {
                    $total_income_others_income += $transaction->income_fc->amount;
                }

            } elseif ($transaction->purchase) {
                $total_income_purchase_discount += $transaction->purchase->discount_price;
                $total_expense_purchase_vat += $transaction->purchase->vat_price;

            } elseif ($transaction->expense_ts) {

                if ($transaction->expense_ts->expense_id == 26) {
                    $total_expense_other_expenses += 0;
                } else {
                    $total_expense_other_expenses += $transaction->expense_ts->amount;
                }

            } elseif ($transaction->returnPurchase) {
                $total_income_purchase_return += $transaction->returnPurchase->total;
            } elseif ($transaction->returnOrder) {
                $total_expense_sell_return += $transaction->returnOrder->total;

                foreach ($transaction->returnOrder->orderDetails as $details) {

                    $total_sell_return_income += ($details->quantity * $details->buying_price);

                }

            }

        }

        //total income
        $data['total_income_sell']              = $total_income_sell;
        $data['total_income_purchase_discount'] = $total_income_purchase_discount;
        $data['total_income_purchase_return']   = $total_income_purchase_return;
        $data['total_income_others_income']     = $total_income_others_income;
        $data['total_sell_return_income']       = $total_sell_return_income;

        //total expense
        $data['total_expense_purchase']       = $total_expense_purchase;
        $data['total_expense_purchase_vat']   = $total_expense_purchase_vat;
        $data['total_expense_sell_return']    = $total_expense_sell_return;
        $data['total_expense_other_expenses'] = $total_expense_other_expenses;

        //daily

        $daily_transaction_history = TransactionHistory::where('user_id', auth()->user()->user_id)
            ->whereDate('created_at', date('Y-m-d'))
            ->with(
                'order',
                'purchase',
                'income_fc',
                'expense_ts',
                'returnOrder',
                'returnPurchase'
            )->get();

        $daily_income_sell              = 0;
        $daily_income_purchase_discount = 0;
        $daily_income_purchase_return   = 0;
        $daily_income_others_income     = 0;
        $daily_sell_return_income       = 0;

        $daily_expense_purchase       = 0;
        $daily_expense_purchase_vat   = 0;
        $daily_expense_sell_return    = 0;
        $daily_expense_other_expenses = 0;

        foreach ($daily_transaction_history as $transaction) {

            if ($transaction->order) {
                $daily_income_sell += $transaction->order->total;

                foreach ($transaction->order->orderDetails as $orderDetails) {
                    $daily_expense_purchase += ($orderDetails->quantity * $orderDetails->buying_price);
                }

            } elseif ($transaction->income_fc) {

                if ($transaction->income_fc->income_id == 12) {
                    $daily_income_others_income += 0;
                } else {
                    $daily_income_others_income += $transaction->income_fc->amount;
                }

            } elseif ($transaction->purchase) {
                $daily_income_purchase_discount += $transaction->purchase->discount_price;
                $daily_expense_purchase_vat += $transaction->purchase->vat_price;

            } elseif ($transaction->expense_ts) {

                if ($transaction->expense_ts->expense_id == 26) {
                    $daily_expense_other_expenses += 0;
                } else {
                    $daily_expense_other_expenses += $transaction->expense_ts->amount;
                }

            } elseif ($transaction->returnPurchase) {
                $daily_income_purchase_return += $transaction->returnPurchase->total;
            } elseif ($transaction->returnOrder) {
                $daily_expense_sell_return += $transaction->returnOrder->total;

                foreach ($transaction->returnOrder->orderDetails as $details) {

                    $daily_sell_return_income += ($details->quantity * $details->buying_price);

                }

            }

        }

        //daily income
        $data['daily_income_sell']              = $daily_income_sell;
        $data['daily_income_purchase_discount'] = $daily_income_purchase_discount;
        $data['daily_income_purchase_return']   = $daily_income_purchase_return;
        $data['daily_income_others_income']     = $daily_income_others_income;
        $data['daily_sell_return_income']       = $daily_sell_return_income;

        //daily expense
        $data['daily_expense_purchase']       = $daily_expense_purchase;
        $data['daily_expense_purchase_vat']   = $daily_expense_purchase_vat;
        $data['daily_expense_sell_return']    = $daily_expense_sell_return;
        $data['daily_expense_other_expenses'] = $daily_expense_other_expenses;

        return $data;
    }

}
