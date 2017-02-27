@extends('layouts.app')

@section('content')
<div id="activationModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">{{Lang::get('pages.register.activation_form')}}</h4>
        </div>
        <div class="modal-body">
            <form class="form-horizontal" role="form" id="activation_form" method="POST" action="/register/sendactivation">
            <div class="alert alert-danger" style="display: none">
                {{Lang::get('pages.register.unexpected_error')}}
            </div>
            {!! csrf_field() !!}
            <div class="form-group">
                <label class="col-md-3 control-label">{{Lang::get('pages.register.select_method')}}</label>
                <div class="col-md-4">
                    <select class="form-control" name="select_method">
                        <option value="email">{{Lang::get('pages.register.email')}}</option>
                        <option value="sms">{{Lang::get('pages.register.phone')}}</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <input class="form-control" name="activation_value" style="display: inblock">
                </div>
            </div>
            <div class="alert alert-success" style="display: none">
                
            </div>
        </div>
        
        <div class="modal-footer">
            <button id="activationCodeSubmit" type="button" class="btn btn-primary"><i class="fa fa-btn fa fa-paper-plane-o"></i>{{Lang::get('pages.register.send_activation')}}</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">{{Lang::get('pages.register.close')}}</button>
        </div>
        </form>
    </div>

  </div>
</div>
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">{{Lang::get('pages.register.activate_title')}}</div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="{{ url('/activate/activate') }}">
                        {!! csrf_field() !!}

                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            <label class="col-md-4 control-label">{{Lang::get('pages.register.email')}}</label>
                            <div class="col-md-6">
                                <input type="email" class="form-control" name="email" value="{{ old('email') }}">
                                @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                            <label class="col-md-4 control-label">{{Lang::get('pages.register.password')}}</label>
                            <div class="col-md-6">
                                <input type="password" class="form-control" name="password">

                                @if ($errors->has('password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                            <label class="col-md-4 control-label">{{Lang::get('pages.register.password_confirm')}}</label>
                            <div class="col-md-6">
                                <input type="password" class="form-control" name="password_confirmation">

                                @if ($errors->has('password_confirmation'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password_confirmation') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('activation_code') ? ' has-error' : '' }}">
                            <label class="col-md-4 control-label">{{Lang::get('pages.register.activation_code')}}</label>
                            <div class="col-md-6">
                                <input type="text" class="form-control" name="activation_code" value="{{$code}}">

                                @if ($errors->has('activation_code'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('activation_code') }}</strong>
                                    </span>
                                @endif
                                <span class="help-block">
                                    <strong>{{Lang::get('pages.register.click')}} <a href="" data-toggle="modal" data-target="#activationModal">{{Lang::get('pages.register.here')}}</a> {{Lang::get('pages.register.for_activation_code')}}</strong>
                                </span>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-btn fa-user"></i>{{Lang::get('pages.register.register')}}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@section('scripts')
@parent
<script>
    $('#activationCodeSubmit').on('click', function(e){
        e.preventDefault();
        
        if($('#activation_form').find('input[name=activation_value]').val() == '') { 
            $('#activation_form').find('.alert.alert-danger').text('Please enter value').show().delay(5000).fadeOut();
            return;
        }
        
        var $form = $('#activation_form');
        
        sendData = { 
            'method': $form.find('select[name=select_method]').val(),
            'value': $form.find('input[name=activation_value]').val(),
            '_token': $form.find('input[name=_token]').val()
        };
        
        
        $.ajax({
            url : $form.attr('action'),
            method : 'POST',
            data : sendData,
            error : function () {
                $form.find('.alert.alert-danger').show().delay(5000).fadeOut();
            },
            success : function(response) {
                if(response.status == false) { 
                    $form.find('.alert.alert-danger').text(response.msg).show().delay(5000).fadeOut();
                } else if(response.status === true) {
                    $form.find('.alert.alert-success').text(response.msg).show().delay(10000).fadeOut();;
                }
            }
        });
    });

</script>
@stop
@endsection
