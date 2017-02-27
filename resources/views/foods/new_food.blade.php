@extends('index')
@section('content')

<div class="col-md-6" style="margin-top: 20px;">
    <form method="POST"
          enctype="multipart/form-data"
          action="{{ action('FoodController@store') }}"
          class="form-horizontal form-bordered form-row-stripped">

        {{ csrf_field() }}
        
        <div class="form-body">

            {{-- TITLE --}}
            <div class="form-group">
                <label class="col-md-4 control-label" for="title">
                        Име
                </label>

                <div class="col-md-8">
                    <input type="text"
                           name="title"
                           class="form-control"
                           placeholder="Име на храната"
                           value=""/>
                </div>
            </div>                                            

            {{-- UNITS --}}
            <div class="form-group">
                <label class="col-md-4 control-label">
                    Мерна еденица
                </label>
                <div class="col-md-4">
                    <select name="units" class="form-control" id="selectUnits">
                        <option value="abs" selected>брой</option>
                        <option value="grams">грама</option>
                    </select>
                </div>
            </div>

            {{-- CALORIES --}}
            <div class="form-group">
                <label class="col-md-4 control-label">
                    <span id="count_type">Калории за 1 брой</span>
                </label>

                <div class="col-md-8">
                    <input type="text"
                           name="calories"
                           class="form-control"
                           value=""/>
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
   