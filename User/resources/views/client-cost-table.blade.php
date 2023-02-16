<table class="table table-separate table-head-custom table-checkable" id="table">
    <thead>
    <tr>
        <th class="text-center">No</th>
        <th class="text-center">{{__('icon')}}</th>
        <th class="text-center">{{__('summary')}}</th>
        <th class="text-center">{{__('account-item')}}</th>
        <th class="text-center">{{__('amount')}}</th>
        <th class="text-center">{{__('tax')}}</th>
        <th class="text-center">{{__('import-date')}}</th>
        <th class="text-center"></th>
    </tr>
    </thead>
    <tbody>

    @foreach($data as $index => $item)
        <tr style="">
            <td class="p-0 border text-end align-middle px-1">{{$index+1}}</td>
            <td class="p-0 border text-center align-middle px-1"></td>
            <td class="p-0 border text-left align-middle px-1">{{!empty($item['shop_id']) ? $item['shop']['shop_name'] : ""}}</td>
            <td class="p-0 border text-center align-middle px-1"></td>
            <td class="p-0 border text-end align-middle px-1">{{number_format($item['total'])}}円</td>
            <td class="p-0 border text-center align-middle px-1">{{$item['percent']}}％</td>
            <td class="p-0 border text-left align-middle px-1">{{date('Y/m/d', strtotime($item['created_at']))}}</td>
            <td class="p-0 border text-center align-middle">
                <input type="hidden" value="{{$item['id']}}">
                <a href="{{route('client.cost-edit', $item['id'])}}" class="btn btn-outline-dark waves-effect ex_change" style="padding: 8px; margin: 5px;">{{__('detail')}}</a>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>