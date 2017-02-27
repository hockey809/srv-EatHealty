@extends('index')
@section('content')
<div class="row">
    <div class="col-lg-12">
        <h2 class="page-header">{{Lang::get('pages.foods.list')}}</h2>
    </div>
</div>

<div class="col-md-6">
    <div class="btn-toolbar">        
        {{-- SEARCH --}}
        <input style="margin-bottom: 5px;" id="foodSearch" type="text" placeholder="{{ Lang::get('pages.foods.search') }}..." class="form-control input-small input-inline" />
    </div>
    <div style="max-height: 400px; overflow-y: auto;">
        <table class="table table-condensed">
            <thead>
            <tr>
                <th>Име</th>
                <th>Калории</th>                
            </tr>
            </thead>
            <tbody>
                @foreach($data['foods'] as $key => $food)
                <tr class="rowFilter">
                    <td><a href="/edit_food/{{$food->id}}">{{$food->title}}</a></td>                    
                    <td>@if($food->units == 'grams') {{$food->calories_per_unit + 0}} калории за 100 грама @else {{$food->calories_per_unit + 0}} калории за 1 брой @endif</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@section('scripts')
@parent
<script>
    $(document).ready(function(){
        $("#foodSearch").keyup(function () {
            var query = this.value;
            $('.rowFilter').hide().filter(function () {
                return $(this).has('td:contains("' + query + '")').length > 0;
            }).show();
        }); 
    });

    
</script>
@stop
    
    
    
@stop