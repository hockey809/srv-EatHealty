@extends('index')
@section('content')
<div class="row">
    <div class="col-lg-12">
        <h2 class="page-header">{{Lang::get('pages.dashboard.title')}}</h2>
    </div>
</div>

<div class="col-md-12">
    @if(Session::has('success'))
    <div class="alert alert-success">
        {{Session::get('success')}}
    </div>
    @endif
    <div class="btn-toolbar">        
        {{-- SEARCH --}}
        <input style="margin-bottom: 5px;" id="clientSearch" type="text" placeholder="{{ Lang::get('pages.dashboard.search_clients') }}..." class="form-control input-small input-inline" />
    </div>
    <div style="max-height: 400px; overflow-y: auto;">
        <table class="table table-condensed" id="customerListTable">
            <thead>
            <tr>
                <th>Име</th>
                <th>Имейл</th>
                <th>Телефон</th>
                <th class="small">Дата раждане</th>
                <th class="small">Дата регистрация</th>
                <th>Действия</th>
            </tr>
            </thead>
            <tbody>
                @foreach($data['customers'] as $key => $value)
                <tr class="rowFilter">
                    <td><a href="/customers/{{$value->id}}">{{$value->first_name . ' ' . $value->last_name}}</a></td>
                    <td>{{$value->email}}</td>
                    <td>{{$value->phone_1}} {{$value->phone_2}}</td>
                    <td>{{Carbon\Carbon::createFromFormat('Y-m-d H:i:s',$value->birthdate)->format('d.m.Y')}}</td>
                    <td>{{Carbon\Carbon::createFromFormat('Y-m-d H:i:s',$value->created_at)->format('d.m.Y')}}</td>                    
                    <td>
                        <a class="btn" data-resource-id="{{$value->id}}" title="Редакция" href="/customers/edit/{{$value->id}}">
                            <i class="fa fa-edit"></i>
                        </a>
                        <a class="btn delete-button" data-resource-id="{{$value->id}}" title="Изтриване" href="#">
                            <i class="fa fa-times"></i>
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- DELETE MODAL --}}
<div class="modal fade" id="delete-modal" tabindex="-1" role="basic" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">Изтриване клиент</h4>
            </div>
            <form action="" method="post">
                <input type="hidden" name="_method" value="DELETE"/>

                {{ csrf_field() }}
                
                <div class="modal-body">
                    Сигурни ли сте, че искате да изтриете този клиент?
                </div>

                <div class="modal-footer">
                    <button id="btnDeleteModal" type="submit" class="btn red">
                        {{ Lang::get('common.buttons.submit') }}
                    </button>
                    <button type="button" class="btn default" data-dismiss="modal">
                        {{ Lang::get('common.buttons.cancel') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@section('scripts')
@parent
<script>
    $(document).ready(function(){
        $("#clientSearch").keyup(function () {
            var query = this.value;
            $('.rowFilter').hide().filter(function () {
                return $(this).has('td:contains("' + query + '")').length > 0;
            }).show();
        }); 
        
        $('#customerListTable').on('click', '.delete-button', function () {
            var id = $(this).data('resourceId'),
                    $deleteModal = $('#delete-modal');

            $deleteModal.find('form').attr('action', '/customers/' + id);
            $deleteModal.modal('show');
        });
    });

    
</script>
@stop
    
    
    
@stop