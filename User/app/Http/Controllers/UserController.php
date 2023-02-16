<?php

namespace App\Http\Controllers;

use App\Exports\InvoicesExport;
use App\Models\Account;
use App\Models\Cost;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use PDF;

class UserController extends Controller
{
    public function costManage(Request $request)
    {
        $user = Auth::user();
        return view('client-cost-manage', compact('user'));
    }

    public function tableCost(Request $request)
    {
        $user_id = $request->id;
        $data = Cost::with('user', 'shop')->where('user_id', $user_id)->orderBy('created_at', 'desc')->get();

        return view('client-cost-table', compact('data'));
    }

    public function editCost($id)
    {
        $data = Cost::with('user', 'shop')->find($id);
        return view('client-cost-edit', compact('data'));
    }

    public function deleteCost(Request $request)
    {
        $id = $request->id;
        Cost::where('id', $id)->delete();
        return response()->json(['status' => true]);
    }

    public function costExportExcel(Request $request)
    {
        $user_id = $request->id;
        $data = Cost::with('user', 'shop')->where('user_id', $user_id)->orderBy('created_at', 'desc')->get();

        $arr = array();
        foreach ($data as $index => $datum) {
            $tmp = [];
            $tmp['shop_name'] = $datum->shop->shop_name;
            $tmp['total'] = $datum->total;
            $tmp['pay_date'] = $datum->pay_date;
            $tmp['content'] = $datum->content;
            $tmp['note'] = $datum->note;
            array_push($arr, $tmp);
        }
        $data = $arr;

        $array[] = array('NO', __('shop-name'), __('total'), __('pay-date'), __('content'), __('note'));
        foreach ($data as $index => $item) {
            $array[] = array(
                'NO' => $index + 1,
                __('shop-name') => $item['shop_name'],
                __('total') => $item['total'],
                __('pay-date') => $item['pay_date'],
                __('content') => $item['content'],
                __('note') => $item['note']
            );
        }
        $export = new InvoicesExport([
            $array
        ]);

        return Excel::download($export, '経費一覧.xlsx');
    }

    public function costExportPDF($id)
    {
        // retreive all records from db
        $user_id = $id;
        $data = Cost::with('user', 'shop')->where('user_id', $user_id)->orderBy('created_at', 'desc')->get()->toArray();
        // share data to view
        //view()->share('employee',$data);
        $data = [
            'data' => $data
        ];
        $pdf = PDF::loadView('cost-table-pdf', $data);
        // download PDF file with download method
        return $pdf->download('経費一覧.pdf');
    }

    public function manageAccount()
    {
        return view('account-manage');
    }

    public function tableAccount(Request $request)
    {
        $status = $request->status;
        $contact = $request->contact;
        $type = $request->type;
        $user_id = Auth::user()->id;
        if (isset($status)) {
            if (isset($type)) {
                $data = Account::where('user_id', $user_id)->where('status', $status)->where('type', $type)->where('contact', 'like', '%' . $contact . '%')->orderBy('created_at', 'desc')->get();
            } else {
                $data = Account::where('user_id', $user_id)->where('status', $status)->where('contact', 'like', '%' . $contact . '%')->orderBy('created_at', 'desc')->get();
            }
        } else {
            if (isset($type)) {
                $data = Account::where('user_id', $user_id)->where('type', $type)->where('contact', 'like', '%' . $contact . '%')->orderBy('created_at', 'desc')->get();
            } else {
                $data = Account::where('user_id', $user_id)->where('contact', 'like', '%' . $contact . '%')->orderBy('created_at', 'desc')->get();
            }
        }

        return view('account-table', compact('data'));
    }

    public function addAccount()
    {
        return view('account-add');
    }

    public function editAccount($id)
    {
        $account = Account::find($id);
        return view('account-add', compact('account'));
    }

    public function saveAccount(Request $request)
    {
        $id = $request->id;
        if (!isset($id)) {
            $data = [
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'post_code' => $request->post_code,
                'address' => $request->address,
                'contact' => $request->contact,
                'charge' => $request->charge,
                'status' => $request->status,
                'remarks' => $request->remarks,
                'type' => $request->type,
                'represent' => $request->represent,
                'user_id' => Auth::user()->id
            ];
            Account::create($data);
        } else {
            if (isset($request->password)) {
                $data = [
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'post_code' => $request->post_code,
                    'address' => $request->address,
                    'contact' => $request->contact,
                    'charge' => $request->charge,
                    'status' => $request->status,
                    'remarks' => $request->remarks,
                    'type' => $request->type,
                    'represent' => $request->represent
                ];
            } else {
                $data = [
                    'user_code' => $request->user_code,
                    'name' => $request->name,
                    'email' => $request->email,
                    'post_code' => $request->post_code,
                    'address' => $request->address,
                    'contact' => $request->contact,
                    'charge' => $request->charge,
                    'status' => $request->status,
                    'remarks' => $request->remarks,
                    'type' => $request->type,
                    'represent' => $request->represent
                ];
            }
            Account::find($id)->update($data);
        }
        return response()->json(['status' => true]);
    }

    public function deleteAccount(Request $request)
    {
        $id = $request->id;
        Account::where('id', $id)->delete();
        return response()->json(['status' => true]);
    }

    public function myPage()
    {
        $user = Auth::user();
        return view('my-page', compact('user'));
    }

    public function editInfo(Request $request)
    {
        $id = Auth::user()->id;
        if (isset($request->password)) {
            $data = [
                'name' => $request->name,
                'password' => Hash::make($request->password),
                'post_code' => $request->post_code,
                'address' => $request->address,
                'contact' => $request->contact,
                'charge' => $request->charge,
                'remarks' => $request->remarks,
                'plan_id' => $request->plan,
                'represent' => $request->represent,
                'type' => $request->type
            ];
        } else {
            $data = [
                'name' => $request->name,
                'post_code' => $request->post_code,
                'address' => $request->address,
                'contact' => $request->contact,
                'charge' => $request->charge,
                'remarks' => $request->remarks,
                'plan_id' => $request->plan,
                'represent' => $request->represent,
                'type' => $request->type
            ];
        }
        User::find($id)->update($data);

        return response()->json(['status' => true]);
    }
}