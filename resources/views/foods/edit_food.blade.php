@extends('index')
@section('content')

<div class="col-md-6" style="margin-top: 20px;">
    <form method="POST"
          enctype="multipart/form-data"
          action="{{ action('FoodController@update',$data['item']->id) }}"
          class="form-horizontal form-bordered form-row-stripped">

        <input type="hidden" name="_method" value="PUT"/>

        {{ csrf_field() }}
        
        <div class="form-body">

            {{-- TITLE --}}
            <div class="form-group">
                <label class="col-md-5 control-label" for="title">
                        Име
                </label>

                <div class="col-md-7">
                    <input type="text"
                           name="title"
                           class="form-control"
                           placeholder="Име на храната"
                           value="{{$data['item']->title}}"/>
                </div>
            </div>                                            

            {{-- UNITS --}}
            <div class="form-group">
                <label class="col-md-5 control-label">
                    Мерна еденица
                </label>
                <div class="col-md-4">
                    <select name="units" class="form-control" id="selectUnits">
                        <option value="abs" @if($data['item']->unis == 'abs') selected @endif>брой</option>
                        <option value="grams" @if($data['item']->unis == 'grams') selected @endif>грама</option>
                    </select>
                </div>
            </div>

            {{-- CALORIES --}}
            <div class="form-group">
                <label class="col-md-5 control-label">
                    <span id="count_type">@if($data['item']->units == 'abs') Калории за 1 брой @else Калории за 100 грама @endif </span>
                </label>

                <div class="col-md-7">
                    <input type="text"
                           name="calories"
                           class="form-control"
                           value="{{$data['item']->calories_per_unit}}"/>
                </div>
            </div>
        </div>
        <button class="btn btn-default btn-primary pull-right">Запази</button>
    </form>
</div>
@section('scripts')
@parent
<script>
    $(document).ready(function(){
        $('#selectUnits').on('change',function(){
            let selected = $('#selectUnits').find(':selected').val();
            if(selected == 'abs') { 
                $('#count_type').html('Калории за 1 брой');
            } else { 
                $('#count_type').html('Калории за 100 грама');
            }
        });
    });
</script>
@stop

@stop