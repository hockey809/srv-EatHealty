@extends('index')

@section('content')
<div class="page-container">
    <div class="page-content-wrapper">
        <div class="page-content">
            <div class="row">
                <div class="col-md-12">
                    <div class="portlet light">
                        <div class="portlet-body">
                            <div class="table-container">
                                <div class="row">
                                    {{-- client info area --}}
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-5 profile-info">
                                                <div class="inline-headers">
                                                    <h4 style="display: inline-block">
                                                        {{ $data['item']->first_name . ' ' .
                                                        $data['item']->middle_name . ' ' .
                                                        $data['item']->last_name}} 
                                                    </h4> 
                                                </div>

                                                <ul class="list-inline">                                                    
                                                    <li>
                                                        @if (!empty($data['item']->phone_1))
                                                        <i class="fa fa-phone"></i>
                                                        {{ $data['item']->phone_1 }}
                                                        @endif
                                                        @if (!empty($data['item']->phone_2))
                                                            | {{ $data['item']->phone_2 }}
                                                        @endif
                                                        @if (!empty($data['item']->email) && !preg_match('/@example.com$/',$data['item']->email))
                                                        <i class="fa fa-envelope"></i>
                                                        {{ $data['item']->email }}
                                                        @endif
                                                        <i class="fa fa-home"></i>
                                                        {{ App\TextFormat::fromDbToDisplayDate($data['item']->created_at) }}
                                                    </li>
                                                </ul>
                                            </div>
                                            <div class="form-group">
                                                <div class="col-md-2">
                                                    <select class="form-control" id="select_food">
                                                    @foreach($data['foods'] as $key => $food)
                                                        <option data-food-id="{{$food->id}}" data-units="{{$food->units}}" value="{{$food->id}}">{{$food->title}}</option>
                                                    @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-1">
                                                    <input class="form-control" id="foodQuantity" style="max-width: 100px" type="number" value=""/>  
                                                   
                                                </div>
                                                <div class="col-md-1 pull-left">
                                                    <span id="foodUnits" class="help-inline"></span>
                                                </div>
                                                <div class="col-md-2">
                                                    
                                                    <button class="btn btn-danger" id="addFoodToCustomer">Добави</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- end client info area --}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="portlet light">
                        <div class="portlet-body">
                            <div class="tabbable-line">
                                <ul class="nav nav-tabs">
                                    <li class="active">
                                        <a href="#tab-foods" data-toggle="tab">Списък храни</a>
                                    </li>
                                </ul>
                                <div class="tab-content">
                                    {{-- TAB FOODS --}}
                                    <div class="tab-pane active" id="tab-foods">
                                        <div class="table-container">
                                            <table class="table table-bordered table-hover" id="foods-list-table">
                                                <thead>
                                                <tr role="row" class="heading">
                                                    <th width="35%">Храна</th>
                                                    <th>Количество</th>
                                                    <th>Калории</th>
                                                    <th>Дата</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @foreach($data['item']->foods()->get() as $key => $food)
                                                <tr>                                                    
                                                    <td>{{$food->title}}</td>
                                                    <td>{{$food->amount}} @if($food->units == 'abs') броя @else грама @endif </td>
                                                    <td>{{$food->calories}}</td>
                                                    <td>{{Carbon\Carbon::createFromFormat('Y-m-d H:i:s',$food->created_at)->format('d.m.Y H:i')}}</td>
                                                </tr>
                                                @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>                
            </div>
        </div>
    </div>
</div> 
@endsection

@section('scripts')
@parent
<script>
    $('#select_food').on('change',function(){
        if($(this).find(':selected').data('units') == 'grams') { 
            $('#foodUnits').html('грама');
        } else { 
            $('#foodUnits').html('броя');
        }
    })
    
    $('#select_food').change();
    
    $('#addFoodToCustomer').on('click',function(){
        var food_id = $('#select_food').find(':selected').data('foodId');
        var quantity = $('#foodQuantity').val();
        var data = {'food_id': food_id, 'quantity': quantity, 'user_id': '{{$data["item"]->id}}', '_token': '{{csrf_token()}}' };
        $.ajax({
            type: 'POST',
            cache: false,
            data: data,
            url: '/food/add_to_customer',
            success: function (response) {
                location.reload()
            }
        });
    })
</script>
@stop
    